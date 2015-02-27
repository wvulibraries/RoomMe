<?php
require_once("engineHeader.php");

$errorMsg = "";
$error    = FALSE;

$db       = db::get($localvars->get('dbConnectionName'));

// we are editing a reservation
$reservationInfo = NULL;
$username        = "";
$groupname       = "";
$comments        = "";
$submitError     = FALSE;

$reservation = new Reservation;

try {

	// Is this an Update? 
	// Currently checking for this in both get and post. 
	if (isset($_GET['MYSQL']['id']) || (isset($_POST['MYSQL']['reservationID']) && !is_empty($_POST['MYSQL']['reservationID'])) ) {

		$reservationID = (isset($_POST['MYSQL']['reservationID']) && !is_empty($_POST['MYSQL']['reservationID']))?$_POST['MYSQL']['reservationID']:$_GET['MYSQL']['id'];

		if ($reservation->get($reservationID) === FALSE) {
			throw new Exception("Error retrieving reservation.");
		}

	}
	else {
		if (!isset($_POST['MYSQL']['library']) && validate::getInstance()->integer($_POST['MYSQL']['library']) === FALSE) {
			throw new Exception("Missing or invalid building");
		}
		if (!isset($_POST['MYSQL']['room']) && validate::getInstance()->integer($_POST['MYSQL']['room']) === FALSE) {
			throw new Exception("Missing or invalid room");
		}

		$reservation->setBuilding($_POST['MYSQL']['library']);
		$reservation->setRoom($_POST['MYSQL']['room']);

	}

	$localvars->set("buildingID",$reservation->building['ID']);
	$localvars->set("roomID",$reservation->room['ID']);
	$localvars->set("buildingName",$reservation->building['name']);
	$localvars->set("roomName",$reservation->room['name']);
	$localvars->set("roomNumber",$reservation->room['number']);


	if (isset($_POST['MYSQL']['roomSubmit'])) {

	// This comes from the room select page

	}
	else if (isset($_POST['MYSQL']['createSubmit'])) {

		if (!$reservation->create()) {
			throw new Exception("Error Creating Reservation.");
		}

	}
	else if (isset($_POST['MYSQL']['deleteSubmit'])) {

		if (!$reservation->delete()) {
			throw new Exception("Error deleting reservation.");
		}

		// @TODO this should not be hard coded. 
		header('Location: reservationsList.php');

	}

}
catch(Exception $e) {
	errorHandle::errorMsg($e->getMessage());

	// Setup to repopulate form on error
	$submitError = TRUE;
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
				(!$reservation->isNew() && $row['ID'] == $reservation->reservation['createdVia'])?"selected":"",
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

// Display time in 12 hour or 24 hour
$displayHour = getConfig('24hour');
$displayHour = ($displayHour != 1)?12:24;

// If this is a new reservation, use the current time. 
// If this is an update, use the time from the reservation
$currentMonth = ($reservation->isNew())?date("n"):date("n",$reservation->reservation['startTime']);
$currentDay   = ($reservation->isNew())?date("j"):date("j",$reservation->reservation['startTime']);
$currentYear  = ($reservation->isNew())?date("Y"):date("Y",$reservation->reservation['startTime']);
$currentHour  = ($reservation->isNew())?date("G"):date("G",$reservation->reservation['startTime']);
$nextHour     = ($reservation->isNew())?(date("G")+1):date("G",$reservation->reservation['endTime']);

$startMinute = ($reservation->isNew())?"0":date("i",$reservation->reservation['startTime']);
$endMinute   = ($reservation->isNew())?"0":date("i",$reservation->reservation['endTime']);

// Set some localvars for use in the HTML below. 
$localvars->set("username",($reservation->isNew())?"":$reservation->reservation['username']);
$localvars->set("groupname",($reservation->isNew())?"":$reservation->reservation['groupname']);
$localvars->set("comments",($reservation->isNew())?"":$reservation->reservation['comments']);
$localvars->set("action",($reservation->isNew())?"Add":"Update");
$localvars->set("reservationID",($reservation->isNew())?"":$reservation->reservation['ID']);
$localvars->set("submitText",($reservation->isNew())?"Reserve this Room":"Update Reservation");


if ($submitError) {

	$localvars->set("username",$_POST['HTML']['username']);
	$localvars->set("groupname",$_POST['HTML']['groupname']);
	$localvars->set("comments",$_POST['HTML']['comments']);

	$currentMonth = $_POST['HTML']['start_month'];
	$currentDay   = $_POST['HTML']['start_day'];
	$currentYear  = $_POST['HTML']['start_year'];
	$currentHour  = $_POST['HTML']['start_hour'];
	$nextHour     = $_POST['HTML']['end_hour'];

	$startMinute = $_POST['HTML']['start_minute'];
	$endMinute   = $_POST['HTML']['end_minute'];

}

templates::display('header');
?>

<header>
	<h1>{local var="action"} a Reservation</h1>
</header>

<?php if (count($engine->errorStack) > 0) {	?>
<section id="actionResults">
	<header>
		<h1>Results</h1>
	</header>
	<?php print errorHandle::prettyPrint(); ?>
</section>
<?php } ?>

	<p>Adding a reservation for Room <strong>{local var="roomName"} {local var="roomNumber"}</strong> in building <strong>{local var="buildingName"}</strong></p>

	<form action="{phpself query="true"}" method="post">
		{csrf}

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
					<th colspan="3" style="text-align: left;"><strong>Reservation Date:</strong></th>
				</tr>
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
						<strong>Start Time</strong>
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
								printf('<option value="%s" %s>%s</option>',
									($I < 10)?"0".$I:$I,
									($I == $startMinute)?"selected":"",
									$I);
							}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<strong>End Time</strong>
					</td>
				</tr>
				<tr>
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
								printf('<option value="%s" %s>%s</option>',
									($I < 10)?"0".$I:$I,
									($I == $endMinute)?"selected":"",
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

			<?php if (!$reservation->isNew() && $reservation->hasEmail()) { ?>

			<a href="email/?id={local var="reservationID"}">Email Patron</a><br /><br />

			<?php }	?>

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
		<input type="submit" name="createSubmit" value="{local var="submitText"}"/> &nbsp;&nbsp;

		<?php if (!$reservation->isNew()) { ?>

		<input type="submit" name="deleteSubmit" value="Delete" id="deleteReservation"/>

		<?php }	?>

	</form>




	<?php
	templates::display('footer');
	?>