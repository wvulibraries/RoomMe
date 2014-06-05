<?php

require_once("engineHeader.php");
recurseInsert("includes/functions.php","php");

$errorMsg = "";
$error    = FALSE;

$table           = new tableObject("array");
$table->sortable = TRUE;
$table->summary  = "Room reservation listings";
$table->class    = "styledTable";

$reservations    = array();

$sql       = sprintf("SELECT seriesReservations.*, building.name as buildingName, building.roomListDisplay as roomListDisplay, rooms.name as roomName, rooms.number as roomNumber FROM `seriesReservations` LEFT JOIN `rooms` on seriesReservations.roomID=rooms.ID LEFT JOIN `building` ON building.ID=rooms.building ORDER BY building.name, rooms.name, seriesReservations.username, seriesReservations.startTime ");
$sqlResult = $engine->openDB->query($sql);

if ($sqlResult->error()) {
	$error     = TRUE;
	$errorMsg .= errorHandle::errorMsg("Error retrieving series reservation list.");
	errorHandle::newError(__METHOD__."() - ".$sqlResult['error'], errorHandle::DEBUG);
}

if ($error === FALSE) {

	$hoursOnTable = getConfig("hoursOnReservationTable");

	$headers = array();
	$headers[] = "Username";
	$headers[] = "Building";
	$headers[] = "Room";
	$headers[] = "Start Time";
	$headers[] = "End Time";
	$headers[] = "Frequency";
	$headers[] = "Edit";
	$table->headers($headers);

	$hourSetting = getConfig('24hour');
	if ($hourSetting == "1") {
		$timeFormat = "m/d/Y H:i";
	}
	else {
		$timeFormat = "m/d/Y g:iA";
	}
	
	while($row       = mysql_fetch_array($sqlResult['result'],  MYSQL_ASSOC)) {

		$displayName = $row['username'];
		if (isset($row['groupname']) && !is_empty($row['groupname'])) {
			$displayName .= " (".$row['groupname'].")";
		}

		$roomDisplayName = str_replace("{name}", $row['roomName'], $row['roomListDisplay']);
		$roomDisplayName = str_replace("{number}", $row['roomNumber'], $roomDisplayName);

		switch($row['frequency']) {
			case "1":
				$frequency = "Every Week";
				break;
			case "2":
				$frequency = "Every Month (Day)";
				break;
			case "3":
				$frequency = "Every Month (Week Day)";
				break;
			case "0":
				$frequency = "Every Day";
				break;
			default:
				$frequency = "error";
				break;
		}

		$temp = array();
		$temp['username']  = $displayName; //$row['username'];
		$temp['building']  = $row['buildingName'];
		$temp['room']      = $roomDisplayName; //$row['roomName'];
		$temp['startTime'] = date($timeFormat,$row['startTime']);
		$temp['endTime']   = date($timeFormat,$row['endTime']);
		$temp['frequency'] = $frequency;
		$temp['edit']      = sprintf('<a href="seriesCreate.php?id=%s">Edit</a>',
			$engine->openDB->escape($row['ID'])
			);

		$reservations[] = $temp;

	}
}

$engine->eTemplate("include","header");
?>
<header>
<h1>Series Reservation Listing</h1>
</header>

<?php print $table->display($reservations); ?>


<?php
$engine->eTemplate("include","footer");
?>