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

// This is so broken :-/ 
if (isset($engine->cleanPost['MYSQL']['library'])) {
	$engine->cleanGet['MYSQL']['library'] = $engine->cleanPost['MYSQL']['library'];
	$engine->cleanGet['HTML']['library']  = $engine->cleanPost['HTML']['library'];
	$engine->cleanGet['RAW']['library']   = $engine->cleanPost['RAW']['library'];
	$engine->cleanGet['MYSQL']['room']    = $engine->cleanPost['MYSQL']['room'];
	$engine->cleanGet['HTML']['room']     = $engine->cleanPost['HTML']['room'];
	$engine->cleanGet['RAW']['room']      = $engine->cleanPost['RAW']['room'];
}

// We have an edit instead of a new page
if (isset($engine->cleanGet['MYSQL']['id']) && validate::integer($engine->cleanGet['MYSQL']['id']) === TRUE) {

	$reservationID = $engine->cleanGet['MYSQL']['id'];
	localvars::add("reservationID",$reservationID);
	$sql       = sprintf("SELECT seriesReservations.*, building.ID as buildingID FROM `seriesReservations` LEFT JOIN `rooms` ON rooms.ID=seriesReservations.roomID LEFT JOIN `building` ON building.ID=rooms.building WHERE seriesReservations.ID='%s'",
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
		$engine->cleanPost['MYSQL']['library'] = $reservationInfo['buildingID'];
		$engine->cleanPost['HTML']['library']  = $reservationInfo['buildingID'];
		$engine->cleanPost['RAW']['library']   = $reservationInfo['buildingID'];
		$engine->cleanPost['MYSQL']['room']    = $reservationInfo['roomID'];
		$engine->cleanPost['HTML']['room']     = $reservationInfo['roomID'];
		$engine->cleanPost['RAW']['room']      = $reservationInfo['roomID'];
		$engine->cleanGet['MYSQL']['library'] = $reservationInfo['buildingID'];
		$engine->cleanGet['HTML']['library']  = $reservationInfo['buildingID'];
		$engine->cleanGet['RAW']['library']   = $reservationInfo['buildingID'];
		$engine->cleanGet['MYSQL']['room']    = $reservationInfo['roomID'];
		$engine->cleanGet['HTML']['room']     = $reservationInfo['roomID'];
		$engine->cleanGet['RAW']['room']      = $reservationInfo['roomID'];

		$action = "Update";

		$weekdaysAssigned = array();
		if (!isempty($reservationInfo['weekdays'])) {
			$weekdaysAssigned = unserialize($reservationInfo['weekdays']);
		}

	}

}


if (!isset($engine->cleanGet['MYSQL']['library']) || validate::integer($engine->cleanGet['MYSQL']['library']) === FALSE) {
	$errorMsg .= errorHandle::errorMsg("Missing or invalid building");
	$error = TRUE;
}
if (!isset($engine->cleanGet['MYSQL']['room']) || validate::integer($engine->cleanGet['MYSQL']['room']) === FALSE) {
	$errorMsg .= errorHandle::errorMsg("Missing or invalid room");
	$error = TRUE;
}



if ($error === FALSE) {

	$buildingID = $engine->cleanGet['MYSQL']['library'];
	$roomID     = $engine->cleanGet['MYSQL']['room'];

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

$currentMonth = (isnull($reservationInfo))?date("n"):date("n",$reservationInfo['startTime']);
$currentDay   = (isnull($reservationInfo))?date("j"):date("j",$reservationInfo['startTime']);
$currentYear  = (isnull($reservationInfo))?date("Y"):date("Y",$reservationInfo['startTime']);
$currentHour  = (isnull($reservationInfo))?date("G"):date("G",$reservationInfo['startTime']);
$nextHour     = (isnull($reservationInfo))?(date("G")+1):date("G",$reservationInfo['endTime']);

$seriesEndMonth = (isnull($reservationInfo))?date("n"):date("n",$reservationInfo['seriesEndDate']);
$seriesEndDay   = (isnull($reservationInfo))?date("j"):date("j",$reservationInfo['seriesEndDate']);
$seriesEndYear  = (isnull($reservationInfo))?date("Y"):date("Y",$reservationInfo['seriesEndDate']);

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


if (isset($engine->cleanPost['MYSQL']['createSubmit'])) {

	$schedule = array();

	$weekdays = array(FALSE,FALSE,FALSE,FALSE,FALSE,FALSE,FALSE);
	if (isset($engine->cleanPost['MYSQL']['weekday'])) {
		foreach ($engine->cleanPost['MYSQL']['weekday'] as $I=>$V) {
			$weekdays[$V] = TRUE;
		}
	}

	$allDay    = (isset($engine->cleanPost['MYSQL']['allDay']) && $engine->cleanPost['MYSQL']['allDay'] == "1")?TRUE:FALSE; 
	$frequency = $engine->cleanPost['MYSQL']['frequency'];

	if ($allDay === TRUE) {
		$engine->cleanPost['MYSQL']['start_hour']   = "0";
		$engine->cleanPost['MYSQL']['start_minute'] = "0";
		$engine->cleanPost['MYSQL']['end_hour']     = "23";
		$engine->cleanPost['MYSQL']['end_minute']   = "59";
	}

	$startTime     = mktime($engine->cleanPost['MYSQL']['start_hour'],$engine->cleanPost['MYSQL']['start_minute'],0,$engine->cleanPost['MYSQL']['start_month'],$engine->cleanPost['MYSQL']['start_day'],$engine->cleanPost['MYSQL']['start_year']);
	$endTime       = mktime($engine->cleanPost['MYSQL']['end_hour'],$engine->cleanPost['MYSQL']['end_minute'],0,$engine->cleanPost['MYSQL']['start_month'],$engine->cleanPost['MYSQL']['start_day'],$engine->cleanPost['MYSQL']['start_year']);
	$startDay      = mktime(0,0,0,$engine->cleanPost['MYSQL']['start_month'],$engine->cleanPost['MYSQL']['start_day'],$engine->cleanPost['MYSQL']['start_year']);
	$seriesEndDate = mktime(0,0,0,$engine->cleanPost['MYSQL']['seriesEndDate_month'],$engine->cleanPost['MYSQL']['seriesEndDate_day'],$engine->cleanPost['MYSQL']['seriesEndDate_year']);


	// print "startTime: <pre>";
	// var_dump($startTime);
	// print "</pre>";

	// print "endTime<pre>";
	// var_dump($endTime);
	// print "</pre>";

	// print "seriesEndDate<pre>";
	// var_dump($seriesEndDate);
	// print "</pre>";

	// print "weekdays<pre>";
	// var_dump($weekdays);
	// print "</pre>";

	// print "allDay<pre>";
	// var_dump($allDay);
	// print "</pre>";

	// print "Frequency<pre>";
	// var_dump($frequency);
	// print "</pre>";

	// if "Every Day" is the freuency, error when weekdays are selected
	if ($frequency === "0" && in_array(TRUE,$weekdays)) {
		$errorMsg .= errorHandle::errorMsg("Cannot select Everyday as a frequency and select specific days of the week");
		$error     = TRUE;
	}
	if (($frequency =="2" || $frequency == "3")&& in_array(TRUE,$weekdays)) {
		$errorMsg .= errorHandle::errorMsg("Cannot select Every Month as a frequency and select specific days of the week");
		$error     = TRUE;
	}
	if ($seriesEndDate < $startDay) {
		$errorMsg .= errorHandle::errorMsg("Series End Date cannot be before the start time.");
		$error     = TRUE;
	}
	if ($seriesEndDate == $startDay || ($seriesEndDate > $startTime && $seriesEndDate < $endTime)) {
		$errorMsg .= errorHandle::errorMsg("Series end date is the same day as start time, please create a normal reservation");
		$error     = TRUE;
	}

	if ($error === FALSE) {

		// Everyday
		if ($frequency == "0") {

			$schedule = getSchedule($startTime,$endTime,$startDay,$seriesEndDate,"+1 day");

		}
		// every week
		else if ($frequency == "1") {

			// no weekdays are selected
			if (!in_array(TRUE,$weekdays)) {
				$schedule = getSchedule($startTime,$endTime,$startDay,$seriesEndDate,"+1 week");
			}

			// weekdays are selected
			else {

				$dateInfo = getdate($startTime);

				foreach ($weekdays as $I=>$V) {
					if ($V === TRUE) {
						if ($dateInfo['wday'] > $I) {
							$interval = 7 - $dateInfo['wday'] + $I;
							$interval = "+".$interval." days";

							$startDayTemp       = strtotime($interval,$startDay);
							$startTimeTemp      = strtotime($interval,$startTime);
							$endTimeTemp        = strtotime($interval,$endTime);

						}
						else if ($dateInfo['wday'] < $I) {
							$interval = "+".($I - $dateInfo['wday'])." days";

							$startDayTemp       = strtotime($interval,$startDay);
							$startTimeTemp      = strtotime($interval,$startTime);
							$endTimeTemp        = strtotime($interval,$endTime);
						}
						else { // equal	
						}
						$temp = getSchedule($startTimeTemp,$endTimeTemp,$startDayTemp,$seriesEndDate,"+1 week");
						$schedule = array_merge($schedule,$temp);
					}
				}
			}	

		}
		// Every Month (Month Day) 
		else if ($frequency == "2") {

			$schedule = getSchedule($startTime,$endTime,$startDay,$seriesEndDate,"+1 Month");

		}
		// Every Month (Week Day) 
		else if ($frequency == "3") {

			$interval = "";

			$weekdayOccurence = getWeekdayOccurrence($startTime);
			// $weekdayOccurence = array("1","Sunday");
			switch ($weekdayOccurence[0]) {
				case 1: 
					$interval = "first";
					break;
				case 2:
					$interval = "second";
					break;
				case 3: 
					$interval = "third";
					break;
				case 4:
					$interval = "forth";
					break;
				case 5: 
					$interval = "fifth";
					break;
			}
			$intervalStart = $interval." ".lc($weekdayOccurence[1])." +".$engine->cleanPost['MYSQL']['start_hour']."hours +".$engine->cleanPost['MYSQL']['start_minute']."minutes" ;
			$intervalEnd   = $interval." ".lc($weekdayOccurence[1])." +".$engine->cleanPost['MYSQL']['end_hour']."hours +".$engine->cleanPost['MYSQL']['end_minute']."minutes" ;

			$startDay = mktime(0,0,0,$engine->cleanPost['MYSQL']['start_month'],1,$engine->cleanPost['MYSQL']['start_year']);

			// $startDay       = strtotime($interval,$startDay);
			// print "<p>TEST: ".(date("F j, Y, g:i a",$startDay))."</p>";


			$count       = 0;
			$startTime_1 = 0;
			while($startTime_1 <= $seriesEndDate) {

				$startTime_1 = strtotime($intervalStart,$startDay);
				$endTime_1   = strtotime($intervalEnd,$startDay);

				if ($startTime_1 > $seriesEndDate) {
					break;
				}

				$schedule[] = array(
					'startTime' => $startTime_1,
					'endTime'   => $endTime_1
					);

				$startDay = strtotime("next month",$startDay);

			}

			// $schedule = getScheduleMonthWeek($startTime,$endTime,$startDay,$seriesEndDate,$interval);
		}
	

	// print "Schedule: <pre>";
	// var_dump($schedule);
	// print "</pre>";

	// turn on transactions
	$transResult = $engine->openDB->transBegin("reservations");

	if ($transResult === TRUE) {

		$submissionError = FALSE;
		$seriesID        = NULL;

		recurseInsert("includes/getUserInfo.php","php"); 
		$userInformation = getUserInfo($engine->cleanPost['MYSQL']['username']);    

		if ($userInformation !== FALSE) {


			// put the serial information in the serial table
			$sql       = sprintf("INSERT INTO seriesReservations (`createdOn`,`createdBy`,`createdVia`,`roomID`,`startTime`,`endTime`,`modifiedOn`,`modifiedBy`,`username`,`initials`,`groupname`,`comments`,`allDay`,`frequency`,`weekdays`,`seriesEndDate`) VALUES('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s')",

				$engine->openDB->escape(time()),
				$engine->openDB->escape(sessionGet("username")),
				$engine->cleanPost['MYSQL']['via'],
				$engine->openDB->escape($roomID),
				$engine->openDB->escape($startTime),
				$engine->openDB->escape($endTime),
				$engine->openDB->escape(time()),
				$engine->openDB->escape(sessionGet("username")),
				$engine->cleanPost['MYSQL']['username'],
				$userInformation['initials'],
				$engine->cleanPost['MYSQL']['groupname'],
				$engine->cleanPost['MYSQL']['comments'],
				(isset($engine->cleanPost['MYSQL']['allDay']))?"1":"0",
				$engine->cleanPost['MYSQL']['frequency'],
				(isset($engine->cleanPost['MYSQL']['weekday']))?serialize($engine->cleanPost['MYSQL']['weekday']):"",
				$engine->openDB->escape($seriesEndDate)
				);
			$sqlResult = $engine->openDB->query($sql);

			$seriesID = $sqlResult['id'];

		}
		else {
			$engine->openDB->transRollback();
			$engine->openDB->transEnd();
			errorHandle::errorMsg(getResultMessage("invalidUsername"));
			$error     = TRUE;
		}
	}

		if ($error === FALSE && !isnull($seriesID)) {
			foreach ($schedule as $I=>$V) {
				// print "<p>".(date("F j, Y, g:i a",$V['startTime']))."</p>";
				// print "<p>".(date("F j, Y, g:i a",$V['endTime']))."</p>";
				// print "<p>--</p>";	

				// set all the needed posted variables
				$engine->cleanPost['MYSQL']['start_month']  = date("m",$V['startTime']);
				$engine->cleanPost['MYSQL']['start_day']    = date("d",$V['startTime']);
				$engine->cleanPost['MYSQL']['start_year']   = date("Y",$V['startTime']);

				$engine->cleanPost['MYSQL']['start_hour']   = date("H",$V['startTime']);
				$engine->cleanPost['MYSQL']['start_minute'] = date("i",$V['startTime']);

				$engine->cleanPost['MYSQL']['end_hour']     = date("H",$V['endTime']);
				$engine->cleanPost['MYSQL']['end_minute']   = date("i",$V['endTime']);

				// submit the reservation
				$reservationReturn = createReservation($buildingID,$roomID,$seriesID);

				// check the return value. If false, roll back the transactions and stop looping. 
				if ($reservationReturn === FALSE) {
					$submissionError = TRUE;
					break;
				}

			}
		}

		if ($submissionError !== FALSE) {
			// roll back the transaction
			$engine->openDB->transRollback();
			$engine->openDB->transEnd();

			// set an error message
			$errorMsg .= errorHandle::errorMsg("Failed create series reservation.");
			$error     = TRUE;
		}
		else {
			// end the transaction and commit it
			$engine->openDB->transCommit();
			$engine->openDB->transEnd();
		}
	}
	else {
		// Transaction failed to start

		$errorMsg .= errorHandle::errorMsg("Failed to begin database transaction. Please contact administrator.");
		$error     = TRUE;

	}



// library
// room
// reservationID
// username
// groupname	
// via
// override
// comments
// seriesEndDate_year
// seriesEndDate_day
// seriesEndDate_month
// weekday[]
// frequency
// allDay
// start_month
// start_day
// start_year
// start_hour
// start_minute
// end_hour
// end_minute

} // submit create
else if (isset($engine->cleanPost['MYSQL']['deleteSubmit'])) {

	$transResult = $engine->openDB->transBegin("reservations");

	$sql       = sprintf("DELETE FROM `reservations` WHERE seriesID='%s' AND startTime>'%s'",
		$engine->cleanPost['MYSQL']['reservationID'],
		time()
		);
	$sqlResult = $engine->openDB->query($sql);

	if (!$sqlResult['result']) {
		$engine->openDB->transRollback();
		$engine->openDB->transEnd();
		$error = TRUE;
		errorHandle::newError(__METHOD__."() - ".$sqlResult['error'], errorHandle::DEBUG);
		errorHandle::errorMsg("Error deleting series reservation.");
	}
	else {

		$sql       = sprintf("DELETE FROM `seriesReservations` WHERE ID='%s'",
			$engine->cleanPost['MYSQL']['reservationID']
			);
		$sqlResult = $engine->openDB->query($sql);

		if (!$sqlResult['result']) {
			$engine->openDB->transRollback();
			$engine->openDB->transEnd();
			errorHandle::successMsg("Series Reservation Deleted.");
		}
		else {
			$engine->openDB->transCommit();
			$engine->openDB->transEnd();
			header('Location: seriesList.php');
		}

	}



}

function getSchedule($startTime,$endTime,$startDay,$seriesEndDate,$interval) {

	$schedule = array();

	$workingDay       = 0;
	$workingStartTime = 0;
	$workingEndTime   = 0;
	while ($workingDay <= $seriesEndDate) {
		if ($workingDay == 0) {
			$schedule[] = array(
				'startTime' => $startTime,
				'endTime'   => $endTime
				);

			$workingDay       = strtotime($interval,$startDay);
			$workingStartTime = strtotime($interval,$startTime);
			$workingEndTime   = strtotime($interval,$endTime);

			continue;
		}

		$schedule[] = array(
			'startTime' => $workingStartTime,
			'endTime'   => $workingEndTime
			);

		$workingDay       = strtotime($interval,$workingDay);
		$workingStartTime = strtotime($interval,$workingStartTime);
		$workingEndTime   = strtotime($interval,$workingEndTime);
	}

	return($schedule);

}

function getScheduleMonthWeek($startTime,$endTime,$startDay,$seriesEndDate,$interval) {
	$schedule = array();

	$workingDay       = 0;
	$workingStartTime = 0;
	$workingEndTime   = 0;
	while ($workingDay <= $seriesEndDate) {
		if ($workingDay == 0) {
			$schedule[] = array(
				'startTime' => $startTime,
				'endTime'   => $endTime
				);

			$workingDay       = strtotime("+1 Month",$startDay);
			$workingStartTime = strtotime("+1 Month",$startTime);
			$workingEndTime   = strtotime("+1 Month",$endTime);		

			$workingDay       = strtotime($interval,$startDay);
			$workingStartTime = strtotime($interval,$startTime);
			$workingEndTime   = strtotime($interval,$endTime);

			continue;
		}

		$schedule[] = array(
			'startTime' => $workingStartTime,
			'endTime'   => $workingEndTime
			);


		$workingDay       = strtotime("+1 Month",$workingDay);
		$workingStartTime = strtotime("+1 Month",$workingStartTime);
		$workingEndTime   = strtotime("+1 Month",$workingEndTime);		

		$workingDay       = strtotime($interval,$workingDay);
		$workingStartTime = strtotime($interval,$workingStartTime);
		$workingEndTime   = strtotime($interval,$workingEndTime);
	}

	return($schedule);
}

function getWeekdayOccurrence($time) { 
    $month = intval(date("m", $time)); $day = intval(date("d", $time)); 
    for ($i = 0; $i < 7; $i++) { 
        $days[] = date("l", mktime(0, 0, 0, $month, ($i+1), date("Y", $time)));    
    } 

    $posd  = array_search(date("l", $time), $days); 
    $posdm = array_search($days[0], $days) - $posd; 

    return array((($day+$posdm+6)/7), $days[$posd]);        
} 

$engine->eTemplate("include","header");
?>

<header>
<h1>{local var="action"} a Series Reservation</h1>
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

<p>Adding a <em><strong>Series</strong></em> reservation for Room <strong>{local var="roomName"}</strong> in building <strong>{local var="buildingName"}</strong></p>

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

	<p>Weekdays</p> 

	<table>
		<tr>
			<th>
				<label for="sunday">Sunday:</label>
			</th>
			<th>
				<label for="monday">Monday:</label>
			</th>
			<th>
				<label for="tuesday">Tuesday:</label>
			</th>
			<th>
				<label for="wednesday">Wednesday:</label>
			</th>
			<th>
				<label for="thursday">Thursday:</label>
			</th>
			<th>
				<label for="friday">Friday:</label>
			</th>
			<th>
				<label for="saturday">Saturday:</label>
			</th>
		</tr>
	<tr>
		<td> <input type="checkbox" name="weekday[]" value="0" id="sunday" <?php print (in_array("0",$weekdaysAssigned))?"checked":""; ?>/></td>
		<td> <input type="checkbox" name="weekday[]" value="1" id="monday" <?php print (in_array("1",$weekdaysAssigned))?"checked":""; ?>/></td>
		<td> <input type="checkbox" name="weekday[]" value="2" id="tuesday" <?php print (in_array("2",$weekdaysAssigned))?"checked":""; ?>/></td>
		<td> <input type="checkbox" name="weekday[]" value="3" id="wednesday" <?php print (in_array("3",$weekdaysAssigned))?"checked":""; ?>/></td>
		<td> <input type="checkbox" name="weekday[]" value="4" id="thursday" <?php print (in_array("4",$weekdaysAssigned))?"checked":""; ?>/></td>
		<td> <input type="checkbox" name="weekday[]" value="5" id="friday" <?php print (in_array("5",$weekdaysAssigned))?"checked":""; ?>/></td>
		<td> <input type="checkbox" name="weekday[]" value="6" id="saturday" <?php print (in_array("6",$weekdaysAssigned))?"checked":""; ?>/></td>
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
	<?php if (isnull($reservationInfo)) { ?>
	<input type="submit" name="createSubmit" value="Reserve this Room"/> &nbsp;&nbsp;
	<?php } ?>

	<?php if (!isnull($reservationInfo)) { ?>

	<input type="submit" name="deleteSubmit" value="Delete" id="deleteReservation"/>

	<?php }	?>

</form>


<?php
$engine->eTemplate("include","footer");
?>