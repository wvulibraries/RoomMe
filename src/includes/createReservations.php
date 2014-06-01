<?php

function createReservation($buildingID,$roomID,$seriesID=NULL) {

	if (!function_exists("getBuildingName")) {
		recurseInsert("includes/functions.php","php");
	}

	$engine = EngineAPI::singleton();

	$validate = validateRoomPostVariables();

	if ($validate === FALSE) {
		errorHandle::errorMsg(getResultMessage("dataValidationError"));
		return(FALSE);
	}

	$via               = "Public Interface";
	$override          = "0";
	$groupname         = "";
	$comments          = "";
	$reservationUpdate = FALSE;
	// If the fields are set AND we are coming from the staff interface, we can modify $via and $override
	if (isset($_POST['MYSQL']['via']) && (preg_match('/\/admin\/reservationCreate\.php/',$_SERVER['PHP_SELF']) || preg_match('/\/admin\/seriesCreate\.php/',$_SERVER['PHP_SELF']))) {
		$via      = $_POST['MYSQL']['via'];
		$override = $_POST['MYSQL']['override'];

		if (isset($_POST['MYSQL']['groupname']) && !isempty($_POST['MYSQL']['groupname'])) {
			$groupname = $_POST['MYSQL']['groupname'];
		}
		if (isset($_POST['MYSQL']['comments']) && !isempty($_POST['MYSQL']['comments'])) {
			$comments = $_POST['MYSQL']['comments'];
		}

		if (isset($_POST['MYSQL']['reservationID']) && !isempty($_POST['MYSQL']['reservationID'])) {
			$reservationUpdate = $_POST['MYSQL']['reservationID'];
		}
	}

	// Username -- this will be hidden on the public form, entry on the staff interface
	$username = $_POST['MYSQL']['username'];

	// verify that the username is real and get the initials of the user
	recurseInsert("includes/getUserInfo.php","php"); // This file should be modified an have a function "getUserInfo"
	$userInformation = getUserInfo($username);         // takes a username and returns an array with info on success or FALSE on failure

	if ($userInformation === FALSE) {
		errorHandle::errorMsg(getResultMessage("invalidUsername"));
		return(FALSE);
	}

	// convert the start time to unix 

	// convert the end time to unix
	// -- First make sure it is the same day. 
	// -- If the end hour is less than the start hour, and the start hour is greater than 18 assume they
	// --- Mean the next morning, otherwise error
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
		return(FALSE);
	}

	$sUnix = mktime($shour,$smin,0,$month,$day,$year);

	if ((int)$shour >= 18 && (int)$ehour < (int)$shour) {
		// assume the end hour is the next morning/day

		// add 24 hours of seconds to the start time to get the next day
		$nextDay = $sUnix + 86400;

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
		return(FALSE);
	}

	// is this a reservation being requested in the past?
	// @TODO This needs to be configurable, time before current when reservation is not allowed.
	// We may even want to beak it off into a separate check for better error message input. 
	if (isnull($seriesID) && $sUnix < (time() - 3600)) {
		errorHandle::errorMsg(getResultMessage("reservationInPast"));
		return(FALSE);
	}

	// check for a duplicate reservation
	if (duplicateReservationCheck($username,$roomID,$sUnix,$eUnix) !== FALSE) {
		errorHandle::errorMsg(getResultMessage("duplicateReservation"));
		return(FALSE);
	}

	// check for a conflict with another reservation
	if (conflictReservationCheck($roomID,$sUnix,$eUnix) !== FALSE) {
		errorHandle::errorMsg(getResultMessage("reservationConflict"));
		return(FALSE);
	}

	// determine the total number of hours the room is being requested
	$totalTime  = $eUnix - $sUnix;
	$totalHours = (float)($totalTime / 60 / 60);

	// Get System, library, and Policy information

	$sql = sprintf("SELECT policies.*, building.hoursRSS as hoursRSS, building.fineLookupURL as fineLookupURL, building.fineAmount as building_fineAmount, building.maxHoursAllowed as building_maxHoursAllowed, building.period as building_period, building.bookingsAllowedInPeriod as building_bookingsAllowedInPeriod FROM rooms LEFT JOIN roomTemplates ON rooms.roomTemplate=roomTemplates.ID LEFT JOIN `policies` ON roomTemplates.policy = policies.ID LEFT JOIN building ON rooms.building = building.ID WHERE rooms.ID='%s' LIMIT 1",
		$_POST['MYSQL']['room']);

	$engine->openDB->sanitize = FALSE;
	$sqlResult                = $engine->openDB->query($sql);

	if (!$sqlResult['result']) {
		errorHandle::newError(__METHOD__."() - ".$sqlResult['error'], errorHandle::DEBUG);
		errorHandle::errorMsg(getResultMessage("policyError"));
		return(FALSE);
	}

	$row                   = mysql_fetch_array($sqlResult['result'],  MYSQL_ASSOC);

	$libraryPeriod         = $row['building_period'];
	$libraryMaxBookings    = $row['building_bookingsAllowedInPeriod'];
	$libraryMaxHours       = $row['building_maxHoursAllowed'];
	$libraryMaxFine        = $row['building_fineAmount'];
	$libraryfineLookupURL  = $row['fineLookupURL'];
	$libraryHoursURL       = $row['hoursRSS'];

	$policyID                  = $row['ID'];
	$policyPeriod              = $row['period'];
	$policyMaxBookings         = $row['bookingsAllowedInPeriod'];
	$policyMaxHours            = $row['hoursAllowed'];
	$policyHoursPerBooking     = $row['maxLoanLength'];
	$policyAllowWithFine       = $row['allowWithFines'];
	$policyMaxFine             = $row['fineAmount'];
	$policySameDayReservations = $row['sameDayReservations'];
	$policyFutureScheduleLen   = $row['futureScheduleLength'];



	// check to see If they are allowed to book the room while having fines (based on policy) set the policy amount to 0 (undefined);
	if ($policyAllowWithFine == "1") {
		$policyMaxFine = 0;
	}

	$sql       = sprintf("SELECT * FROM siteConfig");
	$sqlResult = $engine->openDB->query($sql);

	if (!$sqlResult['result']) {
		errorHandle::newError(__METHOD__."() - ".$sqlResult['error'], errorHandle::DEBUG);
		errorHandle::errorMsg(getResultMessage("systemsPolicyError"));
		return(FALSE);
	}

	while($row       = mysql_fetch_array($sqlResult['result'],  MYSQL_ASSOC)) {

		switch($row['name']) {
			case "periodSystem":
			$systemPeriod = $row['value'];
			break;
			case "maxHoursAllowedSystem":
			$systemMaxHours = $row['value'];
			break;
			case "maxBookingsAllowedSystem":
			$systemMaxBookings = $row['value'];
			break;
			case "maxFineAllowedSystem":
			$systemMaxFine = $row['value'];
			break;
		}

	}
	
	// Set the current period. Which ever period is the most restrictive is the period that we will use
	// $currentPeriod = 0;
	// if ($systemPeriod > 0 && $systemPeriod <= $libraryPeriod && $systemPeriod <= $policyPeriod) {
	// 	$currentPeriod = $systemPeriod;
	// }
	// else if ($libraryPeriod > 0 && $libraryPeriod <= $systemPeriod && $libraryPeriod <= $policyPeriod) {
	// 	$currentPeriod = $libraryPeriod;
	// }
	// else if ($policyPeriod > 0 && $policyPeriod <= $systemPeriod && $policyPeriod <= $libraryPeriod) {
	// 	$currentPeriod = $policyPeriod;
	// }

	$currentPeriod = 0;
	if ($systemPeriod > 0) {
		$currentPeriod = $systemPeriod;
	}
	if ($libraryPeriod > 0 && $currentPeriod == 0) {
		$currentPeriod = $libraryPeriod;
	}
	else if ($libraryPeriod > 0 && $currentPeriod > 0 && $libraryPeriod <= $currentPeriod) {
		$currentPeriod = $libraryPeriod;
	}
	if ($policyPeriod > 0 && $currentPeriod == 0) {
		$currentPeriod = $policyPeriod;
	}
	else if ($policyPeriod > 0 && $currentPeriod > 0 && $policyPeriod <= $currentPeriod) {
		$currentPeriod = $policyPeriod;
	}

	// Convert the current Period from Days into seconds
	if ($currentPeriod > 0) {
		$currentPeriod = $currentPeriod * 24 * 60 * 60;
	}

	// set the current mac fine ammount. Which ever max fine amount is more restrive is what we will use
	$currentFineAmount = 0;
	if ($systemMaxFine > 0) {
		$currentFineAmount = $systemMaxFine;
	}
	if ($libraryMaxFine > 0 && $currentFineAmount == 0) {
		$currentFineAmount = $libraryMaxFine;
	}
	else if ($libraryMaxFine > 0 && $currentFineAmount > 0 && $libraryMaxFine <= $currentFineAmount) {
		$currentFineAmount = $libraryMaxFine;
	}
	if ($policyMaxFine > 0 && $currentFineAmount == 0) {
		$currentFineAmount = $policyMaxFine;
	}
	else if ($policyMaxFine > 0 && $currentFineAmount > 0 && $policyMaxFine <= $currentFineAmount) {
		$currentFineAmount = $policyMaxFine;
	}

	// prepopulate the counts array with all buildings and policies
	$counts                         = array();
	$counts['hours']                = array();
	$counts['bookings']             = array();
	$counts['hours']['building']    = array();
	$counts['bookings']['building'] = array();
	$counts['hours']['policy']      = array();
	$counts['bookings']['policy']   = array();
	$counts['hours']['total']       = 0;
	$counts['bookings']['total']    = 0;
 
	$sql       = sprintf("SELECT * FROM building");
	$sqlResult = $engine->openDB->query($sql);

	if (!$sqlResult['result']) {
		errorHandle::newError(__METHOD__."() - ".$sqlResult['error'], errorHandle::DEBUG);
		errorHandle::errorMsg("Error retrieving buildings");
		return(FALSE);
	}

	while ($row       = mysql_fetch_array($sqlResult['result'],  MYSQL_ASSOC)) {
		$counts['hours']['building'][$row['ID']]    = 0;
		$counts['bookings']['building'][$row['ID']] = 0;
	}

	$sql       = sprintf("SELECT * FROM policies");
	$sqlResult = $engine->openDB->query($sql);

	if (!$sqlResult['result']) {
		errorHandle::newError(__METHOD__."() - ".$sqlResult['error'], errorHandle::DEBUG);
		errorHandle::errorMsg("Error retrieving buildings");
		return(FALSE);
	}

	while ($row       = mysql_fetch_array($sqlResult['result'],  MYSQL_ASSOC)) {
		$counts['hours']['policy'][$row['ID']]    = 0;
		$counts['bookings']['policy'][$row['ID']] = 0;
	}

	// get patron information
	$sql = sprintf("SELECT reservations.roomID as roomID, reservations.startTime as startTime, reservations.endTime as endTime, building.ID as buildingID, roomTemplates.policy as policyID FROM reservations LEFT JOIN rooms ON reservations.roomID=rooms.ID LEFT JOIN building ON rooms.building=building.ID LEFT JOIN roomTemplates ON rooms.roomTemplate=roomTemplates.ID WHERE username='%s' AND reservations.endTime>='%s' AND reservations.startTime<='%s'",
		lc($username),
		$sUnix - ($currentPeriod/2), // 1/2 period backwards
		$eUnix + ($currentPeriod/2) // 1/2 period forwards
		);

// // Debugging
// print "<pre>";
// var_dump($sql);
// print "</pre>";
// print "sunix<pre>";
// var_dump($sUnix);
// print "</pre>";
// print "eunix<pre>";
// var_dump($eUnix);
// print "</pre>";
// print "period<pre>";
// var_dump($currentPeriod);
// print "</pre>";
// print "sUnix -<pre>";
// var_dump($sUnix - ($currentPeriod/2));
// print "</pre>";
// print "eUnix +<pre>";
// var_dump($eUnix + ($currentPeriod/2));
// print "</pre>";
// return(FALSE);


	$sqlResult  = $engine->openDB->query($sql);

	if (!$sqlResult['result']) {
		errorHandle::newError(__METHOD__."() - ".$sqlResult['error'], errorHandle::DEBUG);
		errorHandle::errorMsg(getResultMessage("patronReservationInfo"));
		return(FALSE);
	}


	while ($row = mysql_fetch_array($sqlResult['result'],  MYSQL_ASSOC)) {

		$bookedHours = (float)(($row['endTime'] - $row['startTime']) / 60 / 60);

		// //
		// determine number of hours patron has booked in this building

		// if (!isset($counts['hours']['building'])) {
		// 	// initialize the building array if needed
		// 	$counts['hours']['building']    = array();
		// 	$counts['bookings']['building'] = array();
		// 	$counts['hours']['policy']      = array();
		// 	$counts['bookings']['policy']   = array();
		// }
		// if (!isset($counts['hours']['building'][$row['buildingID']])) {
		// 	// set the current library count to 0 if it hasn't been set yet
		// 	$counts['hours']['building'][$row['buildingID']] = $totalHours;
		// }

		// add the current hours
		$counts['hours']['building'][$row['buildingID']] += $bookedHours;

		// determine number of hours patron has booked in this policy
		// if (!isset($counts['hours']['policy'][$row['policyID']])) {
		// 	$counts['hours']['policy'][$row['policyID']] = $totalHours;
		// }
		
		$counts['hours']['policy'][$row['policyID']] += $bookedHours;
		

		// determine total number of hours patron has booked
		// if (!isset($counts['hours']['total'])) {
		// 	$counts['hours']['total'] = $totalHours;
		// }
		
		$counts['hours']['total'] += $bookedHours;
		

		// determine number of bookings the patron as in this building
		// if (!isset($counts['bookings']['building'][$row['buildingID']])) {
		// 	$counts['bookings']['building'][$row['buildingID']] = 1;
		// }
		
		$counts['bookings']['building'][$row['buildingID']]++;
		

		// determine number of hours patron has in this policy
		// if (!isset($counts['bookings']['policy'][$row['policyID']])) {
		// 	$counts['bookings']['policy'][$row['policyID']] = 1;
		// }
		
		$counts['bookings']['policy'][$row['policyID']]++;
		
		// determine total number of bookings paron has booked
		// if (!isset($counts['bookings']['total'])) {
		// 	$counts['bookings']['total'] = 1;
		// }
		
		$counts['bookings']['total']++;

	} // While Loop

	// Do all of the checks to see if the user can create a reservation. IF override isn't set. 
	if ($override == "0") {

		// Check to see if a user is allowed to book multiple rooms at the same time. If no
		// stop them
		$allowMultipleBookings = getConfig('allowMultipleBookings');
		if ($allowMultipleBookings == "0") {
			if (multipleBooksings($username,$sUnix,$eUnix)) {
				errorHandle::errorMsg(getResultMessage("multipleRoomBookings"));
				return(FALSE);
			}
		}

		// query the system to determine how much money the user owes
		if ($currentFineAmount > 0) {
			// call voyager
			// this should be a URL that returns a number
			$usersFineAmount = file_get_contents($libraryfineLookupURL.$userInformation['idNumber']);

			if ($usersFineAmount >= $currentFineAmount) {
				$resultFineMessage = getResultMessage("maxFineExceeded");
				$resultFineMessage = preg_replace("/{amount}/", $currentFineAmount, $resultFineMessage);
				errorHandle::errorMsg(getResultMessage($resultFineMessage));
				return(FALSE);
			}
		}

		// check against library hours

		// get hours from the RSS feed. 
		// if the RSS feed is unavailable, assume the library will be open (should this be configurable?)
		if (!isempty($libraryHoursURL)) {

			$opts = array(
				'http'=>array(
					'method'=> "GET",
					'header'=>  "Accept-language: en\r\n" .
								"Accept: text/plain\r\n" .
								"User-Agent: EngineAPI -- Room Reservation Software\r\n"
					)
				);

			$context = stream_context_create($opts);

			$hoursInfo = file_get_contents($libraryHoursURL.$sUnix, false, $context);
			$hoursInfo = explode("|",$hoursInfo);

			// // debugging
			// print $sUnix ." -- ".$eUnix ."<br />";
			// print "<pre>";
			// var_dump($hoursInfo);
			// print "</pre>";


			// print "<pre>";
			// var_dump($hoursInfo[0]);
			// print "</pre>";
			// print "<pre>";
			// var_dump($sUnix);
			// print "</pre>";
			// print "<pre>";
			// var_dump($eUnix);
			// print "</pre>";
			// print "<pre>";
			// var_dump($hoursInfo[1]);
			// print "</pre>";

			if (isset($hoursInfo[1]) && !isempty($hoursInfo[1]) && isset($hoursInfo[0]) && !isempty($hoursInfo[0])) {
				if ($sUnix >= $hoursInfo[0] && $sUnix < $hoursInfo[1] && $eUnix > $hoursInfo[0] && $eUnix <= $hoursInfo[1]) {

				}
				else {
					errorHandle::errorMsg(getResultMessage("libraryClose"));
					return(FALSE);
				}
			}
		}

		// Check for same day reservations
		if ($policySameDayReservations == "0") {
			$todayMonth = date("n");
			$todayDay   = date("j");
			$todayYear  = date("Y");

			if ($sUnix >= mktime(0,0,0,$todayMonth,$todayDay,$todayYear) && $sUnix <= mktime(23,59,59,$todayMonth,$todayDay,$todayYear)) {
				errorHandle::errorMsg(getResultMessage("sameDayReservation"));
				return(FALSE);
			}
		}

		// Get system max hours and bookings
		if ($policyFutureScheduleLen > 0 && strtotime("+ ".$policyFutureScheduleLen." days") < $sUnix) {
			errorHandle::errorMsg(getResultMessage("tooFarInFuture"));
			return(FALSE);
		}

		// is the request length greater than the max loan length for this policy
		if ($policyHoursPerBooking > 0 && $totalHours > $policyHoursPerBooking) {
			errorHandle::errorMsg(getResultMessage("reservationLengthTooLong"));
			return(FALSE);
		}

		// will requesting this length push the user over the total number of hours allowed
		// for this policy/location

		// // Debugging
		// print "system: ".$systemMaxHours." == ".$counts['hours']['total']."<br />";
		// print "library: ".$libraryMaxHours." == ".$counts['hours']['building'][$buildingID]."<br />";
		// print "policy: ".$policyMaxHours." == ".$counts['hours']['policy'][$policyID]."<br />";

		// print "system: ".$systemMaxBookings." == ".$counts['bookings']['total']."<br />";
		// print "library: ".$libraryMaxBookings." == ".$counts['bookings']['building'][$buildingID]."<br />";
		// print "policy: ".$policyMaxBookings." == ".$counts['bookings']['policy'][$policyID]."<br />";

		// system check
		if ($systemMaxHours > 0 && isset($counts['hours']['total']) && ($counts['hours']['total'] + $totalHours) > $systemMaxHours) {
			// print "here<br />";
			// print $counts['hours']['total']."<br />";
			errorHandle::errorMsg(getResultMessage("userOverSystemHours"));
			return(FALSE);
		}

		// Library Check
		if ($libraryMaxHours > 0 && isset($counts['hours']['building'][$buildingID]) && ($counts['hours']['building'][$buildingID] + $totalHours) > $libraryMaxHours) {
			errorHandle::errorMsg(getResultMessage("userOverLibraryHours"));
			return(FALSE);
		}

		// Policy Check
		if ($policyMaxHours > 0 && isset($counts['hours']['policy'][$policyID]) && ($counts['hours']['policy'][$policyID] + $totalHours) > $policyMaxHours) {
			errorHandle::errorMsg(getResultMessage("userOverPolicyHours"));
			return(FALSE);
		}

		// will requesting this room push the user over the allowed bookings per period for 
		// this policy/location

		// system check
		if ($systemMaxBookings > 0 && isset($counts['bookings']['total']) && ($counts['bookings']['total'] + 1) > $systemMaxBookings) {
			errorHandle::errorMsg(getResultMessage("userOverSystemBookings"));
			return(FALSE);
		}

		// Library Check
		if ($libraryMaxBookings > 0 && isset($counts['bookings']['building'][$buildingID]) && ($counts['bookings']['building'][$buildingID] + 1) > $libraryMaxBookings) {
			errorHandle::errorMsg(getResultMessage("userOverBuildingBookings"));
			return(FALSE);
		}

		// Policy Check
		if ($libraryMaxBookings > 0 && isset($counts['bookings']['policy'][$policyID]) && ($counts['bookings']['policy'][$policyID] + 1) > $policyMaxBookings) {
			errorHandle::errorMsg(getResultMessage("userOverPolicyBookings"));
			return(FALSE);
		}
	}

	if ($reservationUpdate === FALSE) {
		$sql       = sprintf("INSERT INTO `reservations` (createdOn,createdBy,createdVia,roomID,startTime,endTime,modifiedOn,modifiedBy,username,initials,groupname,comments,seriesID) VALUES('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s')",
			$engine->openDB->escape(time()),
			$engine->openDB->escape(sessionGet("username")),
			$engine->openDB->escape($via),
			$engine->openDB->escape($roomID),
			$engine->openDB->escape($sUnix),
			$engine->openDB->escape($eUnix),
			$engine->openDB->escape(time()),
			$engine->openDB->escape(sessionGet("username")),
			$engine->openDB->escape(lc($username)),
			$engine->openDB->escape($userInformation['initials']),
			$groupname,
			$comments,
			(isnull($seriesID))?"":$seriesID
			);
	}
	else {
		$sql = sprintf("UPDATE `reservations` SET startTime='%s', endTime='%s', modifiedOn='%s', modifiedBy='%s', username='%s', initials='%s', groupname='%s', comments='%s' WHERE ID='%s'",
			$engine->openDB->escape($sUnix),
			$engine->openDB->escape($eUnix),
			$engine->openDB->escape(time()),
			$engine->openDB->escape(sessionGet("username")),
			$engine->openDB->escape(lc($username)),
			$engine->openDB->escape($userInformation['initials']),
			$engine->openDB->escape($reservationUpdate),
			$groupname,
			$comments
			);
	}

	$sqlResult = $engine->openDB->query($sql);

	if (!$sqlResult['result']) {
		errorHandle::errorMsg(getResultMessage("errorInserting"));
		return(FALSE);
	}

	$roomName     = getRoomInfo($_POST['MYSQL']['room']);

	if ($reservationUpdate === FALSE) {
		$resultMessage = getResultMessage("reservationCreated");
		$resultMessage = preg_replace("/{roomName}/", $roomName['displayName'], $resultMessage);
		errorHandle::successMsg($resultMessage);
	}
	else {
		errorHandle::successMsg(getResultMessage("reservationUpdated"));
	}

	// Print off slip link
	// // TODO

	// If there was an email address submitted, send an email to that address
	if (isset($_POST['HTML']['notificationEmail']) && validate::emailAddr($_POST['HTML']['notificationEmail'])) {
		
		
		$buildingName = getBuildingName($roomName['building']);

		$sam = "am";
		if ($shour > 12) {
			$shour = $shour - 12;
			$sam = "pm";
		}

		$eam = "am";
		if ($ehour > 12) {
			$ehour = $ehour - 12;
			$eam = "pm";
		}

		$subject  = "Room Reservation Created: ".$month."/".$day."/".$year;

		$emailMsg  = "Your room reservation has been successfully created: \n";
		$emailMsg .= "Date: ".$month."/".$day."/".$year."\n";
		$emailMsg .= "Time: ".$shour.":".$smin." ".$sam." - ".$ehour.":".$emin." ".$eam."\n";
		$emailMsg .= "Building: ".$buildingName."\n";
		$emailMsg .= "Room: ".$roomName['displayName']."\n";

		$mail = new mailSender();
		$mail->addRecipient($_POST['HTML']['notificationEmail']);
		$mail->addSender("libsys@mail.wvu.edu", "WVU Libraries");
		$mail->addSubject($subject);
		$mail->addBody($emailMsg);

		$sendResult = $mail->sendEmail();

	}

	return(TRUE);

}

function duplicateReservationCheck($username,$roomID,$sUnix,$eUnix) {

	$engine = EngineAPI::singleton();

	$sql       = sprintf("SELECT * FROM `reservations` WHERE username='%s' AND roomID='%s' AND startTime='%s' AND endTime='%s'",
		$engine->openDB->escape($username),
		$engine->openDB->escape($roomID),
		$engine->openDB->escape($sUnix),
		$engine->openDB->escape($eUnix)
		);
	$sqlResult = $engine->openDB->query($sql);
	if (!$sqlResult['result']) {
		errorHandle::newError(__METHOD__."() - ".$sqlResult['error'], errorHandle::DEBUG);
		return(NULL);
	}
	else {
		if ($sqlResult['numrows'] > 0) {
			return(TRUE);
		}
	}

	return(FALSE);

}

function multipleBooksings($username,$sUnix,$eUnix) {

	$engine = EngineAPI::singleton();

	$sql       = sprintf("SELECT * FROM `reservations` WHERE ((startTime<='%s' AND endTime>'%s') OR (startTime<'%s' AND endTime>='%s')) AND username='%s'",
		$engine->openDB->escape($sUnix),
		$engine->openDB->escape($sUnix),
		$engine->openDB->escape($eUnix),
		$engine->openDB->escape($eUnix),
		$engine->openDB->escape($username)
		);
	$sqlResult = $engine->openDB->query($sql);

	if (!$sqlResult['result']) {
		errorHandle::newError(__METHOD__."() - ".$sqlResult['error'], errorHandle::DEBUG);
		return(TRUE); // we return true, because there was an error and we don't want the reservation to submit on error
	}

	if ($sqlResult['numrows'] > 0) {
		return(TRUE);
	}

	return(FALSE);

}

function conflictReservationCheck($roomID,$sUnix,$eUnix) {

	$engine = EngineAPI::singleton();

	$sql       = sprintf("SELECT * FROM `reservations` WHERE ( ((startTime<='%s' AND endTime>'%s') OR (startTime<'%s' AND endTime>='%s')) OR (startTime>='%s' AND endTime<='%s') ) AND roomID='%s'",
		$engine->openDB->escape($sUnix),
		$engine->openDB->escape($sUnix),
		$engine->openDB->escape($eUnix),
		$engine->openDB->escape($eUnix),
		$engine->openDB->escape($sUnix),
		$engine->openDB->escape($eUnix),
		$engine->openDB->escape($roomID)
		);
	$sqlResult = $engine->openDB->query($sql);

	if (!$sqlResult['result']) {
		errorHandle::newError(__METHOD__."() - ".$sqlResult['error'], errorHandle::DEBUG);
		return(TRUE); // we return true, because there was an error and we don't want the reservation to submit on error
	}

	if ($sqlResult['numrows'] > 0) {
		return(TRUE);
	}

	return(FALSE);

}

function validateRoomPostVariables() {

	$validate         = new formValidation();
	$validate->strict = FALSE;

	$field = array();
	$field['type']     = "post";
	$field['var']      = "library";
	$field['validate'] = "integer";
	$validate->addField($field);
	unset($field);

	$field = array();
	$field['type']     = "post";
	$field['var']      = "room";
	$field['validate'] = "integer";
	$validate->addField($field);
	unset($field);


	$field = array();
	$field['type']     = "post";
	$field['var']      = "username";
	$field['validate'] = "/^[a-z0-9_-]{3,15}$/";
	$validate->addField($field);
	unset($field);

	$field = array();
	$field['type']     = "post";
	$field['var']      = "start_month";
	$field['validate'] = "/\d\d/";
	$validate->addField($field);
	unset($field);

	$field = array();
	$field['type']     = "post";
	$field['var']      = "start_day";
	$field['validate'] = "/\d\d/";
	$validate->addField($field);
	unset($field);

	$field = array();
	$field['type']     = "post";
	$field['var']      = "start_year";
	$field['validate'] = "/\d\d\d\d/";
	$validate->addField($field);
	unset($field);

	$field = array();
	$field['type']     = "post";
	$field['var']      = "start_hour";
	$field['validate'] = "/\d\d/";
	$validate->addField($field);
	unset($field);

	$field = array();
	$field['type']     = "post";
	$field['var']      = "start_minute";
	$field['validate'] = "/\d\d/";
	$validate->addField($field);
	unset($field);

	$field = array();
	$field['type']     = "post";
	$field['var']      = "end_hour";
	$field['validate'] = "/\d\d/";
	$validate->addField($field);
	unset($field);

	$field = array();
	$field['type']     = "post";
	$field['var']      = "end_minute";
	$field['validate'] = "/\d\d/";
	$validate->addField($field);
	unset($field);

	$field = array();
	$field['type']     = "post";
	$field['var']      = "createSubmit";
	$field['validate'] = NULL;
	$validate->addField($field);
	unset($field);

	$result = $validate->validate(FALSE);

	return($result);

}

?>