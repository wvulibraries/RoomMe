<?php
require_once("engineHeader.php");
recurseInsert("includes/functions.php","php");
recurseInsert("includes/createReservations.php","php");

$errorMsg = "";
$error    = FALSE;

// we are editing a reservation
$reservationID   = "";
$reservationInfo = NULL;
$username        = "";
$groupname       = "";
$comments        = "";
$action          = "Add";
if (isset($engine->cleanGet['MYSQL']['id']) && validate::integer($engine->cleanGet['MYSQL']['id']) === TRUE) {
	$reservationID = $engine->cleanGet['MYSQL']['id'];
	localvars::add("reservationID",$reservationID);
	$sql       = sprintf("SELECT reservations.*, building.ID as buildingID FROM `reservations` LEFT JOIN `rooms` ON rooms.ID=reservations.roomID LEFT JOIN `building` ON building.ID=rooms.building WHERE reservations.ID='%s'",
		$reservationID
		);
	$sqlResult = $engine->openDB->query($sql);

	if (!$sqlResult['result']) {
		errorHandle::newError(__METHOD__."() - ".$sqlResult['error'], errorHandle::DEBUG);
		$error = TRUE;
	}
	else {
		$reservationInfo = mysql_fetch_array($sqlResult['result'],  MYSQL_ASSOC);
		$username        = $reservationInfo['username'];
		$groupname       = $reservationInfo['groupname'];
		$comments        = $reservationInfo['comments'];
		$_POST['MYSQL']['library'] = $reservationInfo['buildingID'];
		$_POST['HTML']['library']  = $reservationInfo['buildingID'];
		$_POST['RAW']['library']   = $reservationInfo['buildingID'];
		$_POST['MYSQL']['room']    = $reservationInfo['roomID'];
		$_POST['HTML']['room']     = $reservationInfo['roomID'];
		$_POST['RAW']['room']      = $reservationInfo['roomID'];

		$action = "Update";
	}

}

if (!isset($_POST['MYSQL']['library']) && validate::integer($_POST['MYSQL']['library']) === FALSE) {
	$errorMsg .= errorHandle::errorMsg("Missing or invalid building");
	$error = TRUE;
}
if (!isset($_POST['MYSQL']['room']) && validate::integer($_POST['MYSQL']['room']) === FALSE) {
	$errorMsg .= errorHandle::errorMsg("Missing or invalid room");
	$error = TRUE;
}



if ($error === FALSE) {

	$buildingID = $_POST['MYSQL']['library'];
	$roomID     = $_POST['MYSQL']['room'];

	localvars::add("buildingID",$buildingID);
	localvars::add("roomID",$roomID);

	$buildingName = getBuildingName($buildingID);
	$roomName     = getRoomName($roomID);

	localvars::add("buildingName",$buildingName);
	localvars::add("roomName",$roomName);

	$sql       = sprintf("SELECT * FROM `via` ORDER BY `name`");
	$sqlResult = $engine->openDB->query($sql);

	if (!$sqlResult['result']) {
		errorHandle::newError(__METHOD__."() - ".$sqlResult['error'], errorHandle::DEBUG);
		$error = TRUE;
	}
	else {
		$viaOptions = "";
		while($row = mysql_fetch_array($sqlResult['result'],  MYSQL_ASSOC)) {
			$viaOptions .= sprintf('<option value="%s" %s>%s</option>',
				htmlSanitize($row['ID']),
				(!isnull($reservationInfo) && $row['ID'] == $reservationInfo['createdVia'])?"selected":"",
				htmlSanitize($row['name'])
				);
		}
		localvars::add("viaOptions",$viaOptions);
	}

}

if (isset($_POST['MYSQL']['roomSubmit'])) {

	// This comes from the room select page

}
else if (isset($_POST['MYSQL']['createSubmit'])) {

	createReservation($buildingID,$roomID);

	if (isset($engine->cleanGet['MYSQL']['id']) && validate::integer($engine->cleanGet['MYSQL']['id']) === TRUE) {
		$sql       = sprintf("SELECT reservations.*, building.ID as buildingID FROM `reservations` LEFT JOIN `rooms` ON rooms.ID=reservations.roomID LEFT JOIN `building` ON building.ID=rooms.building WHERE reservations.ID='%s'",
			$reservationID
			);
		$sqlResult = $engine->openDB->query($sql);

		if (!$sqlResult['result']) {
			errorHandle::newError(__METHOD__."() - ".$sqlResult['error'], errorHandle::DEBUG);
			$error = TRUE;
		}
		else {
			$reservationInfo = mysql_fetch_array($sqlResult['result'],  MYSQL_ASSOC);
			$username        = $reservationInfo['username'];
			$groupname       = $reservationInfo['groupname'];
			$comments        = $reservationInfo['comments'];
		}

	}
	

}
else if (isset($_POST['MYSQL']['deleteSubmit'])) {

	$sql       = sprintf("DELETE FROM `reservations` WHERE ID='%s'",
		$_POST['MYSQL']['reservationID']
		);
	$sqlResult = $engine->openDB->query($sql);

	if ($sqlResult['result']) {
		header('Location: reservationsList.php');
	}

	$error = TRUE;
	errorHandle::newError(__METHOD__."() - ".$sqlResult['error'], errorHandle::DEBUG);
	errorHandle::errorMsg("Error deleting reservation.");

}

$currentMonth = (isnull($reservationInfo))?date("n"):date("n",$reservationInfo['startTime']);
$currentDay   = (isnull($reservationInfo))?date("j"):date("j",$reservationInfo['startTime']);
$currentYear  = (isnull($reservationInfo))?date("Y"):date("Y",$reservationInfo['startTime']);
$currentHour  = (isnull($reservationInfo))?date("G"):date("G",$reservationInfo['startTime']);
$nextHour     = (isnull($reservationInfo))?(date("G")+1):date("G",$reservationInfo['endTime']);

localvars::add("username",$username);
localvars::add("groupname",$groupname);
localvars::add("comments",$comments);
localvars::add("action",$action);

$sql        = sprintf("SELECT value FROM siteConfig WHERE name='24hour'");
$sqlResult  = $engine->openDB->query($sql);

$displayHour = 24;
if ($sqlResult['result']) {
	$row        = mysql_fetch_array($sqlResult['result'],  MYSQL_ASSOC);
	$displayHour = ($row['value'] == 1)?24:12;
}

$engine->eTemplate("include","header");
?>

<header>
<h1>{local var="action"} a Reservation</h1>
</header>

<?php
if (count($engine->errorStack) > 0) {
?>
<section id="actionResults">
	<header>
		<h1>Results</h1>
	</header>
	<?php print errorHandle::prettyPrint(); ?>
</section>
<?php } ?>

<p>Adding a reservation for Room <strong>{local var="roomName"}</strong> in building <strong>{local var="buildingName"}</strong></p>

<form action="{phpself query="true"}" method="post">
	{csrf insert="post"}

	<input type="hidden" name="library" value="{local var="buildingID"}" />
	<input type="hidden" name="room" value="{local var="roomID"}" />
	<input type="hidden" name="reservationID" value="{local var="reservationID"}" />

	<fieldset>
		<legend>User Information</legend>
		<label for="username" class="requiredField">Username:</label> &nbsp; <input type="text" id="username" name="username" value="{local var="username"}"/>
		<br />
		<label for="groupName">Groupname:</label> &nbsp; <input type="text" id="groupname" name="groupname" value="{local var="groupname"}"/>
	</fieldset>
	<br />
	<fieldset>
		<legend>Room Information</legend>
	<table>
		<tr>
			<td>
				<label for="start_month">Month:</label><br />
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
				<label for="start_day">Day:</label><br />
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
				<label for="start_year">Year:</label><br />
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
			<td></td>
		</tr>
		<tr>
			<td colspan="2">
				Start Time
			</td>
			<td colspan="2">
				End Time
			</td>
		</tr>
		<tr>	
			<td>
				<label for="start_hour">Hour:</label><br />
				<select name="start_hour" id="start_hour" >
					<?php

						for($I=0;$I<=23;$I++) {
							printf('<option value="%s" %s>%s</option>',
								($I < 10)?"0".$I:$I,
								($I == $currentHour)?"selected":"",
								($displayHour == 24)?$I:(($I==12)?"12pm":(($I>=13)?($I-12)."pm":(($I == 0)?"12am":$I."am"))));
						}
					?>
				</select>
			</td>
			<td>
				<label for="start_minute">Minute:</label><br />
				<select name="start_minute" id="start_minute" >
					<?php
						for($I=0;$I<60;$I += 15) {
							printf('<option value="%s">%s</option>',
								($I < 10)?"0".$I:$I,
								$I);
						}
					?>
				</select>
			</td>

			<td>
				<label for="end_hour">Hour:</label><br />
				<select name="end_hour" id="end_hour" >
					<?php

						for($I=0;$I<=23;$I++) {
							printf('<option value="%s" %s>%s</option>',
								($I < 10)?"0".$I:$I,
								($I == $nextHour)?"selected":"",
								($displayHour == 24)?$I:(($I==12)?"12pm":(($I>=13)?($I-12)."pm":(($I == 0)?"12am":$I."am"))));
						}
					?>
				</select>
			</td>
			<td>
				<label for="end_minute">Minute:</label><br />
				<select name="end_minute" id="end_minute" >
					<?php
						for($I=0;$I<60;$I += 15) {
							printf('<option value="%s">%s</option>',
								($I < 10)?"0".$I:$I,
								$I);
						}
					?>
				</select>
			</td>
		</tr>
	</table>
</fieldset>
	<br />
	<fieldset>
		<legend>Administrative Information</legend>
		<label for="via">Via:</label>
		<select name="via" id="via">
			{local var="viaOptions"}
		</select>
		<br />

		<label for="override">Override:</label>
		<select name="override" id="override">
			<option value="0" selected>No</option>
			<option value="1">Yes</option>
		</select>
		<label for="comments">
			Comments/Notes:
		</label>
		<textarea name="comments" id="comments">{local var="comments"}</textarea>
	</fieldset>
	<br /><br />
	<input type="submit" name="createSubmit" value="Reserve this Room"/> &nbsp;&nbsp;

	<?php if (!isnull($reservationInfo)) { ?>

	<input type="submit" name="deleteSubmit" value="Delete" id="deleteReservation"/>

	<?php }	?>

</form>




<?php
$engine->eTemplate("include","footer");
?>