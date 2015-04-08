<?php
require_once("../../engineHeader.php");
recurseInsert("includes/functions.php","php");
recurseInsert("includes/createReservations.php","php");

$snippet = new Snippet("pageContent","content");

$error      = FALSE;
$roomID = "";
if (!isset($_GET['MYSQL']['room'])) {
	$error = TRUE;
	errorHandle::errorMsg("Invalid or missing room ID");
}
else {
	$roomID = $_GET['MYSQL']['room'];
}

$room         = getRoomInfo($roomID);

if ($room !== FALSE && isset($room['building'])) {

	$roomObj      = new room;
	$roomPolicy   = getRoomPolicy($roomID);
	$buildingName = getBuildingName($room['building']);

	$userinfo = new userInfo();
	if ($userinfo->get(session::get("username"))) {
		$localvars->set("useremail",   $userinfo->user['email']);
	}

	$localvars->set("roomName",    htmlSanitize($room['name']));
	$localvars->set("roomNumber",  htmlSanitize($room['number']));
	$localvars->set("policyURL",   htmlSanitize($room['policyURL']));
	$localvars->set("username",    session::get("username"));
	$localvars->set("buildingID",  htmlSanitize($room['building']));
	$localvars->set("roomID",      htmlSanitize($room['ID']));
	$localvars->set("buildingName",htmlSanitize($buildingName));
	$localvars->set("prettyPrint", errorHandle::prettyPrint());
	$localvars->set("loginURL",    $engineVars['loginPage'].'?page='.$_SERVER['PHP_SELF']."&qs=".(urlencode($_SERVER['QUERY_STRING'])));
	$localvars->set("mapURL",      htmlSanitize($room['mapURL']));
	$localvars->set("displayName", htmlSanitize($room['displayName']));

	$localvars->set("roomPicture", $roomObj->getPicture($room['ID']));

}
else {

	$roomPolicy   = NULL;
	$buildingName = NULL;

	$localvars->set("roomName",    "Error");
	$localvars->set("roomNumber",  "Error");
	$localvars->set("policyURL",   "Error");
	$localvars->set("username",    session::get("username"));
	$localvars->set("buildingID",  "Error");
	$localvars->set("roomID",      "Error");
	$localvars->set("buildingName","Error");
	$localvars->set("prettyPrint", errorHandle::prettyPrint());
	$localvars->set("loginURL",    $engineVars['loginPage'].'?page='.$_SERVER['PHP_SELF']."&qs=".(urlencode($_SERVER['QUERY_STRING'])));
	$localvars->set("mapURL",      "Error");
	$localvars->set("displayName", "Error");

}

$currentMonth = (!isset($_GET['MYSQL']['reservationSTime']))?date("n"):date("n",$_GET['MYSQL']['reservationSTime']);
$currentDay   = (!isset($_GET['MYSQL']['reservationSTime']))?date("j"):date("j",$_GET['MYSQL']['reservationSTime']);
$currentYear  = (!isset($_GET['MYSQL']['reservationSTime']))?date("Y"):date("Y",$_GET['MYSQL']['reservationSTime']);
$currentHour  = (!isset($_GET['MYSQL']['reservationSTime']))?date("G"):date("G",$_GET['MYSQL']['reservationSTime']);
$currentMin   = (!isset($_GET['MYSQL']['reservationSTime']))?"00":date("i",$_GET['MYSQL']['reservationSTime']);
$nextHour     = (!isset($_GET['MYSQL']['reservationETime']))?(date("G")+1):date("G",$_GET['MYSQL']['reservationETime']);
$nextMin      = (!isset($_GET['MYSQL']['reservationSTime']))?"00":date("i",$_GET['MYSQL']['reservationSTime']);

$sql        = sprintf("SELECT value FROM siteConfig WHERE name='24hour'");
$sqlResult  = $db->query($sql);

$displayHour = 24;
if (!$sqlResult->error()) {
	$row        = $sqlResult->fetch();
	$displayHour = ($row['value'] == 1)?24:12;
}

if (isset($_POST['MYSQL']['createSubmit'])) {

	$buildingID = $_POST['MYSQL']['library'];
	$roomID     = $_POST['MYSQL']['room'];

	$reservation = new Reservation;
	$reservation->setBuilding($buildingID);
	$reservation->setRoom($roomID);

	$reservation->create();

	$localvars->set("prettyPrint",errorHandle::prettyPrint());
}

$localvars->set("policyLabel",htmlSanitize(getResultMessage("policyLabel")));

templates::display('header');
?>

	{local var="prettyPrint"}

<header>
<h1>{local var="displayName"} in {local var="buildingName"}</h1>
</header>


<?php if ($roomPolicy['publicViewing'] == 1) { ?>

<!-- This is no longer neccesary w/new table -->
<!-- <a href="#" class="calendarModal_link" data-type="room" data-id="<?php print $roomID ?>">View Reservation Calendar &ndash; This Room</a><br /> -->

<?php } ?>

<a href="{local var="roomReservationHome"}/building/?building={local var="buildingID"}">View This Building's Entire Room Listing</a>

<section id="reservationsRoomInformation">

	<header>
		<h1>Room Information</h1>
	</header>


	<table>
		<tr>
			<td><strong>Room Name:</strong></td>
			<td>{local var="roomName"}</td>
		</tr>
		<tr>
			<td><strong>Room Number:</strong></td>
			<td>{local var="roomNumber"}</td>
		</tr>
		<tr>
			<td><strong>Building:</strong></td>
			<td>{local var="buildingName"}</td>
		</tr>

		<?php if (isset($room['mapURL']) && !is_empty($room['mapURL'])) { ?>
		<tr>
			<td><strong>Map:</strong></td>
			<td><a href="{local var="mapURL"}" class="mapModal_link">View Map</a></td>
		</tr>
		<?php } ?>

		<?php if (isset($room['policyURL']) && !is_empty($room['policyURL'])) { ?>
		<tr>
			<td><strong>{local var="policyLabel"} Information:</strong></td>
			<td><a href="{local var="policyURL"}">View Policies</a></td>
		</tr>
		<?php } ?>

		<?php if (count($room['equipment']) > 0) { ?>
		<tr>
			<td><strong>Equipment:</strong></td>
			<td>
				<ul>
					<?php 
						foreach ($room['equipment'] as $I=>$equipment) { 
					?>
						<li>
							<a href="equipment/?id=<?php print htmlSanitize($equipment['ID']); ?>"><?php print htmlSanitize($equipment['name']); ?></a>
						</li>
					<?php } ?>
				</ul>
			</td>
		</tr>
		<?php } ?>

	</table>

<div id="roomPictureContainer">
	{local var="roomPicture"}
</div>


</section>



<section id="reservationsReserveRoom">

	<header>
		<h1>Reserve Room</h1>
	</header>

<!-- 	{local var="prettyPrint"} -->

<?php if(isset($roomPolicy['publicScheduling']) && $roomPolicy['publicScheduling']=="1") { // public scheduling?>

	<?php if(is_empty(session::get("username"))) { ?>

	<p>You must be logged in to reserve a room. </p>
	<a href="{local var="loginURL"}">Login</a>

	<?php } else { ?>

<form action="{phpself query="true"}" method="post">
	{csrf}

	<input type="hidden" name="library" value="{local var="buildingID"}" />
	<input type="hidden" name="room" value="{local var="roomID"}" />
	<input type="hidden" id="username" name="username" value="{local var="username"}"/>

	<table>
						<tr>
					<th colspan="3" style="text-align: left;"><strong>Reservation Date:</strong></th>
				</tr>
		<tr>
			<td id="montDayYearSelects">
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
			
		</tr>
				<tr>
					<td colspan="2">
						<strong>Start Time</strong>
					</td>
				</tr>
		<tr id="startEndTimeSelects">	
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
								($I == $currentMin)?"selected":"",
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
								($I == $nextMin)?"selected":"",
								$I);
						}
					?>
				</select>
			</td>
		</tr>
	</table>
	<br />
	<label for="openEvent">Is this an open, public, event?</label><br />
	<select name="openEvent" id="openEvent"><option value="0">No</option><option value="1">Yes</option></select><br />
	<div id="openEventDescriptionContainer"  style="display:none;">
		<label for="openEventDescription">Describe your event:</label><br />
		<textarea id="openEventDescription" name="openEventDescription"></textarea>
	</div>
	<br /><br />
	<label name="notificationEmail" class="requiredField" >Email Address:</label>
	<input type="email" name="notificationEmail" id="notificationEmail" placeholder="" value="{local var="useremail"}" required />
	<br /><br />
	
	<input type="submit" name="createSubmit" class="button" value="Reserve this Room" />
</form>
<?php } ?>
<?php } else { // public scheduling?>


	{snippet id="8" field="content"}
	
<?php } ?>
</section>


<div id="calendarModal">
</div>

<?php
templates::display('footer');
?>