<?php
require_once("engineHeader.php");
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
$sqlResult = $DB->query($sql);
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

	// check to see if the provided date is valid
	$validDate = checkdate($month,$day,$year);
	if ($validDate === FALSE) {
		errorHandle::errorMsg(getResultMessage("invalidDate"));
		$error = TRUE;
	}

	if ($error === FALSE) {
		$sUnix = mktime($shour,$smin,0,$month,$day,$year);

		if ((int)$shour >= 18 && (int)$ehour < (int)$shour) {
			// assume the end hour is the next morning/day

			// add 24 hours of seconds to the start time
			$nextDay = $sunix + 86400;

			// grab the new month, day, year
			$emonth = date("n",$nextDay);
			$eday   = date("j",$nextDay);
			$eyear  = date("Y",$nextDay);

			$eUnix = mktime($ehour,$emin,0,$emonth,$eday,$eyear);

		}
		else {
			// otherwise just use what we were given
			$eUnix = mktime($ehour,$emin,0,$month,$day,$year);
		}


		// make sure the end time is after the start time
		if ($eUnix <= $sUnix) {
			errorHandle::errorMsg(getResultMessage("endBeforeStart"));
			$error = TRUE;
		}
	}

	if ($error === FALSE) {

		$sql       = sprintf("SELECT * FROM building WHERE ID=?");
		$sqlResult = $db->query($sql,array($_POST['MYSQL']['library']));
		$building  = $sqlResult->fetch();


		$sql       = sprintf("SELECT rooms.*, building.roomListDisplay as roomListDisplay FROM rooms LEFT JOIN building ON building.ID=rooms.building LEFT JOIN roomTemplates ON roomTemplates.ID=rooms.roomTemplate LEFT JOIN policies ON policies.ID=roomTemplates.policy WHERE policies.publicScheduling='1' AND rooms.building=? AND rooms.ID NOT IN (SELECT rooms.ID FROM rooms LEFT JOIN reservations ON reservations.roomID=rooms.ID WHERE (((startTime<=? AND endTime>?) OR (startTime<? AND endTime>=?)) OR (startTime>=? AND endTime<=?)) AND rooms.building=?) ORDER BY rooms.%s",
		#$sql       = sprintf("SELECT rooms.*, building.roomListDisplay as roomListDisplay FROM rooms LEFT JOIN building ON building.ID=rooms.building LEFT JOIN roomTemplates ON roomTemplates.ID=rooms.roomTemplate LEFT JOIN policies ON policies.ID=roomTemplates.policy WHERE policies.publicScheduling='1' AND rooms.building='%s' AND rooms.ID NOT IN (SELECT * FROM `reservations` WHERE ( ((startTime<='%s' AND endTime>'%s') OR (startTime<'%s' AND endTime>='%s')) OR (startTime>='%s' AND endTime<='%s') ) AND roomID='%s') ORDER BY rooms.%s",
			$building['roomSortOrder']
			);
		$sqlResult = $db->query($sql,array($_POST['MYSQL']['library'],$sUnix,$sUnix,$eUnix,$eUnix,$sUnix,$eUnix,$_POST['MYSQL']['library']));



		if ($sqlResult->error()) {
			errorHandle::newError(__METHOD__."() - ".$sqlResult['error'], errorHandle::DEBUG);
			$results = errorHandle::errorMsg("Error searching database");
		}
		else {

			if ($sqlResult->rowCount() == 0) {
				print "<pre>";
				var_dump($sql);
				print "</pre>";
				$results = errorHandle::errorMsg("No Rooms found!");
			}
			else {
				$results = "<ul>";
				while($row = $sqlResult->fetch()) {

					$displayName = str_replace("{name}", $row['name'], $row['roomListDisplay']);
					$displayName = str_replace("{number}", $row['number'], $displayName);

					$results .= sprintf('<li><a href="room.php?room=%s&reservationSTime=%s&reservationETime=%s">%s</a></li>',
						htmlSanitize($row['ID']),
						htmlSanitize($sUnix),
						htmlSanitize($eUnix),
						htmlSanitize($displayName)
						);
				}
				$results .= "</ul>";
			}
		}

		$localvars->set("results",$results);
	}

}

templates::display('header');
?>

<header>
<h1>Find a Room</h1>
</header>

<p>Select a date and time with the form below to see a list of rooms that are available at your desired time</p>


<form action="{phpself query="true"}" method="post">
	{csrf insert="post"}

	<label for="library">Library:</label>
	<select name="library" id="library">
		{local var="librarySelectOptions"}
	</select>

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
								($displayHour == 24)?$I:(($I==12)?"12pm":(($I>=13)?($I-12)."pm":(($I == 0)?"12am":$I."am")))
								);
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
	
	
	<input type="submit" name="lookupSubmit" />
</form>

<?php if (!is_empty($results)) { ?>

<section>
	<header>
		<h1>Rooms Available at the Requested Day and Time</h1>
	</header>

	{local var="results"}

</section>

<?php } ?>

<div id="calendarModal">
</div>

<?php
templates::display('footer');
?>