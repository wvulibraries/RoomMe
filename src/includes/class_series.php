<?php

class series {

	public $startTime;
	public $endTime;
	public $startDay;
	public $seriesEndDate;

	private $localvars;
	private $engine;
	private $db;

	public $building     = array();
	public $room         = array();

	public $reservation = NULL;

	function __construct() {
		$this->localvars = localvars::getInstance();
		$this->engine    = EngineAPI::singleton();
		$this->db        = db::get($this->localvars->get('dbConnectionName'));
	}

	public function get($ID) {

		if (!$this->validateID($ID)) {
			return FALSE;
		}

		$sql       = sprintf("SELECT seriesReservations.*, building.ID as buildingID FROM `seriesReservations` LEFT JOIN `rooms` ON rooms.ID=seriesReservations.roomID LEFT JOIN `building` ON building.ID=rooms.building WHERE seriesReservations.ID=?");
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

		if (!is_empty($this->reservation['weekdays'])) {
			$this->reservation['weekdaysAssigned'] = unserialize($this->reservation['weekdays']);
		}

		return TRUE;

	}

	private function validateID($ID) {
		return validate::getInstance()->integer($ID);
	}

	public function delete($ID) {

		$transResult = $this->db->beginTransaction();

		$sql       = sprintf("DELETE FROM `reservations` WHERE seriesID=? AND startTime>?");
		$sqlResult = $this->db->query($sql,array($ID,time()));

		if ($sqlResult->error()) {
			$this->db->rollback();
			errorHandle::newError($sqlResult->errorMsg(), errorHandle::DEBUG);
			errorHandle::errorMsg("Error deleting series reservation.");
			return FALSE;
		}

		$sql       = sprintf("DELETE FROM `seriesReservations` WHERE ID=?");
		$sqlResult = $this->db->query($sql,array($ID));

		if ($sqlResult->error()) {
			$this->db->rollback();

			errorHandle::successMsg("Series Reservation Deleted.");

			return FALSE;
		}

		$this->db->commit();
		return TRUE;

	}

	public function create() {

		$errorMsg = "";

		$schedule = array();

		$weekdays = array(FALSE,FALSE,FALSE,FALSE,FALSE,FALSE,FALSE);
		if (isset($_POST['MYSQL']['weekday'])) {
			foreach ($_POST['MYSQL']['weekday'] as $I=>$V) {
				$weekdays[$V] = TRUE;
			}
		}

		$allDay    = (isset($_POST['MYSQL']['allDay']) && $_POST['MYSQL']['allDay'] == "1")?TRUE:FALSE;
		$frequency = $_POST['MYSQL']['frequency'];

		if ($allDay === TRUE) {
			$_POST['MYSQL']['start_hour']   = "0";
			$_POST['MYSQL']['start_minute'] = "0";
			$_POST['MYSQL']['end_hour']     = "23";
			$_POST['MYSQL']['end_minute']   = "59";
		}

		$this->startTime     = mktime($_POST['MYSQL']['start_hour'],$_POST['MYSQL']['start_minute'],0,$_POST['MYSQL']['start_month'],$_POST['MYSQL']['start_day'],$_POST['MYSQL']['start_year']);

		$ehour = (int)$_POST['MYSQL']['end_hour'] * 60 * 60;
		$emin  = (int)$_POST['MYSQL']['end_minute'] * 60;
		$this->endTime = $this->startTime + $ehour + $emin;

		$this->startDay      = mktime(0,0,0,$_POST['MYSQL']['start_month'],$_POST['MYSQL']['start_day'],$_POST['MYSQL']['start_year']);
		$this->seriesEndDate = mktime(0,0,0,$_POST['MYSQL']['seriesEndDate_month'],$_POST['MYSQL']['seriesEndDate_day'],$_POST['MYSQL']['seriesEndDate_year']);

		// if "Every Day" is the frequency, error when weekdays are selected
		if ($frequency === "0" && in_array(TRUE,$weekdays)) {
			$errorMsg .= errorHandle::errorMsg("Cannot select Everyday as a frequency and select specific days of the week");
			return FALSE;
		}
		if (($frequency =="2" || $frequency == "3")&& in_array(TRUE,$weekdays)) {
			$errorMsg .= errorHandle::errorMsg("Cannot select Every Month as a frequency and select specific days of the week");
			return FALSE;
		}
		if ($this->seriesEndDate < $this->startDay) {
			$errorMsg .= errorHandle::errorMsg("Series End Date cannot be before the start time.");
			return FALSE;
		}
		if ($this->seriesEndDate == $this->startDay || ($this->seriesEndDate > $this->startTime && $this->seriesEndDate < $this->endTime)) {
			$errorMsg .= errorHandle::errorMsg("Series end date is the same day as start time, please create a normal reservation");
			return FALSE;
		}

		// Everyday
		if ($frequency == "0") {

			$schedule = $this->getSchedule("+1 day");

		}
		// every week
		else if ($frequency == "1") {

			// no weekdays are selected
			if (!in_array(TRUE,$weekdays)) {
				$schedule = $this->getSchedule("+1 week");
			}

			// weekdays are selected
			else {

				$dateInfo = getdate($this->startTime);

				// Save original start and end times
				$originalStartDay  = $this->startDay;
				$originalStartTime = $this->startTime;
				$originalEndTime   = $this->endTime;

				foreach ($weekdays as $I=>$V) {

					if ($V === TRUE) {
						if ($dateInfo['wday'] > $I) {
							$interval = 7 - $dateInfo['wday'] + $I;
							$interval = "+".$interval." days";

							$this->startDay       = strtotime($interval,$this->startDay);
							$this->startTime      = strtotime($interval,$this->startTime);
							$this->endTime        = strtotime($interval,$this->endTime);

						}
						else if ($dateInfo['wday'] < $I) {
							$interval = "+".($I - $dateInfo['wday'])." days";

							$this->startDay       = strtotime($interval,$this->startDay);
							$this->startTime      = strtotime($interval,$this->startTime);
							$this->endTime        = strtotime($interval,$this->endTime);
						}
						else { // equal
							// $startDayTemp       = $this->startDay;
							// $startTimeTemp      = $this->startTime;
							// $endTimeTemp        = $this->endTime;
						}
						$temp = $this->getSchedule("+1 week");
						$schedule = array_merge($schedule,$temp);
					}


					$this->startDay  = $originalStartDay;
					$this->startTime = $originalStartTime;
					$this->endTime   = $originalEndTime;

				}
			}
		}

		// Every Month (Month Day)
		else if ($frequency == "2") {

			$schedule = $this->getSchedule("+1 Month");

		}

		// Every Month (Week Day)
		else if ($frequency == "3") {

			$interval = "";

			$weekdayOccurence = $this->getWeekdayOccurrence($this->startTime);
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
			$intervalStart = $interval." ".strtolower($weekdayOccurence[1])." +".$_POST['MYSQL']['start_hour']."hours +".$_POST['MYSQL']['start_minute']."minutes" ;
			$intervalEnd   = $interval." ".strtolower($weekdayOccurence[1])." +".$_POST['MYSQL']['end_hour']."hours +".$_POST['MYSQL']['end_minute']."minutes" ;

			$startDay = mktime(0,0,0,$_POST['MYSQL']['start_month'],1,$_POST['MYSQL']['start_year']);

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

		// turn on transactions
		if ($this->db->beginTransaction() === FALSE) {
			$errorMsg .= errorHandle::errorMsg("Failed to begin database transaction. Please contact administrator.");
			return FALSE;
		}

		$submissionError = FALSE;
		$seriesID        = NULL;

		$userInformation = getUserInfo($_POST['MYSQL']['username']);

		if ($userInformation !== FALSE) {

			// put the serial information in the serial table
			$sql       = sprintf("INSERT INTO seriesReservations (`createdOn`,`createdBy`,`createdVia`,`roomID`,`startTime`,`endTime`,`modifiedOn`,`modifiedBy`,`username`,`initials`,`groupname`,`comments`,`allDay`,`frequency`,`weekdays`,`seriesEndDate`,`email`) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
			$sqlResult = $this->db->query($sql,array(
				time(),
				session::get("username"),
				$_POST['MYSQL']['via'],
				$this->room['ID'],
				$this->startTime,
				$this->endTime,
				time(),
				session::get("username"),
				$_POST['MYSQL']['username'],
				$userInformation['initials'],
				$_POST['MYSQL']['groupname'],
				$_POST['MYSQL']['comments'],
				(isset($_POST['MYSQL']['allDay']))?"1":"0",
				$_POST['MYSQL']['frequency'],
				(isset($_POST['MYSQL']['weekday']))?serialize($_POST['MYSQL']['weekday']):"",
				$this->seriesEndDate,
				$_POST['MYSQL']['notificationEmail'])
			);

			if ($sqlResult->error()) {
				$this->db->rollback();
				errorHandle::errorMsg("Failed create series reservation.");
				errorHandle::newError(__FUNCTION__."() - Error creating series: ".$sqlResult->errorMsg(), errorHandle::DEBUG);
				return FALSE;
			}

			if (($seriesID = $sqlResult->insertId()) === 0) {
				$this->db->rollback();
				errorHandle::errorMsg("Failed create series reservation.");
				errorHandle::newError(__FUNCTION__."() - Error creating series -- seriesID error", errorHandle::DEBUG);
				return FALSE;
			}



		}
		else {
			$this->db->rollback();

			$messages = new messages;

			errorHandle::errorMsg($messages->get("invalidUsername"));
			return FALSE;
		}


		if (!isnull($seriesID)) {
			foreach ($schedule as $I=>$V) {

				// set all the needed posted variables
				$_POST['MYSQL']['start_month']  = date("m",$V['startTime']);
				$_POST['MYSQL']['start_day']    = date("d",$V['startTime']);
				$_POST['MYSQL']['start_year']   = date("Y",$V['startTime']);

				$_POST['MYSQL']['start_hour']   = date("H",$V['startTime']);
				$_POST['MYSQL']['start_minute'] = date("i",$V['startTime']);

				// We need to convert the end time back into a duration
				// $duration = $V['endTime'] - $V['startTime'];
				// $hour = floor($duration/60/60);
				// $minute = ($duration/60)%60;

				$duration = $V['endTime'] - $V['startTime'];
				$_POST['MYSQL']['end_hour']     = floor($duration/60/60);
				$_POST['MYSQL']['end_minute']   = ($duration/60)%60;

				// submit the reservation
				$reservation = new reservation;

				$reservation->series = TRUE;

				$reservation->setBuilding($this->building['ID']);
				$reservation->setRoom($this->room['ID']);

				// check the return value. If false, roll back the transactions and stop looping.
				if ($reservation->create($seriesID) === FALSE) {
					$this->db->rollback();
					errorHandle::errorMsg("Failed create series reservation.");
					return FALSE;
				}

			}
		}

		errorHandle::successMsg("Series Reservation successfully created.");
		$this->db->commit();

		return TRUE;
	}

	public function getWeekdayOccurrence($time) {
		$month = intval(date("m", $time)); $day = intval(date("d", $time));
		for ($i = 0; $i < 7; $i++) {
			$days[] = date("l", mktime(0, 0, 0, $month, ($i+1), date("Y", $time)));
		}

		$posd  = array_search(date("l", $time), $days);
		$posdm = array_search($days[0], $days) - $posd;

		return array((($day+$posdm+6)/7), $days[$posd]);
	}

	// @TODO -- is this function no longer needed?
	public function getScheduleMonthWeek($startTime,$endTime,$startDay,$seriesEndDate,$interval) {
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

	public function getSchedule($interval) {

		$schedule = array();

		$workingDay       = 0;
		$workingStartTime = 0;
		$workingEndTime   = 0;
		while ($workingDay <= $this->seriesEndDate) {

			$schedule[] = array(
				'startTime' => (!$workingDay)?$this->startTime:$workingStartTime,
				'endTime'   => (!$workingDay)?$this->endTime:$workingEndTime
				);

			$workingStartTime = strtotime($interval,(!$workingDay)?$this->startTime:$workingStartTime);
			$workingEndTime   = strtotime($interval,(!$workingDay)?$this->endTime:$workingEndTime);
			$workingDay       = strtotime($interval,(!$workingDay)?$this->startDay:$workingDay);
		}

		return($schedule);

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

}

?>
