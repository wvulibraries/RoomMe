<?php
require_once("engineHeader.php");
recurseInsert("includes/functions.php","php");

$error  = FALSE;
$roomID = "";
$month  = "";
$day    = "";
$year   = "";
if (!isset($_GET['MYSQL']['building']) && !isset($_GET['MYSQL']['room'])) {
	$error = TRUE;
	$buildingID = "";
	errorHandle::errorMsg("Invalid or missing ID");
}
else {
	if (isset($_GET['MYSQL']['building'])) {
		$buildingID = $_GET['MYSQL']['building'];
		$calType = "building";
		$buildingName = getBuildingName($buildingID);
		$localvars->set("id",$buildingID);
		$localvars->set("buildingName",$buildingName);
	}
	else if (isset($_GET['MYSQL']['room'])) {
		$roomID = $_GET['MYSQL']['room'];
		$calType = "room";
		$roomName     = getRoomName($roomID);
		$localvars->set("id",$roomID);
		$localvars->set("roomName",$roomName);
	}

	$localvars->set("calType",$calType);
	// set the date that we will be displaying
	// grab it from the query string, if that is set, otherwise the current date
	if (isset($_GET['MYSQL']['month'])) {
		$month = $_GET['MYSQL']['month'];
	}
	else {
		$month = date("n");
	}
	if (isset($_GET['MYSQL']['day'])) {
		$day = $_GET['MYSQL']['day'];
	}
	else {
		$day = date("d");
	}
	if (isset($_GET['MYSQL']['year'])) {
		$year = $_GET['MYSQL']['year'];
	}
	else {
		$year = date("Y");
	}

	$localvars->set("month",$month);
	$localvars->set("day",$day);
	$localvars->set("year",$year);

	$currentMonth = (!isset($_GET['MYSQL']['reservationSTime']))?date("n"):date("n",$_GET['MYSQL']['reservationSTime']);
	$currentDay   = (!isset($_GET['MYSQL']['reservationSTime']))?date("j"):date("j",$_GET['MYSQL']['reservationSTime']);
	$currentYear  = (!isset($_GET['MYSQL']['reservationSTime']))?date("Y"):date("Y",$_GET['MYSQL']['reservationSTime']);

	// setup the variables for the buttons

	// get the previous Day
	$localvars->set("prevMonth",date("n",strtotime("-1 day",mktime(0,0,0,$month,$day,$year))));
	$localvars->set("prevDay",date("d",strtotime("-1 day",mktime(0,0,0,$month,$day,$year))));
	$localvars->set("prevYear",date("Y",strtotime("-1 day",mktime(0,0,0,$month,$day,$year))));

	// get the current (today) day
	$localvars->set("todayMonth",date("n"));
	$localvars->set("todayDay",date("d"));
	$localvars->set("todayYear",date("Y"));

	// get the next Day
	$localvars->set("nextMonth",date("n",strtotime("+1 day",mktime(0,0,0,$month,$day,$year))));
	$localvars->set("nextDay",date("d",strtotime("+1 day",mktime(0,0,0,$month,$day,$year))));
	$localvars->set("nextYear",date("Y",strtotime("+1 day",mktime(0,0,0,$month,$day,$year))));

	// Get the Room IDs that we will be displaying
	if ($calType == "building") {
		$sql = sprintf("SELECT ID FROM `rooms` WHERE `building`='%s' ORDER BY `name`",
			$engine->openDB->escape($buildingID)
			);

		$engine->openDB->sanitize = FALSE;
		$sqlResult                = $engine->openDB->query($sql);

		if ($sqlResult->error()) {
			errorHandle::newError(__METHOD__."() - ".$sqlResult['error'], errorHandle::DEBUG);
			$error = TRUE;
		}
		else {
			while($row = $sqlResult->fetch()) {
				$roomID[] = $row['ID'];
			}
		}
	}

}




$localvars->set("calendarName",($calType == "building")?$buildingName:$roomName)

// $engine->eTemplate("include","header");
?>

<span class="simplemodal-close" id="closeModalCalendar"><img src="images/closeIcon.png" title="close"/></span>
<header>
	<h1>{local var="calendarName"} Calendar</h1>
	<h2>{local var="month"} / {local var="day"} / {local var="year"}</h2>
</header>

<br />

<section>

		<select name="start_month_modal" id="start_month_modal" style="width: 75px;">
			<?php
			for($I=1;$I<=12;$I++) {
				printf('<option value="%s" %s>%s</option>',
					$I,
					($I == $month)?"selected":"",
					$I);
			}
			?>
		</select>

		<select name="start_day_modal" id="start_day_modal" style="width: 75px;">
		<?php
		for($I=1;$I<=31;$I++) {
			printf('<option value="%s" %s>%s</option>',
				($I < 10)?"0".$I:$I,
				($I == $day)?"selected":"",
				$I);
		}
		?>
		</select>

		<select name="start_year_modal" id="start_year_modal" style="width: 75px;">
		<?php
		for($I=$currentYear;$I<=$currentYear+10;$I++) {
			printf('<option value="%s" %s>%s</option>',
				$I,
				($I == $year)?"selected":"",
				$I);
		}
		?>
		</select>

		<button id="calUpdateFormSubmit" data-type="{local var="calType"}" data-id="{local var="id"}" style="margin-top: -8px">Jump to Date</button>



				<br />

	<button class="calUpdateButton" id="prevDayButton"  data-type="{local var="calType"}" data-id="{local var="id"}" data-month="{local var="prevMonth"}"  data-day="{local var="prevDay"}"  data-year="{local var="prevYear"}">&lt;&lt; Previous</button>
	<button class="calUpdateButton" id="todayButton"    data-type="{local var="calType"}" data-id="{local var="id"}" data-month="{local var="todayMonth"}" data-day="{local var="todayDay"}" data-year="{local var="todayYear"}">Today</button>
	<button class="calUpdateButton" id="nextDayButton"  data-type="{local var="calType"}" data-id="{local var="id"}" data-month="{local var="nextMonth"}"  data-day="{local var="nextDay"}"  data-year="{local var="nextYear"}">Next &gt;&gt;</button>
	<br /><br />
	<?php print drawRoomCalendar($roomID,array($month,$day,$year)); ?>

</section>

<footer>
	<h1>{local var="calendarName"} Calendar</h1>
	<h2>{local var="month"} / {local var="day"} / {local var="year"}</h2>
</footer>

<script type="text/javascript">
$('#closeModalCalendar').live('click',handler_closeModal);

</script>

<?php
// $engine->eTemplate("include","footer");
?>