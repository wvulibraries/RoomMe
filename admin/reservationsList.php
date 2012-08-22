<?php
require_once("engineHeader.php");
recurseInsert("includes/functions.php","php");
$errorMsg = "";
$error    = FALSE;

$buildingID = NULL;
$roomID     = NULL;

$table           = new tableObject("array");
$table->sortable = TRUE;
$table->summary  = "Room reservation listings";
$table->class    = "styledTable";

$reservations    = array();

if (isset($engine->cleanPost['MYSQL'])) {
	if (isset($engine->cleanPost['MYSQL']['building'])) {
		$buildingID = $engine->cleanPost['MYSQL']['building'];
	}
	if (isset($engine->cleanPost['MYSQL']['room'])) {
		$roomID = $engine->cleanPost['MYSQL']['room'];
	}
}


$sql       = sprintf("SELECT reservations.*, building.name as buildingName, building.roomListDisplay as roomListDisplay, rooms.name as roomName, rooms.number as roomNumber FROM `reservations` LEFT JOIN `rooms` on reservations.roomID=rooms.ID LEFT JOIN `building` ON building.ID=rooms.building WHERE reservations.endTime>'%s' ORDER BY building.name, rooms.name, reservations.username, reservations.startTime ",
	time());
$sqlResult = $engine->openDB->query($sql);

if (!$sqlResult['result']) {
	$error     = TRUE;
	$errorMsg .= errorHandle::errorMsg("Error retrieving reservation list.");
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
	if ($hoursOnTable == "1") {
		$headers[] = "Hours";
	}
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
		if (isset($row['groupname']) && !isempty($row['groupname'])) {
			$displayName .= " (".$row['groupname'].")";
		}

		$roomDisplayName = str_replace("{name}", $row['roomName'], $row['roomListDisplay']);
		$roomDisplayName = str_replace("{number}", $row['roomNumber'], $roomDisplayName);

		$temp = array();
		$temp['username']  = $displayName; //$row['username'];
		$temp['building']  = $row['buildingName'];
		$temp['room']      = $roomDisplayName; //$row['roomName'];
		$temp['startTime'] = date($timeFormat,$row['startTime']);
		$temp['endTime']   = date($timeFormat,$row['endTime']);
		if ($hoursOnTable == "1") {
			$temp['hoursOnReservationTable'] = ($row['endTime'] - $row['startTime'])/60/60;
		}
		$temp['edit']      = sprintf('<a href="reservationCreate.php?id=%s">Edit</a>',
			$engine->openDB->escape($row['ID'])
			);

		$reservations[] = $temp;

	}
}

$engine->eTemplate("include","header");
?>

<header>
<h1>Reservation Listing</h1>
</header>


<?php print $table->display($reservations); ?>


<?php
$engine->eTemplate("include","footer");
?>