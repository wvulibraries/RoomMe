<?php
require_once("engineHeader.php");
recurseInsert("includes/functions.php","php");
$errorMsg = "";
$error    = FALSE;

$currentMonth = date("n");
$currentDay   = date("j");
$currentYear  = date("Y");

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

$time = NULL;
if (isset($engine->cleanPost['MYSQL']['submitListDate'])) {
	$time     = mktime(0,0,0,$engine->cleanPost['MYSQL']['start_month'],$engine->cleanPost['MYSQL']['start_day'],$engine->cleanPost['MYSQL']['start_year']);
	$time_end = mktime(23,59,0,$engine->cleanPost['MYSQL']['start_month'],$engine->cleanPost['MYSQL']['start_day'],$engine->cleanPost['MYSQL']['start_year']);
}
 
$sql       = sprintf("SELECT reservations.*, building.name as buildingName, building.roomListDisplay as roomListDisplay, rooms.name as roomName, rooms.number as roomNumber FROM `reservations` LEFT JOIN `rooms` on reservations.roomID=rooms.ID LEFT JOIN `building` ON building.ID=rooms.building WHERE %s ORDER BY building.name, rooms.name, reservations.username, reservations.startTime ",
	(isnull($time))?"reservations.endTime>'".time()."'":"reservations.startTime>'".$time."' AND reservations.endTime<'".$time_end."'"
	);
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

<form action="{phpself query="true"}" method="post">
	{csrf insert="post"}
	<table>
		<tr>
			<td>
				<label for="start_month">Month:</label>
				<select name="start_month" id="start_month" >
					<?php

					for($I=1;$I<=12;$I++) {
						printf('<option value="%s" %s>%s</option>',
							($I < 10)?"0".$I:$I,
							($I == $currentMonth)?"selected":"",
							$I);
					}
					?>
				</select>
			</td>
			<td>
				<label for="start_day">Day:</label>
				<select name="start_day" id="start_day" >
					<?php

					for($I=1;$I<=31;$I++) {
						printf('<option value="%s" %s>%s</option>',
							($I < 10)?"0".$I:$I,
							($I == $currentDay)?"selected":"",
							$I);
					}
					?>
				</select>
			</td>
			<td>
				<label for="start_year">Year:</label>
				<select name="start_year" id="start_year" >
					<?php

					for($I=$currentYear;$I<=$currentYear+10;$I++) {
						printf('<option value="%s">%s</option>',
							$I,
							$I);
					}
					?>
				</select>
			</td>
			<td style="vertical-align:bottom">
				<input type="submit" name="submitListDate" value="Change Date" />
			</td>
		</tr>
	</table>
	
</form>

<?php print $table->display($reservations); ?>


<?php
$engine->eTemplate("include","footer");
?>