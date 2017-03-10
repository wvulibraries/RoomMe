<?php

class reservation {

	public $reservation = NULL;

	private $localvars;
	private $engine;
	private $db;

	public $building     = array();
	public $room         = array();
	public $series       = FALSE;

	function __construct() {
		$this->localvars = localvars::getInstance();
		$this->engine    = EngineAPI::singleton();
		$this->db        = db::get($this->localvars->get('dbConnectionName'));
	}

	public function get($ID) {

		if (!$this->validateID($ID)) {
			return FALSE;
		}

		$sql       = "SELECT reservations.*, building.ID as buildingID FROM `reservations` LEFT JOIN `rooms` ON rooms.ID=reservations.roomID LEFT JOIN `building` ON building.ID=rooms.building WHERE reservations.ID=?";
		$sqlResult = $this->db->query($sql,array($ID));

		if ($sqlResult->error()) {
			errorHandle::newError($sqlResult->errorMsg(), errorHandle::DEBUG);
			return FALSE;
		}

		$this->reservation = $sqlResult->fetch();

		if ($this->setBuilding($this->reservation['buildingID']) === FALSE) {
			return FALSE;
		}
		if ($this->setRoom($this->reservation['roomID']) === FALSE) {
			return FALSE;
		}

		return TRUE;

	}

	public function delete() {

		if ($this->isNew()) {
			return FALSE;
		}

		$sql       = sprintf("DELETE FROM `reservations` WHERE ID=?");
		$sqlResult = $this->db->query($sql,array($this->reservation['ID']));

		if ($sqlResult->error()) {
			errorHandle::newError($sqlResult->errorMsg(), errorHandle::DEBUG);
			return FALSE;
		}

		return TRUE;

	}

	// @TODO -- this method needs refactored. badly
	public function create($seriesID=NULL) {

		$buildingID = $this->building['ID'];
		$roomID = $this->room['ID'];

		$reservationPermissions = new reservationPermissions;

		if (!$reservationPermissions->permissionsCheck($buildingID, $_POST['MYSQL']['notificationEmail'], $roomID)) {
			return FALSE;
		}

		if ($this->validateRoomPostVariables() === FALSE) {
			errorHandle::errorMsg(getResultMessage("dataValidationError"));
			return FALSE;
		}

		$via               = "Public Interface";
		$override          = "0";
		$groupname         = "";
		$comments          = "";
		// If the fields are set AND we are coming from the staff interface, we can modify $via and $override
		// @TODO we need to handle the preg_match dynamically.

		if (isset($_POST['MYSQL']['via']) && (preg_match('/\/admin\//',$_SERVER['PHP_SELF']))) {
			$via      = $_POST['MYSQL']['via'];
			$override = $_POST['MYSQL']['override'];

			if (isset($_POST['MYSQL']['groupname']) && !is_empty($_POST['MYSQL']['groupname'])) {
				$groupname = $_POST['MYSQL']['groupname'];
			}
			if (isset($_POST['MYSQL']['comments']) && !is_empty($_POST['MYSQL']['comments'])) {
				$comments = $_POST['MYSQL']['comments'];
			}
		}

		// Username -- this will be hidden on the public form, entry on the staff interface
		$username = $_POST['MYSQL']['username'];

		// verify that the username is real and get the initials of the user
		// @TODO this needs converted to the new user class
		$userInformation = getUserInfo($username);         // takes a username and returns an array with info on success or FALSE on failure

		if ($userInformation === FALSE) {
			errorHandle::errorMsg(getResultMessage("invalidUsername"));
			return FALSE;
		}

		// check that an email address was submitted
		// @TODO move this to validateRoomPostVariables
		if (!isset($_POST['MYSQL']['notificationEmail']) || is_empty($_POST['MYSQL']['notificationEmail'])) {
			errorHandle::errorMsg(getResultMessage("emailNotProvided"));
			return FALSE;
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
			return FALSE;
		}

		$sUnix = mktime($shour,$smin,0,$month,$day,$year);

		// Convert the duration into hours and minutes

		$ehour = (int)$ehour * 60 * 60;
		$emin  = (int)$emin * 60;

		// create the end time using the new ehour and emin
		$eUnix = $sUnix + $ehour + $emin;

		// make sure the end time is after the start time
		if ($eUnix <= $sUnix) {
			errorHandle::errorMsg(getResultMessage("endBeforeStart"));
			return FALSE;
		}

		// is this a reservation being requested in the past?
		// @TODO This needs to be configurable, time before current when reservation is not allowed.
		// We may even want to beak it off into a separate check for better error message input.
		// if (isnull($seriesID) && $sUnix < (time() - 3600)) {
		// 	errorHandle::errorMsg(getResultMessage("reservationInPast"));
		// 	return FALSE;
		// }

		// check for a duplicate reservation
		// Check to make sure the reservation is new. If it is an update, it isn't a duplicate.
		if ($this->isNew() && $this->duplicateReservationCheck($username,$roomID,$sUnix,$eUnix) !== FALSE) {
			errorHandle::errorMsg(getResultMessage("duplicateReservation"));
			return FALSE;
		}

		// check for a conflict with another reservation
		if ($this->conflictReservationCheck($roomID,$sUnix,$eUnix) !== FALSE) {
			errorHandle::errorMsg(getResultMessage("reservationConflict"));
			return FALSE;
		}

		// determine the total number of hours the room is being requested
		$totalTime  = $eUnix - $sUnix;
		$totalHours = (float)($totalTime / 60 / 60);

		// Get System, library, and Policy information
		$sql       = sprintf("SELECT policies.*, building.hoursRSS as hoursRSS, building.fineLookupURL as fineLookupURL, building.fineAmount as building_fineAmount, building.maxHoursAllowed as building_maxHoursAllowed, building.period as building_period, building.bookingsAllowedInPeriod as building_bookingsAllowedInPeriod FROM rooms LEFT JOIN roomTemplates ON rooms.roomTemplate=roomTemplates.ID LEFT JOIN `policies` ON roomTemplates.policy = policies.ID LEFT JOIN building ON rooms.building = building.ID WHERE rooms.ID=? LIMIT 1");
		$sqlResult = $this->db->query($sql,array($_POST['MYSQL']['room']));

		if ($sqlResult->error()) {
			errorHandle::newError(__FUNCTION__."() - ".$sqlResult->errorMsg(), errorHandle::DEBUG);
			errorHandle::errorMsg(getResultMessage("policyError"));
			return FALSE;
		}

		$row                   = $sqlResult->fetch();

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
		$sqlResult = $this->db->query($sql);

		if ($sqlResult->error()) {
			errorHandle::newError(__FUNCTION__."() - ".$sqlResult->errorMsg(), errorHandle::DEBUG);
			errorHandle::errorMsg(getResultMessage("systemsPolicyError"));
			return FALSE;
		}

		while($row       = $sqlResult->fetch()) {

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
		$sqlResult = $this->db->query($sql);

		if ($sqlResult->error()) {
			errorHandle::newError(__FUNCTION__."() - ".$sqlResult->errorMsg(), errorHandle::DEBUG);
			errorHandle::errorMsg("Error retrieving buildings");
			return FALSE;
		}

		while ($row       = $sqlResult->fetch()) {
			$counts['hours']['building'][$row['ID']]    = 0;
			$counts['bookings']['building'][$row['ID']] = 0;
		}

		$sql       = sprintf("SELECT * FROM policies");
		$sqlResult = $this->db->query($sql);

		if ($sqlResult->error()) {
			errorHandle::newError(__FUNCTION__."() - ".$sqlResult->errorMsg(), errorHandle::DEBUG);
			errorHandle::errorMsg("Error retrieving buildings");
			return FALSE;
		}

		while ($row       = $sqlResult->fetch()) {
			$counts['hours']['policy'][$row['ID']]    = 0;
			$counts['bookings']['policy'][$row['ID']] = 0;
		}

		// get patron information
		$sql = sprintf("SELECT reservations.roomID as roomID, reservations.startTime as startTime, reservations.endTime as endTime, building.ID as buildingID, roomTemplates.policy as policyID FROM reservations LEFT JOIN rooms ON reservations.roomID=rooms.ID LEFT JOIN building ON rooms.building=building.ID LEFT JOIN roomTemplates ON rooms.roomTemplate=roomTemplates.ID WHERE username=? AND reservations.endTime>=? AND reservations.startTime<=?");

		$sqlResult  = $this->db->query($sql,array(strtolower($username),$sUnix - ($currentPeriod/2),$eUnix + ($currentPeriod/2)));

		if ($sqlResult->error()) {
			errorHandle::newError(__FUNCTION__."() - ".$sqlResult->errorMsg(), errorHandle::DEBUG);
			errorHandle::errorMsg(getResultMessage("patronReservationInfo"));
			return FALSE;
		}


		while ($row = $sqlResult->fetch()) {

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
				if ($this->multipleBooksings($username,$sUnix,$eUnix)) {
					errorHandle::errorMsg(getResultMessage("multipleRoomBookings"));
					return FALSE;
				}
			}

			// query the system to determine how much money the user owes
			if ($currentFineAmount > 0) {
				// call voyager
				// this should be a URL that returns a number
				$usersFineAmount = file_get_contents($libraryfineLookupURL.$userInformation['idNumber']);

				$usersFineAmount = explode("\n", $usersFineAmount);
				$usersFineAmount = $usersFineAmount[count($usersFineAmount) - 1];

				if ($usersFineAmount >= $currentFineAmount) {
					$resultFineMessage = getResultMessage("maxFineExceeded");
					$resultFineMessage = preg_replace("/{amount}/", $currentFineAmount, $resultFineMessage);
					errorHandle::errorMsg($resultFineMessage);
					return FALSE;
				}
			}

			// check against library hours

			// get hours from the RSS feed.
			// if the RSS feed is unavailable, assume the library will be open (should this be configurable?)
			if (!is_empty($libraryHoursURL)) {

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

				if (isset($hoursInfo[1]) && !is_empty($hoursInfo[1]) && isset($hoursInfo[0]) && !is_empty($hoursInfo[0])) {


					if ($sUnix >= $hoursInfo[0] && $sUnix < $hoursInfo[1] && $eUnix > $hoursInfo[0] && $eUnix <= $hoursInfo[1]) {

					}
					else {
						errorHandle::errorMsg(getResultMessage("libraryClose"));
						return FALSE;
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
					return FALSE;
				}
			}

			// Get system max hours and bookings
			if ($policyFutureScheduleLen > 0 && strtotime("+ ".$policyFutureScheduleLen." days") < $sUnix) {
				errorHandle::errorMsg(getResultMessage("tooFarInFuture"));
				return FALSE;
			}

			// is the request length greater than the max loan length for this policy
			if ($policyHoursPerBooking > 0 && $totalHours > $policyHoursPerBooking) {
				errorHandle::errorMsg(getResultMessage("reservationLengthTooLong"));
				return FALSE;
			}

			// will requesting this length push the user over the total number of hours allowed
			// for this policy/location

			// system check
			if ($systemMaxHours > 0 && isset($counts['hours']['total']) && ($counts['hours']['total'] + $totalHours) > $systemMaxHours) {
				errorHandle::errorMsg(getResultMessage("userOverSystemHours"));
				return FALSE;
			}

			// Library Check
			if ($libraryMaxHours > 0 && isset($counts['hours']['building'][$buildingID]) && ($counts['hours']['building'][$buildingID] + $totalHours) > $libraryMaxHours) {
				errorHandle::errorMsg(getResultMessage("userOverLibraryHours"));
				return FALSE;
			}

			// Policy Check
			if ($policyMaxHours > 0 && isset($counts['hours']['policy'][$policyID]) && ($counts['hours']['policy'][$policyID] + $totalHours) > $policyMaxHours) {
				errorHandle::errorMsg(getResultMessage("userOverPolicyHours"));
				return FALSE;
			}

			// will requesting this room push the user over the allowed bookings per period for
			// this policy/location

			// system check
			if ($systemMaxBookings > 0 && isset($counts['bookings']['total']) && ($counts['bookings']['total'] + 1) > $systemMaxBookings) {
				errorHandle::errorMsg(getResultMessage("userOverSystemBookings"));
				return FALSE;
			}

			// Library Check
			if ($libraryMaxBookings > 0 && isset($counts['bookings']['building'][$buildingID]) && ($counts['bookings']['building'][$buildingID] + 1) > $libraryMaxBookings) {
				errorHandle::errorMsg(getResultMessage("userOverBuildingBookings"));
				return FALSE;
			}

			// Policy Check
			if ($libraryMaxBookings > 0 && isset($counts['bookings']['policy'][$policyID]) && ($counts['bookings']['policy'][$policyID] + 1) > $policyMaxBookings) {
				errorHandle::errorMsg(getResultMessage("userOverPolicyBookings"));
				return FALSE;
			}
		}

		if ($this->isNew()) {
			$sql        = sprintf("INSERT INTO `reservations` (createdOn,createdBy,createdVia,roomID,startTime,endTime,modifiedOn,modifiedBy,username,initials,groupname,comments,seriesID,email,openEvent,openEventDescription) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
			$sqlOptions = array(
				time(),
				session::get("username"),
				$via,
				$roomID,
				$sUnix,
				$eUnix,
				time(),
				session::get("username"),
				strtolower($username),
				$userInformation['initials'],
				$groupname,
				$comments,
				(isnull($seriesID))?"":$seriesID,
				(isset($_POST['MYSQL']['notificationEmail']))?$_POST['MYSQL']['notificationEmail']:"",
				(isset($_POST['MYSQL']['openEvent']))?$_POST['MYSQL']['openEvent']:"",
				(isset($_POST['MYSQL']['openEventDescription']))?$_POST['MYSQL']['openEventDescription']:""
				);
		}
		else {
			$sql        = sprintf("UPDATE `reservations` SET startTime=?, endTime=?, modifiedOn=?, modifiedBy=?, username=?, initials=?, groupname=?, comments=?, createdVia=?, email=?, openEvent=?, openEventDescription=? WHERE ID=?");
			$sqlOptions = array(
				$sUnix,
				$eUnix,
				time(),
				session::get("username"),
				strtolower($username),
				$userInformation['initials'],
				$groupname,
				$comments,
				$via,
				(isset($_POST['MYSQL']['notificationEmail']))?$_POST['MYSQL']['notificationEmail']:"",
				($_POST['MYSQL']['openEvent'])?$_POST['MYSQL']['openEvent']:"",
				($_POST['MYSQL']['openEventDescription'])?$_POST['MYSQL']['openEventDescription']:"",
				$this->reservation['ID']
				);
		}

		$sqlResult = $this->db->query($sql,$sqlOptions);

		if ($sqlResult->error()) {
			errorHandle::newError(__METHOD__."() - ".$sqlResult->errorMsg(), errorHandle::DEBUG);
			errorHandle::errorMsg(getResultMessage("errorInserting"));
			return FALSE;
		}

		$roomName     = getRoomInfo($_POST['MYSQL']['room']);

		// we don't want to show the success message for series reservations
		if (!$this->series) {
			if ($this->isNew()) {
				$resultMessage = getResultMessage("reservationCreated");
				$resultMessage = preg_replace("/{roomName}/", $roomName['displayName'], $resultMessage);
				errorHandle::successMsg($resultMessage);
			}
			else {
				errorHandle::successMsg(getResultMessage("reservationUpdated"));
			}
		}

		// Print off slip link
		// // TODO

		// If there was an email address submitted, send an email to that address
		// Don't send email for series reservations
		if (!$this->series && isset($_POST['HTML']['notificationEmail']) && validate::getInstance()->emailAddr($_POST['HTML']['notificationEmail'])) {


			$buildingName = getBuildingName($roomName['building']);
			$subject      = "Room Reservation Created: ".$month."/".$day."/".$year;

			$emailMsg  = "Your room reservation has been successfully created: \n";
			$emailMsg .= "Date: ".$month."/".$day."/".$year."\n";
			$emailMsg .= sprintf("Time: %s - %s\n",
				date("g:i a",$sUnix),
				date("g:i a",$eUnix)
				);
			$emailMsg .= "Building: ".$buildingName."\n";
			$emailMsg .= "Room: ".$roomName['displayName']."\n";

			$buildingObject = new building;
			$building       = $buildingObject->get($roomName['building']);

			$mail = new mailSender();
			$mail->addRecipient($_POST['HTML']['notificationEmail']);
			if (isset($building['fromEmail']) && !is_empty($building['fromEmail'])) {
				$mail->addSender($building['fromEmail'], "WVU Libraries");
			}
			else {
				$mail->addSender("libsys@mail.wvu.edu", "WVU Libraries");
			}
			$mail->addSubject($subject);
			$mail->addBody($emailMsg);

			$sendResult = $mail->sendEmail();

		}

		if isset($_POST['MYSQL']['openEvent']) {
			// send an email to the open event email address
			if (!$this->series && $_POST['MYSQL']['openEvent'] && !is_empty(getConfig('openEventEmail'))) {

				$buildingName = getBuildingName($roomName['building']);

				$subject  = "Room Reservation Created as Open Event: ".$month."/".$day."/".$year;

				$emailMsg  = "The following reservation has been successfully created, and marked as an Open, Public, Event: \n";

				$emailMsg .= "Created By:".$_POST['HTML']['notificationEmail']."\n\n";

				$emailMsg .= "Date: ".$month."/".$day."/".$year."\n";
				$emailMsg .= sprintf("Time: %s - %s\n",
					date("g:i a",$sUnix),
					date("g:i a",$eUnix)
					);
				$emailMsg .= "Building: ".$buildingName."\n";
				$emailMsg .= "Room: ".$roomName['displayName']."\n\n";
				$emailMsg .= "Open Event Description: \n".$_POST['HTML']['openEventDescription']."\n\n";

				$mail = new mailSender();
				$mail->addRecipient(getConfig('openEventEmail'));
				$mail->addSender("libsys@mail.wvu.edu", "WVU Libraries");
				$mail->addSubject($subject);
				$mail->addBody($emailMsg);

				$sendResult = $mail->sendEmail();

			}
		}
		// refresh / get the reservation information
		// We populate this last, otherwise isNew() will not return correctly
		$this->get(($this->isNew())?$sqlResult->insertId():$this->reservation['ID']);

		return TRUE;

	}

	public function setBuilding($ID) {

		$building = new building;

		if (($this->building = $building->get($ID)) === FALSE) {
			return FALSE;
		}

		return TRUE;

	}

	public function setRoom($ID) {

		$room = new room;

		if (($this->room = $room->get($ID)) === FALSE) {
			return FALSE;
		}

		return TRUE;

	}

	public function isNew() {
		if (isnull($this->reservation)) {
			return TRUE;
		}

		return FALSE;
	}

	public function hasEmail() {

		if (isset($this->reservation['email']) && !is_empty($this->reservation['email'])) {
			return TRUE;
		}
		else {
			return FALSE;
		}

	}

	private function validateID($ID) {

		return validate::getInstance()->integer($ID);

	}

	private function duplicateReservationCheck($username,$roomID,$sUnix,$eUnix) {

		$sql       = sprintf("SELECT * FROM `reservations` WHERE username=? AND roomID=? AND startTime=? AND endTime=?");
		$sqlResult = $this->db->query($sql,array($username, $roomID, $sUnix,	$eUnix));
		if ($sqlResult->error()) {
			errorHandle::newError(__FUNCTION__."() - ".$sqlResult->errorMsg(), errorHandle::DEBUG);
			return(NULL);
		}
		else {
			if ($sqlResult->rowCount() > 0) {
				return TRUE;
			}
		}

		return FALSE;

	}

	// $sqlResult is the result set from a sql call
	// Returns true if we are trying to update a reservation and the result conflict is ONLY itself
	// Returns false otherwise.
	private function updatingSelf($sqlResult) {

		if ($this->isNew()) {
			return FALSE;
		}

		if ($sqlResult->rowCount() != 1) {
			return FALSE;
		}

		$row = $sqlResult->fetch();

		if ($row['ID'] == $this->reservation['ID']) {
			return TRUE;
		}

		return FALSE;

	}

	// False is good.
	private function multipleBooksings($username,$sUnix,$eUnix) {

		$sql       = sprintf("SELECT * FROM `reservations` WHERE ((startTime<=? AND endTime>?) OR (startTime<? AND endTime>=?)) AND username=?");
		$sqlResult = $this->db->query($sql,array($sUnix,$sUnix,$eUnix,$eUnix,$username));

		if ($sqlResult->error()) {
			errorHandle::newError(__FUNCTION__."() - ".$sqlResult->errorMsg(), errorHandle::DEBUG);
			return TRUE; // we return true, because there was an error and we don't want the reservation to submit on error
		}

		// 0 (or less) rows return, Good to go.
		if ($sqlResult->rowCount() <= 0) {
			return FALSE;
		}

		return !$this->updatingSelf($sqlResult);

	}

	// Returns true on reservation conflict. False otherwise.
	//  False is good
	private function conflictReservationCheck($roomID,$sUnix,$eUnix) {

		$sql       = sprintf("SELECT * FROM `reservations` WHERE ( ((startTime<=? AND endTime>?) OR (startTime<? AND endTime>=?)) OR (startTime>=? AND endTime<=?) ) AND roomID=?");
		$sqlResult = $this->db->query($sql,array($sUnix, $sUnix, $eUnix, $eUnix, $sUnix, $eUnix, $roomID));

		if ($sqlResult->error()) {
			errorHandle::newError(__FUNCTION__."() - ".$sqlResult->errorMsg(), errorHandle::DEBUG);
			return TRUE; // we return true, because there was an error and we don't want the reservation to submit on error
		}

		// 0 (or less) rows return, Good to go.
		if ($sqlResult->rowCount() <= 0) {
			return FALSE;
		}

		return !$this->updatingSelf($sqlResult);

		// Check to see if the reservation is conflicting with, ONLY, itself.
		// That is OK.
		// if (!$this->isNew() && $sqlResult->rowCount() == 1) {
		// 	$row = $sqlResult->fetch();
		// 	if ($row['ID'] == $this->reservation['ID']) {
		// 		return FALSE;
		// 	}
		// }

		// Otherwise, return TRUE (there is a conflict)
		// return TRUE;

	}

	private function validateRoomPostVariables() {

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

}

?>
