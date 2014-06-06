<?php

function getBuildingName($ID) {

	$engine = EngineAPI::singleton();
	$localvars = localvars::getInstance();
	$db     = db::get($localvars->get('dbConnectionName'));

	$sql       = sprintf("SELECT name FROM building WHERE `ID`=? LIMIT 1");
	$sqlResult = $db->query($sql,array($ID));
	
	if ($sqlResult->error()) {
		errorHandle::newError(__METHOD__."() - Error getting building name.", errorHandle::DEBUG);
		return(FALSE);
	}

	if ($sqlResult->rowCount() < 1) {
		return("Not Found");
	}

	$row = $sqlResult->fetch();

	return($row['name']);
}

function getRoomName($ID) {
	$engine = EngineAPI::singleton();
	$localvars = localvars::getInstance();
	$db     = db::get($localvars->get('dbConnectionName'));

	$sql = sprintf("SELECT name FROM rooms WHERE `ID`=?");

	$sqlResult = $db->query($sql,array($ID));
	
	if ($sqlResult->error()) {
		errorHandle::newError(__METHOD__."() - Error getting room name.", errorHandle::DEBUG);
		return(FALSE);
	}

	$row = $sqlResult->fetch();

	return($row['name']);
}

function getRoomInfo($ID) {
	$engine = EngineAPI::singleton();
	$localvars = localvars::getInstance();
	$db     = db::get($localvars->get('dbConnectionName'));

	$sql = sprintf("SELECT rooms.*, policies.publicScheduling as publicScheduling, policies.publicViewing as publicViewing, policies.url as policyURL, roomTemplates.url as roomTemplateURL, roomTemplates.mapURL as mapURL, building.roomListDisplay as roomListDisplay FROM rooms LEFT JOIN roomTemplates ON rooms.roomTemplate=roomTemplates.ID LEFT JOIN `policies` on roomTemplates.policy=policies.ID LEFT JOIN building ON building.ID=rooms.building WHERE rooms.ID=? LIMIT 1");
	$sqlResult = $db->query($sql,array($ID));

	if ($sqlResult->error()) {
		errorHandle::newError(__METHOD__."() - ".$sql, errorHandle::DEBUG);
		errorHandle::newError(__METHOD__."() - Error getting room information.".$sqlResult['error'], errorHandle::DEBUG);
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
		errorHandle::newError(__METHOD__."() - ".$sqlResult['error'], errorHandle::DEBUG);
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

	$engine = EngineAPI::singleton();
	$localvars = localvars::getInstance();
	$db     = db::get($localvars->get('dbConnectionName'));

	$sql       = sprintf("SELECT * FROM building WHERE ID=?");
	$sqlResult = $db->query($sql,array($ID));

	$building = $sqlResult->fetch();
	$rooms    = array();

	$sql       = sprintf("SELECT * FROM `rooms` WHERE `building`=? ORDER BY %s",
		$db->escape($building['roomSortOrder'])
		);
	$sqlResult = $db->query($sql,array($ID));


	if ($sqlResult->error()) {
		errorHandle::newError(__METHOD__."() - ".$sqlResult['error'], errorHandle::DEBUG);
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

	$engine = EngineAPI::singleton();
	$localvars = localvars::getInstance();
	$db     = db::get($localvars->get('dbConnectionName'));

	$sql = sprintf("SELECT * FROM policies LEFT JOIN roomTemplates ON roomTemplates.policy=policies.ID LEFT JOIN rooms ON rooms.roomTemplate=roomTemplates.ID WHERE rooms.ID=?");
	$sqlResult = $db->query($sql,array($ID));

	if ($sqlResult->error()) {
		errorHandle::newError(__METHOD__."() - ".$sqlResult['error'], errorHandle::DEBUG);
		return(FALSE);
	}

	return($sqlResult->fetch());

}

function getRoomBookingsForDate($ID,$month=NULL,$day=NULL,$year=NULL) {

	$engine = EngineAPI::singleton();
	$localvars = localvars::getInstance();
	$db     = db::get($localvars->get('dbConnectionName'));

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

	$engine = EngineAPI::singleton();
	$localvars = localvars::getInstance();
	$db     = db::get($localvars->get('dbConnectionName'));

	$sql       = sprintf("SELECT `value` FROM `siteConfig` WHERE `name`=?");
	$sqlResult = $db->query($sql,array($value));

	if ($sqlResult->error()) {
		errorHandle::newError(__METHOD__."() - ".$sqlResult['error'], errorHandle::DEBUG);
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
		errorHandle::newError(__METHOD__."() - ".$sqlResult['error'], errorHandle::DEBUG);
		return("");
	}

	$row       = $sqlResult->fetch(); 
	return($row['value']);

}

function drawRoomCalendar($roomID,$date) {

	$engine = EngineAPI::singleton();

	$calType = "building";

	if (!is_array($roomID)) {
		$roomID = array($roomID);
		$calType   = "room";
	}

	if (!is_array($date)) {
		errorHandle::newError(__METHOD__."() - date not given as array", errorHandle::DEBUG);
		return(FALSE);
	}

	$day   = $date[1];
	$month = $date[0];
	$year  = $date[2];

	$rooms = array();
	foreach ($roomID as $I=>$ID) {
		$temp             = getRoomInfo($ID);
		$temp['policy']   = getRoomPolicy($ID);
		$temp['bookings'] = getRoomBookingsForDate($ID,$month,$day,$year);

		$rooms[] = $temp;

	}

// ///
// For Debugging
// print "<pre>";
// var_dump($rooms);
// print "</pre>";

	$calendarDisplayName = getConfig('calendarDisplayName');

	$currentMonth = (!isset($_GET['MYSQL']['reservationSTime']))?date("n"):date("n",$_GET['MYSQL']['reservationSTime']);
	$currentDay   = (!isset($_GET['MYSQL']['reservationSTime']))?date("j"):date("j",$_GET['MYSQL']['reservationSTime']);
	$currentYear  = (!isset($_GET['MYSQL']['reservationSTime']))?date("Y"):date("Y",$_GET['MYSQL']['reservationSTime']);
	$currentHour  = (!isset($_GET['MYSQL']['reservationSTime']))?date("G"):date("G",$_GET['MYSQL']['reservationSTime']);
	$currentMin   = (!isset($_GET['MYSQL']['reservationSTime']))?"00":date("i",$_GET['MYSQL']['reservationSTime']);
	$nextHour     = (!isset($_GET['MYSQL']['reservationETime']))?(date("G")+1):date("G",$_GET['MYSQL']['reservationETime']);
	$nextMin      = (!isset($_GET['MYSQL']['reservationSTime']))?"00":date("i",$_GET['MYSQL']['reservationSTime']);

	$reservationSTime = mktime($currentHour,$currentMin,0,$month,$day,$year);
	$reservationETime = mktime($nextHour,$nextMin,0,$month,$day,$year);

	$output  = '<table id="reservationsRoomTable">';

	$output .= "<thead>";
	$output .= '<tr id="reservationsRoomTableHeader">';
	$output .= '<th id="firstCalHeader">&nbsp;</th>';
	foreach ($rooms as $I=>$room) {

		$displayName = str_replace("{name}", $room['name'], $calendarDisplayName);
		$displayName = str_replace("{number}", $room['number'], $displayName);

		$output .= sprintf('<th><a href="room.php?room=%s&reservationSTime=%s&reservationETime=%s">%s</a></th>',
			htmlSanitize($room['ID']),
			htmlSanitize($reservationSTime),
			htmlSanitize($reservationETime),
			htmlSanitize($displayName)
			);
	}
	$output .= '</tr>';
	$output .= "</thead>";

	$output .= "<tbody>";

	$usernameCheck = array();

	$displayHour   = getConfig("24Hour");
	$displayHour   = ($displayHour == 0)?12:24;

	$displayNameAs = getConfig("displayNameAs");
	$durationRooms = getConfig("displayDurationOnRoomsCal");
	$durationBuild = getConfig("displayDurationOnBuildingCal");

	for ($I = 0;$I<=23;$I++) {

		for ($K = 0;$K<60;$K++) {

			$trClass = "";
			switch($K) {
				case 0:
					$trClass = "hour";
					break;
				case 30:
					$trClass = "half";
					break;
				case 15:
				case 45:
					$trClass = "quarter";
					break;
				default:
					$trClass = "minor";
					break;
			}

if ($K%15 == 0) {

			$rowTime = mktime($I,$K,"0",$month,$day,$year);

			$output .= sprintf('<tr class="%s">',
				$trClass
					);

			if ($K == 0) {
			$output .= sprintf('<th rowspan="4">%s</th>',
				($displayHour == 24)?$I:(($I==12)?"12pm":(($I>=13)?($I-12)."pm":(($I == 0)?"12am":$I."am"))) // someone will hate me later. 
				);
			}

			foreach ($rooms as $roomIndex=>$room) {

				$tdClass     = "notReserved";
				$username    = "";
				$displayTime = "";
				$duration    = "";

				$reserved    = FALSE;

				foreach ($room['bookings'] as $bookingsIndex=>$booking) {
					if ($rowTime >= $booking['startTime'] && $rowTime < $booking['endTime']) {

						$reserved = TRUE;
						$tdClass  = "reserved";
						if (!isset($usernameCheck[$booking['ID']])) {

							if ($durationRooms == "1" || $durationBuild == "1") {
								$duration = ($booking['endTime'] - $booking['startTime'])/60/60;
								$duration = "(".$duration." hour".(($duration!=1)?"s":"").")";
							}

							switch($displayNameAs) {
								case "username":
									if (!is_empty($booking['groupname'])) {
										$username = $booking['groupname'];
									}
									else {
										$username = $booking['username'];
									}
									break;
								case "initials":
								$username = $booking['initials'];
								break;
								default:
								break;
							}

							if ($displayHour == "1") {
								$timeFormat = "H:i";
							}
							else {
								$timeFormat = "g:iA";
							}

							$startDisplayTime = date($timeFormat,$booking['startTime']);
							$endDisplayTime   = date($timeFormat,$booking['endTime']);
							$displayTime      = $startDisplayTime ." - ". $endDisplayTime;

							$usernameCheck[$booking['ID']] = TRUE;
						}
						break;
					}
				}

				$tdClass .= " calendarCol";

				$output .= sprintf('<td class="%s">%s %s %s</td>',
					$tdClass,
					($K%15 == 0)?$username:"",
					($K%15 == 0)?$displayTime:"",
					($reserved===TRUE && $K%15 == 0)?$duration:""
					);

				unset($username);
				unset($displayTime);
				unset($duration);
			}

			$output .= '</tr>';
		}
		}

	}

	$output .= "</tbody>";
	$output .= "<tfoot>";
	$output .= '<tr id="reservationsRoomTableHeader">';
	$output .= '<th id="firstCalHeader">&nbsp;</th>';
	foreach ($rooms as $I=>$room) {

		$displayName = str_replace("{name}", $room['name'], $calendarDisplayName);
		$displayName = str_replace("{number}", $room['number'], $displayName);

		$output .= sprintf('<th><a href="room.php?room=%s&reservationSTime=%s&reservationETime=%s">%s</a></th>',
			htmlSanitize($room['ID']),
			htmlSanitize($reservationSTime),
			htmlSanitize($reservationETime),
			htmlSanitize($displayName)
			);
	}
	$output .= '</tr>';
	$output .= "</tfoot>";
	$output .= '</table>';

	return($output);

}

?>