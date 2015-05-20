<?php
require_once("../../../engineHeader.php");

$errorMsg = "";
$error    = FALSE;

$db       = db::get($localvars->get('dbConnectionName'));

$series = new series;

// we are editing a reservation
$reservationID    = "";
$reservationInfo  = NULL;
$username         = "";
$groupname        = "";
$comments         = "";

// This is so broken :-/
if (isset($_POST['MYSQL']['library'])) {
	http::setGet("library",$_POST['RAW']['library']);
	http::setGet("room",$_POST['RAW']['room']);
}

try {

// We have an edit instead of a new page
	if (isset($_GET['MYSQL']['id'])) {

		$series->get($_GET['MYSQL']['id']);

	}
	else if (isset($_POST['MYSQL']['library']) && isset($_POST['MYSQL']['room'])) {

		$series->setBuilding($_POST['MYSQL']['library']);
		$series->setRoom($_POST['MYSQL']['room']);

	}


if (isset($_POST['MYSQL']['createSubmit'])) {

	if (!$series->create()) {
		throw new Exception("Error Creating Reservation.");
	}

} // submit create
else if (isset($_POST['MYSQL']['deleteSubmit'])) {

	if ($series->delete($_POST['MYSQL']['reservationID'])) {
		header('Location: index.php');
	}

	throw new Exception("Error Deleting Series.");

}
} // 1st Try
catch (Exception $e) {
	errorHandle::errorMsg($e->getMessage());
}

// Create the Via Dropdown
try {
	// @TODO : This needs to be taken out of here
	$sql       = sprintf("SELECT * FROM `via` ORDER BY `name`");
	$sqlResult = $db->query($sql);

	if ($sqlResult->error()) {
		errorHandle::newError($sqlResult->errorMsg(), errorHandle::DEBUG);
		throw new Exception("Error creating via select");
	}
	else {
		$viaOptions = "";
		while($row = $sqlResult->fetch()) {
			$viaOptions .= sprintf('<option value="%s" %s>%s</option>',
				htmlSanitize($row['ID']),
				(!isnull($series->reservation) && $row['ID'] == $series->reservation['createdVia'])?"selected":"",
				htmlSanitize($row['name'])
				);
		}
		$localvars->set("viaOptions",$viaOptions);
	}
	// End Via TODO
}
catch (Exception $e) {
	errorHandle::errorMsg($e->getMessage());
}
// End Via dropdown creation

// If this is a new reservation, use the current time. 
// If this is an update, use the time from the reservation
$currentMonth = ($series->isNew())?date("n"):date("n",$series->reservation['startTime']);
$currentDay   = ($series->isNew())?date("j"):date("j",$series->reservation['startTime']);
$currentYear  = ($series->isNew())?date("Y"):date("Y",$series->reservation['startTime']);
$currentHour  = ($series->isNew())?date("G"):date("G",$series->reservation['startTime']);
$nextHour     = ($series->isNew())?(date("G")+1):date("G",$series->reservation['endTime']);

$startMinute = ($series->isNew())?"0":date("i",$series->reservation['startTime']);
$endMinute   = ($series->isNew())?"0":date("i",$series->reservation['endTime']);

$seriesEndMonth = ($series->isNew())?date("n"):date("n",$series->reservation['seriesEndDate']);
$seriesEndDay   = ($series->isNew())?date("j"):date("j",$series->reservation['seriesEndDate']);
$seriesEndYear  = ($series->isNew())?date("Y"):date("Y",$series->reservation['seriesEndDate']);

$localvars->set("username",$series->reservation['username']);
$localvars->set("email",$series->reservation['email']);
$localvars->set("groupname",$series->reservation['groupname']);
$localvars->set("comments",$series->reservation['comments']);
$localvars->set("action",($series->isNew())?"Add":"Update");

// Display time in 12 hour or 24 hour
$displayHour = getConfig('24hour');
$displayHour = ($displayHour != 1)?12:24;

$localvars->set("reservationID",($series->isNew())?"":$series->reservation['ID']);

// Building the building dropdown list
$building = new building;
$localvars->set("buildingSelectOptions",$building->selectBuildingListOptions(FALSE,(isset($_POST['MYSQL']['library']))?$_POST['MYSQL']['library']:NULL));

// Build the room Dropdown List
$room = new room;
if (isset($_POST['MYSQL']['library']) && !is_empty($_POST['MYSQL']['library'])) {
	$localvars->set("roomSelectOptions",$room->selectRoomListOptions(FALSE,$_POST['MYSQL']['library'],(isset($_POST['MYSQL']['room']))?$_POST['MYSQL']['room']:NULL));
}
else {
	$firstBuilding = array_shift($building->getall());
	$localvars->set("roomSelectOptions",$room->selectRoomListOptions(FALSE,$firstBuilding['ID']));
}

$date = new date;

// If there was a submission error, duration is what was submitted. 
// If we are loading, it needs calculated. 
$duration = $nextHour - $currentHour;

// @TODO display on month dropdown should be configurable via interface
$localvars->set("monthSelect", $date->dropdownMonthSelect(1,$currentMonth,array("name"=>"start_month", "id"=>"start_month")));
$localvars->set("daySelect",   $date->dropdownDaySelect($currentDay,array("name"=>"start_day", "id"=>"start_day")));
$localvars->set("yearSelect",  $date->dropdownYearSelect(0,10,$currentYear,array("name"=>"start_year", "id"=>"start_year")));
$localvars->set("shourSelect", $date->dropdownHourSelect(($displayHour == 12)?TRUE:FALSE,$currentHour,array("name"=>"start_hour", "id"=>"start_hour")));
$localvars->set("sminSelect",  $date->dropdownMinuteSelect("15",$startMinute,array("name"=>"start_minute", "id"=>"start_minute"))); // @TODO need to pull increment from room config
$localvars->set("ehourSelect", dropdownDurationSelect($duration,array("name"=>"end_hour", "id"=>"end_hour")));
$localvars->set("eminSelect",  $date->dropdownMinuteSelect("15",$endMinute,array("name"=>"end_minute", "id"=>"end_minute"))); // @TODO need to pull increment from room config

templates::display('header');
?>

<header>
<h1>{local var="action"} a Series Reservation</h1>
</header>

<?php if (count($engine->errorStack) > 0) { ?>
<section id="actionResults">
	<header>
		<h1>Results</h1>
	</header>
	<?php print errorHandle::prettyPrint(); ?>
</section>
<?php } ?>

<p>Adding a <em><strong>Series</strong></em> reservation</p>

<form action="{phpself query="true"}" method="post">
	{csrf}

	<input type="hidden" name="reservationID" value="{local var="reservationID"}" />

	<fieldset>
		<legend>User Information</legend>
		<label for="username" class="requiredField">Username:</label> &nbsp; <input type="text" id="username" name="username" value="{local var="username"}"/>
		<br />
		<label for="notificationEmail" class="requiredField">Email:</label> &nbsp; <input type="text" id="notificationEmail" name="notificationEmail" value="{local var="email"}" required/>
		<br />
		<label for="groupName">Groupname:</label> &nbsp; <input type="text" id="groupname" name="groupname" value="{local var="groupname"}"/>
	</fieldset>
	<br />

	<fieldset>
		<legend>Room Information</legend>
		<table>
			<tr>
				<td>
					<label for="listBuildingSelect">Building</label>
					<select name="library" id="listBuildingSelect">
						{local var="buildingSelectOptions"}
					</select>
				</td>
				<td>
					<label for="listBuildingRoomsSelect">Room</label>
					<select name="room" id="listBuildingRoomsSelect" data-anyroom="false">
						{local var="roomSelectOptions"}
					</select>
				</td>
			</tr>
		<tr>
			<th colspan="3" style="text-align: left;"><strong>Reservation Date:</strong></th>
		</tr>
		<tr>
			<td>
				<label for="start_month">Month:</label><br />
				{local var="monthSelect"}
			</td>
				<td>
				<label for="start_day">Day:</label><br />
				{local var="daySelect"}
			</td>

			<td>
				<label for="start_year">Year:</label><br />
				{local var="yearSelect"}
			</td>
			<td></td>
		</tr>
				<tr>
					<td colspan="2">
						<strong>Start Time</strong>
					</td>
				</tr>
		<tr>
			<td>
				<label for="start_hour">Hour:</label><br />
				{local var="shourSelect"}
			</td>
			<td>
				<label for="start_minute">Minute:</label><br />
				{local var="sminSelect"}
			</td>
				</tr>
				<tr>
					<td colspan="2">
						<strong>Duration</strong>
					</td>
				</tr>
				<tr>
			<td>
				<label for="end_hour">Hour:</label><br />
				{local var="ehourSelect"}
			</td>
			<td>
				<label for="end_minute">Minute:</label><br />
				{local var="eminSelect"}
			</td>
		</tr>
	</table>
</fieldset>

<br />

<fieldset>
	<legend>Series Information</legend>

	<label for="allDay">All Day:</label>
	<input type="checkbox" name="allDay" id="allDay" value="1" <?php print (($reservationInfo['allDay'] == "1")?"checked":"");?>/>
	<br />

	<label for="frequency">
		Frequency:
	</label>
	<select name="frequency" id="frequency">
		<option value="0" <?php print (($reservationInfo['frequency'] == "0")?"selected":"");?>>Every Day</option>
		<option value="1" <?php print (($reservationInfo['frequency'] == "1")?"selected":"");?>>Every Week</option>
		<option value="2" <?php print (($reservationInfo['frequency'] == "2")?"selected":"");?>>Every Month (Month Day)</option>
		<option value="3" <?php print (($reservationInfo['frequency'] == "3")?"selected":"");?>>Every Month (Week Day)</option>
	</select>

	<br />

	<p>Weekdays (<em>Only used when "Every Week" is selected.</em>)</p>
	<table>
		<tr>
			<th>
				<label for="sunday">Sunday</label>
			</th>
			<th>
				<label for="monday">Monday</label>
			</th>
			<th>
				<label for="tuesday">Tuesday</label>
			</th>
			<th>
				<label for="wednesday">Wednesday</label>
			</th>
			<th>
				<label for="thursday">Thursday</label>
			</th>
			<th>
				<label for="friday">Friday</label>
			</th>
			<th>
				<label for="saturday">Saturday</label>
			</th>
		</tr>
	<tr>
		<td> <input type="checkbox" name="weekday[]" value="0" id="sunday"    <?php print (isset($series->reservation['weekdaysAssigned']) && is_array($series->reservation['weekdaysAssigned']) && in_array("0",$series->reservation['weekdaysAssigned']))?"checked":""; ?>/></td>
		<td> <input type="checkbox" name="weekday[]" value="1" id="monday"    <?php print (isset($series->reservation['weekdaysAssigned']) && is_array($series->reservation['weekdaysAssigned']) && in_array("1",$series->reservation['weekdaysAssigned']))?"checked":""; ?>/></td>
		<td> <input type="checkbox" name="weekday[]" value="2" id="tuesday"   <?php print (isset($series->reservation['weekdaysAssigned']) && is_array($series->reservation['weekdaysAssigned']) && in_array("2",$series->reservation['weekdaysAssigned']))?"checked":""; ?>/></td>
		<td> <input type="checkbox" name="weekday[]" value="3" id="wednesday" <?php print (isset($series->reservation['weekdaysAssigned']) && is_array($series->reservation['weekdaysAssigned']) && in_array("3",$series->reservation['weekdaysAssigned']))?"checked":""; ?>/></td>
		<td> <input type="checkbox" name="weekday[]" value="4" id="thursday"  <?php print (isset($series->reservation['weekdaysAssigned']) && is_array($series->reservation['weekdaysAssigned']) && in_array("4",$series->reservation['weekdaysAssigned']))?"checked":""; ?>/></td>
		<td> <input type="checkbox" name="weekday[]" value="5" id="friday"    <?php print (isset($series->reservation['weekdaysAssigned']) && is_array($series->reservation['weekdaysAssigned']) && in_array("5",$series->reservation['weekdaysAssigned']))?"checked":""; ?>/></td>
		<td> <input type="checkbox" name="weekday[]" value="6" id="saturday"  <?php print (isset($series->reservation['weekdaysAssigned']) && is_array($series->reservation['weekdaysAssigned']) && in_array("6",$series->reservation['weekdaysAssigned']))?"checked":""; ?>/></td>
	</tr>
	</table>

	<br />
	<label for="seriesEndDate">
		Series Ends On:
	</label>
	<table>
		<tr>
			<td>
				<label for="seriesEndDate_month">Month:</label><br />
				<select name="seriesEndDate_month" id="seriesEndDate_month" >
					<?php

						for($I=1;$I<=12;$I++) {
							printf('<option value="%s" %s>%s</option>',
								($I < 10)?"0".$I:$I,
								($I == $seriesEndMonth)?"selected":"",
								$I);
						}
					?>
				</select>
			</td>
				<td>
				<label for="seriesEndDate_day">Day:</label><br />
				<select name="seriesEndDate_day" id="seriesEndDate_day" >
					<?php

						for($I=1;$I<=31;$I++) {
							printf('<option value="%s" %s>%s</option>',
								($I < 10)?"0".$I:$I,
								($I == $seriesEndDay)?"selected":"",
								$I);
						}
					?>
				</select>
			</td>
			<td>
				<label for="seriesEndDate_year">Year:</label><br />
				<select name="seriesEndDate_year" id="seriesEndDate_year" >
					<?php

						for($I=$currentYear;$I<=$currentYear+10;$I++) {
							printf('<option value="%s" %s>%s</option>',
								$I,
								($I == $seriesEndYear)?"selected":"",
								$I);
						}
					?>
				</select>
			</td>
		</tr>
	</table>

<p>
	Notes:
</p>
<ul>
	<li>Every Week: This is every 7 days. so, if the date falls on a Friday, the next schedule day will also be a Friday.</li>
	<li>Every Week: If you select week days, this overrides the above note. The first day selected should be the start date.</li>
	<li>Every Month (Month Day): Will schedule this event every month, on the same Month Day. If you schedule a room on the 13th, it will be on the 13th every month</li>
	<li>Every Month (Week Day): Will schedule this event every month, on the same Week Day. If you schedule a room on the second Friday of the month, it will be on the second Friday every month</li>
</ul>

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
	<?php 
	//@TODO this needs cleaned up
	if ($series->isNew()) { ?>
	<input type="submit" name="createSubmit" value="Reserve this Room"/> &nbsp;&nbsp;
	<?php } 
	else { ?>
	<input type="submit" name="deleteSubmit" value="Delete" id="deleteReservation"/>

	<?php }	?>

</form>


<?php
templates::display('footer');
?>
