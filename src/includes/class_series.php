<?php

class series {
	
	public $startTime;
	public $endTime;
	public $startDay;
	public $seriesEndDate;

	public $building;
	public $room;

	public $reservation = NULL;

	public function get($ID) {
		if (!$this->validateID($ID)) {
			return FALSE;
		}

		$reservationID = $_GET['MYSQL']['id'];
		$localvars->set("reservationID",$reservationID);
		$sql       = sprintf("SELECT seriesReservations.*, building.ID as buildingID FROM `seriesReservations` LEFT JOIN `rooms` ON rooms.ID=seriesReservations.roomID LEFT JOIN `building` ON building.ID=rooms.building WHERE seriesReservations.ID=?");
		$sqlResult = $db->query($sql,array($reservationID));

		if ($sqlResult->error()) {
			errorHandle::newError($sqlResult->errorMsg(), errorHandle::DEBUG);
			$error = TRUE;
		}
		else {

			$reservationInfo = $sqlResult->fetch();
			$username        = $reservationInfo['username'];
			$groupname       = $reservationInfo['groupname'];
			$comments        = $reservationInfo['comments'];

			http::setPost("library",$reservationInfo['buildingID']);
			http::setPost("room",$reservationInfo['roomID']);
			http::setGet("library",$reservationInfo['buildingID']);
			http::setGet("room",$reservationInfo['roomID']);

			$action = "Update";

			if (!is_empty($reservationInfo['weekdays'])) {
				$weekdaysAssigned = unserialize($reservationInfo['weekdays']);
			}

		}

	}

	private function validateID($ID) {
		return validate::getInstance()->integer($ID);
	}

	public function delete($ID) {

		$transResult = $db->beginTransaction();

		$sql       = sprintf("DELETE FROM `reservations` WHERE seriesID=? AND startTime>?");
		$sqlResult = $db->query($sql,array($ID,time()));

		if ($sqlResult->error()) {
			$db->rollback();
			errorHandle::newError($sqlResult->errorMsg(), errorHandle::DEBUG);
			errorHandle::errorMsg("Error deleting series reservation.");
			return FALSE;
		}

		$sql       = sprintf("DELETE FROM `seriesReservations` WHERE ID=?");
		$sqlResult = $db->query($sql,array($ID));

		if ($sqlResult->error()) {
			$db->rollback();

			errorHandle::successMsg("Series Reservation Deleted.");

			return FALSE;
		}

		$db->commit();
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
			if ($workingDay == 0) {
				$schedule[] = array(
					'startTime' => $this->startTime,
					'endTime'   => $this->endTime
					);

				$workingDay       = strtotime($interval,$this->startDay);
				$workingStartTime = strtotime($interval,$this->startTime);
				$workingEndTime   = strtotime($interval,$this->endTime);

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

}

?>