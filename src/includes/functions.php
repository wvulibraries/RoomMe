<?php

function future_date_list($time) {

	$output = '<ul class="roomMobile">';
	for ($I=0;$I<7;$I++) {
		$next_time = date("l, M j",$time + 86400*($I+1));
		$output .= sprintf('<li><a href="index.php?time=%s">%s</a></li>',strtotime($next_time),$next_time);
	}
	$output .= "</ul>";

	return $output;

}

function closestHalfHour() {
	$current_time      = time();
	$prev_half_hour    = $current_time - ($current_time % 1800);
	$next_half_hour    = $current_time + 1800;
	$closest_half_hour = (($current_time - $prev_half_hour) > ($next_half_hour - $current_time))?$next_half_hour:$prev_half_hour;


}

function available_now($date,$building) {
	// return "<li>$time</li>";

	$building_obj      = new building;
	$rooms_in_building = $building_obj->getRooms($building);

	// Example: downtown campus library , may 28 at 3pm
	//SELECT `rooms`.`ID` as `ID`, `rooms`.`name` as `name`, `rooms`.`number` as `number` FROM `rooms` WHERE `rooms`.`building`='2' AND `rooms`.`ID` NOT IN (SELECT `rooms`.`ID` FROM `rooms` LEFT JOIN `reservations` ON `rooms`.`ID`=`reservations`.`roomID` LEFT JOIN `building` ON `building`.`ID`=`rooms`.`building` WHERE `building`.`ID`='2' AND `reservations`.`startTime` >= '1464462000' AND `reservations`.`endTime` <= '1464462000')

	$localvars = localvars::getInstance();
	$db        = db::get($localvars->get('dbConnectionName'));
	$sql       = "SELECT `rooms`.`ID` FROM `rooms` LEFT JOIN `reservations` ON `rooms`.`ID`=`reservations`.`roomID` LEFT JOIN `building` ON `building`.`ID`=`rooms`.`building` WHERE `building`.`ID`='?' AND `reservations`.`startTime` <= '?' AND `reservations`.`endTime` >= '?'";
	$sqlResult = $db->query($sql,array($building,$date,$date));

	if ($sqlResult->error()) {
	  errorHandle::newError(__METHOD__."() - ".$sqlResult->errorMsg(), errorHandle::DEBUG);
		return false;
	}

	$unavailable_rooms = array();
	while ($row = $sqlResult->fetch()) {
		$unavailable_rooms[] = $row['ID'];
	}

	$output = "";
	foreach ($rooms_in_building as $room) {
		if (isset($unavailable_rooms[$room['ID']])) continue;
		$output .= sprintf('<li><a href="%s/building/room/?room=%s">%s -- %s</a></li>', $localvars->get("roomReservationHome"), $room['ID'], $room['name'], $room['number']);
	}

	return $output;

}

function buildAttributes($options) {

	$output = "";

	foreach ($options as $attr=>$value) {
		$output .= sprintf(' %s="%s" ',$attr,$value);
	}

	return $output;

}

function dropdownDurationSelect($selectHour,$options,$length=23) {

	$output = sprintf('<select %s>',buildAttributes($options));
	for ($I=0;$I<=$length;$I++) {
		$output .= sprintf('<option value="%s" %s>%s</option>',
			$I,
			($I == $selectHour)?"selected":"",
			$I
			);
	}
	$output .= "</select>";

	return $output;

}

// @TODO move into building class
function getBuildingName($ID) {

	$buildingObject = new building;
	$building       = $buildingObject->get($ID);

	return $building['name'];
}

// @TODO move into room class
function getRoomName($ID) {
	$roomObject = new building;
	$room       = $roomObject->get($ID);

	return $room['name'];
}


// @TODO move into room class
function getRoomInfo($ID) {
	$engine    = EngineAPI::singleton();
	$localvars = localvars::getInstance();
	$db        = db::get($localvars->get('dbConnectionName'));

	$sql = sprintf("SELECT rooms.*, policies.roomsClosed as roomsClosed, policies.publicScheduling as publicScheduling, policies.publicViewing as publicViewing, policies.url as policyURL, roomTemplates.url as roomTemplateURL, roomTemplates.mapURL as mapURL, building.roomListDisplay as roomListDisplay FROM rooms LEFT JOIN roomTemplates ON rooms.roomTemplate=roomTemplates.ID LEFT JOIN `policies` on roomTemplates.policy=policies.ID LEFT JOIN building ON building.ID=rooms.building WHERE rooms.ID=? LIMIT 1");
	$sqlResult = $db->query($sql,array($ID));

	if ($sqlResult->error()) {
		errorHandle::newError(__FUNCTION__."() - ".$sql, errorHandle::DEBUG);
		errorHandle::newError(__FUNCTION__."() - Error getting room information.".$sqlResult->errorMsg(), errorHandle::DEBUG);
		return(FALSE);
	}

	if ($sqlResult->rowCount() < 1) {
		return(FALSE);
	}

	$row = $sqlResult->fetch();
	$row['equipment'] = array();

	$sql = sprintf("SELECT equipement.* FROM equipement LEFT JOIN roomTypeEquipment on roomTypeEquipment.equipmentID=equipement.ID WHERE roomTypeEquipment.roomTemplateID=?");

	$sqlResult = $db->query($sql,array($row['roomTemplate']));

	if ($sqlResult->error()) {
		errorHandle::newError(__FUNCTION__."() - ".$sqlResult->errorMsg(), errorHandle::DEBUG);
	}
	else {
		while($eqRow  = $sqlResult->fetch()) {
			$row['equipment'][] = $eqRow;
		}
	}



	$row['displayName'] = str_replace("{name}", $row['name'], $row['roomListDisplay']);
	$row['displayName'] = str_replace("{number}", $row['number'], $row['displayName']);
	return($row);
}

// @todo move into building class
function getRoomsForBuilding($ID,$publicViewing=FALSE) {

	$engine    = EngineAPI::singleton();
	$localvars = localvars::getInstance();
	$db        = db::get($localvars->get('dbConnectionName'));

	$sql       = sprintf("SELECT * FROM building WHERE ID=?");
	$sqlResult = $db->query($sql,array($ID));

	$building = $sqlResult->fetch();
	$rooms    = array();

	$sql       = sprintf("SELECT * FROM `rooms` WHERE `building`=? ORDER BY %s",
		$db->escape($building['roomSortOrder'])
		);
	$sqlResult = $db->query($sql,array($ID));


	if ($sqlResult->error()) {
		errorHandle::newError(__FUNCTION__."() - ".$sqlResult->errorMsg(), errorHandle::DEBUG);
		errorHandle::errorMsg("Error retrieving rooms");
		return(FALSE);
	}

	$roomObj = new room;

	while($row = $sqlResult->fetch()) {

		$roomPolicy = $roomObj->getPolicyInfo($row['ID']);
		if ($publicViewing && $roomPolicy['publicViewing'] != '1') {
			continue;
		}

		$row['displayName'] = str_replace("{name}", $row['name'], $building['roomListDisplay']);
		$row['displayName'] = str_replace("{number}", $row['number'], $row['displayName']);
		$rooms[] = $row;
	}

	return($rooms);

}

// @todo move into room class
function getRoomPolicy($ID) {

	$engine    = EngineAPI::singleton();
	$localvars = localvars::getInstance();
	$db        = db::get($localvars->get('dbConnectionName'));

	$sql = sprintf("SELECT * FROM policies LEFT JOIN roomTemplates ON roomTemplates.policy=policies.ID LEFT JOIN rooms ON rooms.roomTemplate=roomTemplates.ID WHERE rooms.ID=?");
	$sqlResult = $db->query($sql,array($ID));

	if ($sqlResult->error()) {
		errorHandle::newError(__FUNCTION__."() - ".$sqlResult->errorMsg(), errorHandle::DEBUG);
		return(FALSE);
	}

	return($sqlResult->fetch());

}

// @todo move into room (or reservation?) class
function getRoomBookingsForDate($ID,$month=NULL,$day=NULL,$year=NULL) {

	$engine    = EngineAPI::singleton();
	$localvars = localvars::getInstance();
	$db        = db::get($localvars->get('dbConnectionName'));

	if (isnull($month)) {
		$month = date("n");
	}
	if (isnull($day)) {
		$day = date("d");
	}
	if (isnull($year)) {
		$year = date("Y");
	}

	$dayStart = mktime(0,0,0,$month,$day,$year);
	$dayEnd   = mktime(23,59,59,$month,$day,$year);

	if ($dayStart === FALSE) {
		return(FALSE);
	}

	$sql       = sprintf("SELECT * FROM reservations WHERE ((startTime>=? AND startTime<=?) OR (endTime>=? AND endTime<=?)) AND roomID=?");
	$sqlResult = $db->query($sql,array($dayStart,$dayEnd,$dayStart,$dayEnd,$ID));

	// @TODO : need sql error checking here

	$bookings = array();
	while ($row = $sqlResult->fetch()) {
		$bookings[] = $row;
	}

	return($bookings);

}

function getConfig($value) {

	$engine    = EngineAPI::singleton();
	$localvars = localvars::getInstance();
	$db        = db::get($localvars->get('dbConnectionName'));

	$sql       = sprintf("SELECT `value` FROM `siteConfig` WHERE `name`=?");
	$sqlResult = $db->query($sql,array($value));

	if ($sqlResult->error()) {
		errorHandle::newError(__FUNCTION__."() - ".$sqlResult->errorMsg(), errorHandle::DEBUG);
		return(FALSE);
	}

	$row       = $sqlResult->fetch();
	return($row['value']);

}

// @TOD move into messages class
function getResultMessage($value) {

	$engine    = EngineAPI::singleton();
	$localvars = localvars::getInstance();

	$db        = db::get($localvars->get('dbConnectionName'));

	$sql       = sprintf("SELECT `value` FROM `resultMessages` WHERE `name`=?");
	$sqlResult = $db->query($sql,array($value));

	if ($sqlResult->error()) {
		errorHandle::newError(__FUNCTION__."() - ".$sqlResult->errorMsg(), errorHandle::DEBUG);
		return("");
	}

	$row       = $sqlResult->fetch();
	return($row['value']);

}

function getTimeFormat() {
	$hourSetting = getConfig('24hour');
	if ($hourSetting == "1") {
		return "m/d/Y H:i";
	}
	else {
		return "m/d/Y g:iA";
	}
}

?>
