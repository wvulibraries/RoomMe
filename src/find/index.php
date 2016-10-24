<?php
require_once("../engineHeader.php");
recurseInsert("includes/functions.php","php");

$results = "";

$currentMonth = (!isset($_GET['MYSQL']['reservationSTime']))?date("n"):date("n",$_GET['MYSQL']['reservationSTime']);
$currentDay   = (!isset($_GET['MYSQL']['reservationSTime']))?date("j"):date("j",$_GET['MYSQL']['reservationSTime']);
$currentYear  = (!isset($_GET['MYSQL']['reservationSTime']))?date("Y"):date("Y",$_GET['MYSQL']['reservationSTime']);
$currentHour  = (!isset($_GET['MYSQL']['reservationSTime']))?date("G"):date("G",$_GET['MYSQL']['reservationSTime']);
$currentMin   = (!isset($_GET['MYSQL']['reservationSTime']))?"00":date("i",$_GET['MYSQL']['reservationSTime']);
$nextHour     = (!isset($_GET['MYSQL']['reservationETime']))?(date("G")+1):date("G",$_GET['MYSQL']['reservationETime']);
$nextMin      = (!isset($_GET['MYSQL']['reservationSTime']))?"00":date("i",$_GET['MYSQL']['reservationSTime']);

// generate library list
$db        = db::get($localvars->get('dbConnectionName'));
$sql       = sprintf("SELECT * FROM `building` ORDER BY `name`");
$sqlResult = $db->query($sql);
$options = "";
while ($row = $sqlResult->fetch()) {
	$options .= sprintf('<option value="%s">%s</option>',
		htmlSanitize($row['ID']),
		htmlSanitize($row['name']));
}

$localvars->set("librarySelectOptions",$options);

$displayHour = (getConfig("24hour") == "1")?"24":"12";

if (isset($_POST['MYSQL']['lookupSubmit'])) {

	$error = FALSE;

	$month = $_POST['MYSQL']['start_month'];
	$day   = $_POST['MYSQL']['start_day'];
	$year  = $_POST['MYSQL']['start_year'];

	$shour = $_POST['MYSQL']['start_hour'];
	$smin  = $_POST['MYSQL']['start_minute'];

	$ehour = $_POST['MYSQL']['end_hour'];
	$emin  = $_POST['MYSQL']['end_minute'];

	$capacity = $_POST['MYSQL']['capacity'];

	// check to see if the provided date is valid
	$validDate = checkdate($month,$day,$year);
	if ($validDate === FALSE) {
		errorHandle::errorMsg($messages->get("invalidDate"));
		$error = TRUE;
	}

	if ($error === FALSE) {
		$sUnix = mktime($shour,$smin,0,$month,$day,$year);

		$ehour = $ehour * 60 * 60;
		$emin  = $emin  * 60;

		$eUnix = $sUnix + $ehour + $emin;

		// make sure the end time is after the start time
		if ($eUnix <= $sUnix) {
			errorHandle::errorMsg($messages->get("endBeforeStart"));
			$error = TRUE;
		}
	}

	if ($error === FALSE) {

		$sql       = sprintf("SELECT * FROM building WHERE ID=?");
		$sqlResult = $db->query($sql,array($_POST['MYSQL']['library']));
		$building  = $sqlResult->fetch();

		$sql = sprintf("SELECT rooms.*,
       building.roomlistdisplay AS roomListDisplay
FROM   rooms
       LEFT JOIN building
              ON building.id = rooms.building
       LEFT JOIN roomTemplates
              ON roomTemplates.id = rooms.roomtemplate
       LEFT JOIN policies
              ON policies.id = roomTemplates.policy
WHERE  policies.publicscheduling = '1'
       AND rooms.building = ?
			 AND rooms.capacity >= ?
       AND rooms.id NOT IN (SELECT rooms.id
                        FROM   rooms
                               LEFT JOIN reservations
                                      ON reservations.roomid = rooms.id
                        WHERE  (
                                    (
                                         ( ? <  reservations.startTime AND ? > reservations.endTime  )
                                         OR
                                         ( ? <= reservations.startTime AND (? >  reservations.startTime AND ? <= reservations.endTime))
                                         OR
                                         ( (? >=  reservations.startTime AND ? < reservations.endTime) AND ? >= reservations.endTime )
                                         OR
                                         ( ? >=  reservations.startTime AND ? <= reservations.endTime )
                                    )
                                    AND rooms.building = ?
                               )
                        )
ORDER  BY rooms.%s",

			$building['roomSortOrder']
			);
		$sqlResult = $db->query($sql,array($_POST['MYSQL']['library'],$_POST['MYSQL']['capacity'],$sUnix,$eUnix,$sUnix,$eUnix,$eUnix,$sUnix,$sUnix,$eUnix,$sUnix,$eUnix,$_POST['MYSQL']['library']));

		if ($sqlResult->error()) {
			errorHandle::newError($sqlResult->errorMsg(), errorHandle::DEBUG);
			$results = errorHandle::errorMsg("Error searching database");
		}
		else {

			if ($sqlResult->rowCount() == 0) {
				$results = "No Rooms found!";
			}
			else {
				$results = "<ul>";
				while($row = $sqlResult->fetch()) {

					$displayName = str_replace("{name}", $row['name'], $row['roomListDisplay']);
					$displayName = str_replace("{number}", $row['number'], $displayName);

					$results .= sprintf('<li><a href="%s/building/room/?room=%s&reservationSTime=%s&reservationETime=%s">%s</a></li>',
						$localvars->get("roomReservationHome"),
						htmlSanitize($row['ID']),
						htmlSanitize($sUnix),
						htmlSanitize($eUnix),
						htmlSanitize($displayName)
						);
				}
				$results .= "</ul><br></br>";
			}
		}

		$localvars->set("results",$results);
	}
}

$localvars->set("prettyPrint",errorHandle::prettyPrint());

templates::display('header');
?>

<section>

	{local var="results"}

</section>

<h3 class="roomH3" style="display: inline-block;">Find a Room</h3>

<!-- Extra Links -->
<a class="policyLink roomTabletDesktop" href="{local var="advancedSearch"}">Advanced Search <i class="fa fa-cog"></i></a>
<a class="policyLink3 roomTabletDesktop" href="{local var="policiesPage"}">Reservation Policies
	<i class="fa fa-exclamation-circle"></i>
</a>
<a class="policyLink roomTabletDesktop" href="{local var="helpPage"}">Help
	<i class="fa fa-question-circle"></i>
</a>

<hr class="roomHR roomTabletDesktop" />

{local var="prettyPrint"}

<p>Select a date and time with the form below to see a list of rooms that are available at your desired time</p>


<form action="{phpself query="true"}" method="post">
	{csrf}

	<strong>Select Library:</strong>
	<div class="roomReservationRows">
		<span class="reserveRoomInput"><label for="library">Library:</label>
		<select name="library" id="library" class="library">
			{local var="librarySelectOptions"}
		</select></span>
	</div>

	<strong>Select The Date:</strong>
	<div class="roomReservationRows">
		<span class="reserveRoomInput"><label for="start_month">Month:</label>
		<select name="start_month" id="start_month" >
			<?php
				for($I=1;$I<=12;$I++) {
					printf('<option value="%s" %s>%s</option>',
						($I < 10)?"0".$I:$I,
						($I == $currentMonth)?"selected":"",
						$I);
				}
			?>
		</select></span>
		<span class="reserveRoomInput"><label for="start_day">Day:</label>
		<select name="start_day" id="start_day" >
			<?php
				for($I=1;$I<=31;$I++) {
					printf('<option value="%s" %s>%s</option>',
						($I < 10)?"0".$I:$I,
						($I == $currentDay)?"selected":"",
						$I);
				}
			?>
		</select></span>
		<span class="reserveRoomInput"><label for="start_year">Year:</label>
		<select name="start_year" id="start_year" >
			<?php
				for($I=$currentYear;$I<=$currentYear+10;$I++) {
					printf('<option value="%s">%s</option>',
						$I,
						$I);
				}
			?>
		</select></span>
	</div>

	<strong>Select The Start Time:</strong>
	<div class="roomReservationRows">
		<span class="reserveRoomInput"><label for="start_hour">Hour:</label>
		<select name="start_hour" id="start_hour" >
			<?php
				for($I=0;$I<=23;$I++) {
					printf('<option value="%s" %s>%s</option>',
						($I < 10)?"0".$I:$I,
						($I == $currentHour)?"selected":"",
						($displayHour == 24)?$I:(($I==12)?"12pm":(($I>=13)?($I-12)."pm":(($I == 0)?"12am":$I."am"))));
				}
			?>
		</select></span>
		<span class="reserveRoomInput"><label for="start_minute">Minute:</label>
		<select name="start_minute" id="start_minute" >
			<?php
				for($I=0;$I<60;$I += 15) {
					printf('<option value="%s">%s</option>',
						($I < 10)?"0".$I:$I,
						$I);
				}
			?>
		</select></span>
	</div>

	<strong>Select The Duration:</strong>
	<div class="roomReservationRows">
		<span class="reserveRoomInput"><label for="end_hour">Hour:</label>
		<select name="end_hour" id="end_hour" >
			<?php
				for($I=0;$I<=23;$I++) {
					printf('<option value="%s">%s</option>',
						$I,
						$I
						);
				}
			?>
		</select></span>
		<span class="reserveRoomInput"><label for="end_minute">Minute:</label>
		<select name="end_minute" id="end_minute" >
			<?php
				for($I=0;$I<60;$I += 15) {
					printf('<option value="%s">%s</option>',
						$I,
						$I);
				}
			?>
		</select></span>
	</div>

	<strong>Enter The Maximum Capacity:</strong>
	<div class="roomReservationRows">
		<span class="reserveRoomInput"><label for="capacity">Capacity:</label>
			<select name="capacity" id="capacity" class="capacity">
		   <option value="*">Any Capacity</option>
		 </select>
	</div>

	<br>
	<input type="submit" name="lookupSubmit" class="button" />
</form>

<div id="calendarModal">
</div>

	<div class="clear:both;"></div>
	<br>

<!-- Rooms Navigation -->
<?php recurseInsert("includes/roomsByBuilding.php","php") ?>

<?php
templates::display('footer');
?>

<script type="text/javascript" src="{local var="roomResBaseDir"}/javascript/roomCapacity.js"></script>
