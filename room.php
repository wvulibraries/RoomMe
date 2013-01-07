<?php
require_once("engineHeader.php");
recurseInsert("includes/functions.php","php");
recurseInsert("includes/createReservations.php","php");

$error      = FALSE;
$roomID = "";
if (!isset($engine->cleanGet['MYSQL']['room'])) {
	$error = TRUE;
	errorHandle::errorMsg("Invalid or missing room ID");
}
else {
	$roomID = $engine->cleanGet['MYSQL']['room'];
}

$room         = getRoomInfo($roomID);

if ($room !== FALSE && isset($room['building'])) {

	$roomPolicy   = getRoomPolicy($roomID);
	$buildingName = getBuildingName($room['building']);

	localvars::add("roomName",    $room['name']);
	localvars::add("roomNumber",  $room['number']);
	localvars::add("policyURL",   $room['policyURL']);
	localvars::add("username",    sessionGet("username"));
	localvars::add("buildingID",  $room['building']);
	localvars::add("roomID",      $room['ID']);
	localvars::add("buildingName",$buildingName);
	localvars::add("prettyPrint", errorHandle::prettyPrint());
	localvars::add("loginURL",    $engineVars['loginPage'].'?page='.$_SERVER['PHP_SELF']."&qs=".(urlencode($_SERVER['QUERY_STRING'])));
	localvars::add("mapURL",      $room['mapURL']);
	localvars::add("displayName", $room['displayName']);

}
else {

	$roomPolicy   = NULL;
	$buildingName = NULL;

	localvars::add("roomName",    "Error");
	localvars::add("roomNumber",  "Error");
	localvars::add("policyURL",   "Error");
	localvars::add("username",    sessionGet("username"));
	localvars::add("buildingID",  "Error");
	localvars::add("roomID",      "Error");
	localvars::add("buildingName","Error");
	localvars::add("prettyPrint", errorHandle::prettyPrint());
	localvars::add("loginURL",    $engineVars['loginPage'].'?page='.$_SERVER['PHP_SELF']."&qs=".(urlencode($_SERVER['QUERY_STRING'])));
	localvars::add("mapURL",      "Error");
	localvars::add("displayName", "Error");

}

$currentMonth = (!isset($engine->cleanGet['MYSQL']['reservationSTime']))?date("n"):date("n",$engine->cleanGet['MYSQL']['reservationSTime']);
$currentDay   = (!isset($engine->cleanGet['MYSQL']['reservationSTime']))?date("j"):date("j",$engine->cleanGet['MYSQL']['reservationSTime']);
$currentYear  = (!isset($engine->cleanGet['MYSQL']['reservationSTime']))?date("Y"):date("Y",$engine->cleanGet['MYSQL']['reservationSTime']);
$currentHour  = (!isset($engine->cleanGet['MYSQL']['reservationSTime']))?date("G"):date("G",$engine->cleanGet['MYSQL']['reservationSTime']);
$currentMin   = (!isset($engine->cleanGet['MYSQL']['reservationSTime']))?"00":date("i",$engine->cleanGet['MYSQL']['reservationSTime']);
$nextHour     = (!isset($engine->cleanGet['MYSQL']['reservationETime']))?(date("G")+1):date("G",$engine->cleanGet['MYSQL']['reservationETime']);
$nextMin      = (!isset($engine->cleanGet['MYSQL']['reservationSTime']))?"00":date("i",$engine->cleanGet['MYSQL']['reservationSTime']);

$sql        = sprintf("SELECT value FROM siteConfig WHERE name='24hour'");
$sqlResult  = $engine->openDB->query($sql);

$displayHour = 24;
if ($sqlResult['result']) {
	$row        = mysql_fetch_array($sqlResult['result'],  MYSQL_ASSOC);
	$displayHour = ($row['value'] == 1)?24:12;
}

if (isset($engine->cleanPost['MYSQL']['createSubmit'])) {

	$buildingID = $engine->cleanPost['MYSQL']['library'];
	$roomID     = $engine->cleanPost['MYSQL']['room'];

	createReservation($buildingID,$roomID);

	localvars::add("prettyPrint",errorHandle::prettyPrint());
}

localvars::add("policyLabel",getResultMessage("policyLabel"));

$engine->eTemplate("include","header");
?>

	{local var="prettyPrint"}

<header>
<h1>{local var="displayName"} in {local var="buildingName"}</h1>
</header>

<?php if ($roomPolicy['publicViewing'] == 1) { ?>

<a href="#" class="calendarModal_link" data-type="room" data-id="<?php print $roomID ?>">View Reservation Calendar &ndash; This Room</a><br />

<?php } ?>

<a href="building.php?building={local var="buildingID"}">Return to Building room listing</a>

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

		<?php if (isset($room['mapURL']) && !isempty($room['mapURL'])) { ?>
		<tr>
			<td><strong>Map:</strong></td>
			<td><a href="{local var="mapURL"}" class="mapModal_link">View Map</a></td>
		</tr>
		<?php } ?>

		<?php if (isset($room['policyURL']) && !isempty($room['policyURL'])) { ?>
		<tr>
			<td><strong>{local var="policyLabel"} Information:</strong></td>
			<td><a href="{local var="policyURL"}">{local var="policyURL"}</a></td>
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
							<a href="equipment.php?id=<?php print $equipment['ID']; ?>"><?php print $equipment['name']; ?></a>
						</li>
					<?php } ?>
				</ul>
			</td>
		</tr>
		<?php } ?>

	</table>

</section>



<section id="reservationsReserveRoom">

	<header>
		<h1>Reserve Room</h1>
	</header>

<!-- 	{local var="prettyPrint"} -->

<?php if(isset($roomPolicy['publicScheduling']) && $roomPolicy['publicScheduling']=="1") { // public scheduling?>

	<?php if(isempty(sessionGet("username"))) { ?>

	<p>You must be logged in to reserve a room. </p>
	<a href="{local var="loginURL"}">Login</a>

	<?php } else { ?>

<form action="{phpself query="true"}" method="post">
	{csrf insert="post"}

	<input type="hidden" name="library" value="{local var="buildingID"}" />
	<input type="hidden" name="room" value="{local var="roomID"}" />
	<input type="hidden" id="username" name="username" value="{local var="username"}"/>

	<table>
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
	
	<label name="notificationEmail">Email Address (<strong><em>optional, for email confirmation</em></strong>):</label>
	<input type="email" name="notificationEmail" id="notificationEmail" placeholder="" />
	<br /><br />
	
	<input type="submit" name="createSubmit" value="Reserve this Room" />
</form>
<?php } ?>
<?php } else { // public scheduling?>

	</p>To schedule this room you must contact the library.</p>

<?php } ?>
</section>


<div id="calendarModal">
</div>

<?php
$engine->eTemplate("include","footer");
?>