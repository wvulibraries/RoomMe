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

$reservations    = array();

if (isset($_POST['MYSQL'])) {
	if (isset($_POST['MYSQL']['building'])) {
		$buildingID = $_POST['MYSQL']['building'];
	}
	if (isset($_POST['MYSQL']['room'])) {
		$roomID = $_POST['MYSQL']['room'];
	}
}

$sql       = sprintf("SELECT * FROM `building` ORDER BY `name`");
$sqlResult = $engine->openDB->query($sql);

if (!$sqlResult['result']) {
	$errorMsg .= errorHandle::errorMsg("Error retrieving library list.");
	$error = TRUE;
}

if ($error === FALSE) {
$options = "";
while ($row = mysql_fetch_array($sqlResult['result'],  MYSQL_ASSOC)) {
	$options .= sprintf('<option value="%s">%s</option>',
		htmlSanitize($row['ID']),
		htmlSanitize($row['name']));
}

$localvars->set("librarySelectOptions",$options);
}


$displayOutput = "";
if ($error === FALSE && isset($_POST['MYSQL']) && isset($_POST['MYSQL']['library'])) {

	$time = NULL;
	$time     = mktime(0,0,0,$_POST['MYSQL']['start_month'],$_POST['MYSQL']['start_day'],$_POST['MYSQL']['start_year']);
	$time_end = mktime(23,59,0,$_POST['MYSQL']['start_month'],$_POST['MYSQL']['start_day'],$_POST['MYSQL']['start_year']);


	$sql       = sprintf("SELECT reservations.*, building.name as buildingName, building.roomListDisplay as roomListDisplay, rooms.name as roomName, rooms.number as roomNumber FROM `reservations` LEFT JOIN `rooms` on reservations.roomID=rooms.ID LEFT JOIN `building` ON building.ID=rooms.building WHERE %s AND building.ID='%s' ORDER BY building.name, rooms.name, rooms.number, reservations.startTime ",
		(isnull($time))?"reservations.endTime>'".time()."'":"reservations.startTime>='".$time."' AND reservations.startTime<'".$time_end."'",
		$_POST['MYSQL']['library']
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
		$headers[] = "Start Time";
		$headers[] = "End Time";
		$headers[] = "Hours";

		$hourSetting = getConfig('24hour');
		if ($hourSetting == "1") {
			$timeFormat = "m/d/Y H:i";
		}
		else {
			$timeFormat = "m/d/Y g:iA";
		}

		$tablesArray = array();

		$previousRoomName = NULL;
		$previousRow      = NULL;

		while($row       = mysql_fetch_array($sqlResult['result'],  MYSQL_ASSOC)) {

			$roomDisplayName = str_replace("{name}", $row['roomName'], $row['roomListDisplay']);
			$roomDisplayName = str_replace("{number}", $row['roomNumber'], $roomDisplayName);

			if ($roomDisplayName != $previousRoomName) {

				if (!isnull($previousRoomName)) {
					$displayOutput .= sprintf('<h1>%s</h1><h2>%s</h2><h3>%s</h3>%s',
						$row['buildingName'],
						$previousRoomName,
						$_POST['MYSQL']['start_month']."/".$_POST['MYSQL']['start_day']."/".$_POST['MYSQL']['start_year'],
						$table->display($reservations)
						);
					$reservations  = array();
					$previousRow   = NULL;
				}

				$table           = new tableObject("array");
				$table->sortable = TRUE;
				$table->summary  = "Room reservation listings";
				$table->class    = "styledTable";

				$table->headers($headers);
			}

			$displayName = $row['username'];
			if (isset($row['groupname']) && !isempty($row['groupname'])) {
				$displayName .= " (".$row['groupname'].")";
			}

			$temp = array();
			$temp['username']  = $displayName;
			$temp['startTime'] = date($timeFormat,$row['startTime']);
			$temp['endTime']   = date($timeFormat,$row['endTime']);
			$temp['hoursOnReservationTable'] = ($row['endTime'] - $row['startTime'])/60/60;

			$reservations[]   = $temp;

			$previousRoomName = $roomDisplayName;
			$previousRow      = $row;

		}

		if ($sqlResult['numrows'] > 0) {  
			$displayOutput .= sprintf('<h1>%s</h1><h2>%s</h2><h3>%s</h3>%s',
				$previousRow['buildingName'],
				$previousRoomName,
				$_POST['MYSQL']['start_month']."/".$_POST['MYSQL']['start_day']."/".$_POST['MYSQL']['start_year'],
			"foo"//$table->display($reservations)
			);
		}
		else if ($sqlResult['numrows'] == 0) {
			$displayOutput = "No reservations found.";
		}
		else {
			$displayOutput = "Error gathering reservations.";
		}

	}

}

$localvars->set("displayOutput",$displayOutput);

?>

<html>
<head>
	<title>Print Room Reservations</title>

<style>

h1 {
	page-break-before: always;
	margin: 0;
}
h2 {
	margin: 0;
}
h3 {
	margin: 0;
	margin-bottom: 20px;
}

p {
	font-size:200%; 
	margin: 0;
}

.styledTable {
  border-collapse:collapse;
  background-color:#ffffff;
  width:645px;
}
.styledTable td, .styledTable th {
  border-style:solid;
  border-width:1px;
  border-color:#000000;
  padding:10px;
}
.styledTable th { background-color:#dddcdc; }
.styledTable th h1 {
  font-size:100%;
  padding-top:0;
  padding-bottom:0;
  margin-top:0;
  margin-bottom:0;
}
.styledTable td.options {
  color:#000000;
  font-weight:bold;
  text-align:center;
}
.styledTable td.info { vertical-align:top; }
.styledTable td.blackLine {
  border-width:2px;
  padding:0;
}
.styledTable td.tableLists { vertical-align:top; }
.styledTable td ul {
  list-style:none;
  margin:0;
  padding:0;
}
.styledTable td ul li {
  margin-bottom:9px;
  line-height:.97em;
}
</style>

</head>

<body>

<p><strong>Select Date and Building</strong></p>

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
		</tr>
		<tr>
			<td colspan="3">
				<select name="library" id="library">
					{local var="librarySelectOptions"}
				</select>
			</td>
		</tr>
		<tr>
			<td style="vertical-align:bottom" colspan="3">
				<input type="submit" name="submitListDate" value="Change Date" />
			</td>
		</tr>
	</table>
	
</form>

{local var="displayOutput"}

</body>
</html>