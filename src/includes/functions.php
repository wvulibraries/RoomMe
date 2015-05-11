<?php

function buildAttributes($options) {

	$output = "";

	foreach ($options as $attr=>$value) {
		$output .= sprintf(' %s="%s" ',$attr,$value);
	}

	return $output;

}

function dropdownDurationSelect($selectHour,$options) {

	$output = sprintf('<select %s>',buildAttributes($options));
	for ($I=0;$I<=23;$I++) {
		$output .= sprintf('<option value="%s" %s>%s</option>',
			$I,
			($I == $selectHour)?"selected":"",
			$I
			);
	}
	$output .= "</select>";

	return $output;

}

function getBuildingName($ID) {

	$buildingObject = new building;
	$building       = $buildingObject->get($ID);

	return $building['name'];
}

function getRoomName($ID) {
	$roomObject = new building;
	$room       = $roomObject->get($ID);

	return $room['name'];
}

function getRoomInfo($ID) {
	$engine    = EngineAPI::singleton();
	$localvars = localvars::getInstance();
	$db        = db::get($localvars->get('dbConnectionName'));

	$sql = sprintf("SELECT rooms.*, policies.publicScheduling as publicScheduling, policies.publicViewing as publicViewing, policies.url as policyURL, roomTemplates.url as roomTemplateURL, roomTemplates.mapURL as mapURL, building.roomListDisplay as roomListDisplay FROM rooms LEFT JOIN roomTemplates ON rooms.roomTemplate=roomTemplates.ID LEFT JOIN `policies` on roomTemplates.policy=policies.ID LEFT JOIN building ON building.ID=rooms.building WHERE rooms.ID=? LIMIT 1");
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

function getRoomsForBuilding($ID) {

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

	while($row = $sqlResult->fetch()) {
		$row['displayName'] = str_replace("{name}", $row['name'], $building['roomListDisplay']);
		$row['displayName'] = str_replace("{number}", $row['number'], $row['displayName']);
		$rooms[] = $row;
	}

	return($rooms);

}

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
