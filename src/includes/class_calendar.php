<?php

class calendar {

	private $db;
	private $engine;
	private $localvars;

	// If modal is true, we set everything up for the event handlers. If modal
	// is false, we set everything up for submission back to itself. 
	private $modal = TRUE;

	// Room trumps building, if both are set as the calendar type, because it is
	// the more specific type
	private $building        = NULL;
	private $room            = NULL;
	private $calObject       = NULL;
	private $buildingRoomIDs = array();

	public $dates           = array();

	function __construct($modal=TRUE) {

		if (!$modal) $this->modal = FALSE;

		$this->engine    = EngineAPI::singleton();
		$this->localvars = localvars::getInstance();
		$this->db        = db::get($this->localvars->get('dbConnectionName'));

		if (isset($_GET['MYSQL']['building']) && !is_empty($_GET['MYSQL']['building'])) $this->setBuilding($_GET['MYSQL']['building']);
		if (isset($_GET['MYSQL']['room'])     && !is_empty($_GET['MYSQL']['room']))     $this->setRoom($_GET['MYSQL']['room']);

		if ($this->building === FALSE || $this->room === FALSE) {
			throw new Exception("Invalid building or Room");
		}

		$this->setDates();

	}

	public function setBuilding($id) {

		$buildingObject = new building;
			
		if (($this->building = $buildingObject->get($id)) === FALSE) {
			return FALSE;
		}

		if (($this->buildingRoomIDs = $buildingObject->getRoomIDs($id)) === FALSE) {
			return FALSE;
		}

		// We only set calObject to building if the room hasn't been set yet.
		if (isnull($this->room)) $this->calObject = $this->building;

		return TRUE;
	}

	public function setRoom($id) {
		$roomObject = new room;
			
		if (($this->room = $roomObject->get($id)) === FALSE) {
			return FALSE;
		}

		$this->calObject = $this->room;

		return TRUE;
	}


	public function setDates() {

		// The display date is the day of the calendar that we are displaying
		// If the date is provided via a query string, we use that date. Otherwise
		// we use the current date. 
		$this->dates['display']['month'] = (isset($_GET['MYSQL']['month']))?$_GET['MYSQL']['month']:date("n");
		$this->dates['display']['day']   = (isset($_GET['MYSQL']['day']))?$_GET['MYSQL']['day']:date("d");
		$this->dates['display']['year']  = (isset($_GET['MYSQL']['year']))?$_GET['MYSQL']['year']:date("Y");

		// get the current (today) day
		$this->dates['today']['month'] = date("n");
		$this->dates['today']['day']   = date("d");
		$this->dates['today']['year']  = date("Y");

		// Get the previous day (compared to the display date)
		$this->dates['prev']['month'] = date("n",strtotime("-1 day",mktime(0,0,0,$this->dates['display']['month'],$this->dates['display']['day'],$this->dates['display']['year'])));
		$this->dates['prev']['day']   = date("d",strtotime("-1 day",mktime(0,0,0,$this->dates['display']['month'],$this->dates['display']['day'],$this->dates['display']['year'])));
		$this->dates['prev']['year']  = date("y",strtotime("-1 day",mktime(0,0,0,$this->dates['display']['month'],$this->dates['display']['day'],$this->dates['display']['year'])));

		// Get the previous day (compared to the display date)
		$this->dates['next']['month'] = date("n",strtotime("+1 day",mktime(0,0,0,$this->dates['display']['month'],$this->dates['display']['day'],$this->dates['display']['year'])));
		$this->dates['next']['day']   = date("d",strtotime("+1 day",mktime(0,0,0,$this->dates['display']['month'],$this->dates['display']['day'],$this->dates['display']['year'])));
		$this->dates['next']['year']  = date("y",strtotime("+1 day",mktime(0,0,0,$this->dates['display']['month'],$this->dates['display']['day'],$this->dates['display']['year'])));

		return TRUE;

	}

	// This is a temporary function under drawRoomCalendar is moved in
	public function getRoomID() {

		if ($this->calendarType() == "building") {
			return $this->buildingRoomIDs;
		}

		return $this->calObject['ID'];
	}

	public function buildDateSelects() {

		$output = "";

		$output .= '<select name="start_month_modal" id="start_month_modal" style="width: 75px;">';
		for($I=1;$I<=12;$I++) {
			$output .= sprintf('<option value="%s" %s>%s</option>',
				$I,
				($I == $this->dates['display']['month'])?"selected":"",
				$I);
		}
		$output .= '</select>';

		$output .= '<select name="start_day_modal" id="start_day_modal" style="width: 75px;">';
		for($I=1;$I<=31;$I++) {
			$output .= sprintf('<option value="%s" %s>%s</option>',
				($I < 10)?"0".$I:$I,
				($I == $this->dates['display']['day'])?"selected":"",
				$I);
		}
		$output .= '</select>';

		$output .= '<select name="start_year_modal" id="start_year_modal" style="width: 75px;">';
		for($I=$this->dates['today']['year'];$I<=$this->dates['today']['year']+10;$I++) {
			$output .= sprintf('<option value="%s" %s>%s</option>',
				$I,
				($I == $this->dates['display']['year'])?"selected":"",
				$I);
		}
		$output .= '</select>';

		$output .= sprintf('<button id="calUpdateFormSubmit" data-type="%s" data-id="%s" data-modal="%s" style="margin-top: -8px">Jump to Date</button>',
			$this->calendarType(),
			$this->calObject['ID'],
			($this->modal)?"true":"false"
			);

		$output .= '<br />';
		$output .= sprintf('<button class="calUpdateButton" id="prevDayButton" data-type="%s" data-id="%s" data-modal="%s" data-month="%s"  data-day="%s"  data-year="%s">&lt;&lt; Previous</button>',
			$this->calendarType(),
			$this->calObject['ID'],
			($this->modal)?"true":"false",
			$this->dates['prev']['month'],
			$this->dates['prev']['day'],
			$this->dates['prev']['year']
			);
		$output .= sprintf('<button class="calUpdateButton" id="todayButton" data-type="%s" data-id="%s" data-modal="%s" data-month="%s" data-day="%s" data-year="%s">Today</button>',
			$this->calendarType(),
			$this->calObject['ID'],
			($this->modal)?"true":"false",
			$this->dates['today']['month'],
			$this->dates['today']['day'],
			$this->dates['today']['year']
			);
		$output .= sprintf('<button class="calUpdateButton" id="nextDayButton" data-type="%s" data-id="%s" data-modal="%s" data-month="%s"  data-day="%s"  data-year="%s">Next &gt;&gt;</button>',
			$this->calendarType(),
			$this->calObject['ID'],
			($this->modal)?"true":"false",
			$this->dates['next']['month'],
			$this->dates['next']['day'],
			$this->dates['next']['year']
			);

		return $output;

	}

	public function setLocalvars() {

		$this->localvars->set("id",$this->calObject['ID']);
		$this->localvars->set("name",$this->calObject['name']);
		$this->localvars->set("calType",$this->calendarType());

		$this->localvars->set("month",$this->dates['display']['month']);
		$this->localvars->set("day",$this->dates['display']['day']);
		$this->localvars->set("year",$this->dates['display']['year']);

		$this->localvars->set("todayMonth",$this->dates['today']['month']);
		$this->localvars->set("todayDay",$this->dates['today']['day']);
		$this->localvars->set("todayYear",$this->dates['today']['year']);

		// get the previous Day
		$this->localvars->set("prevMonth",$this->dates['prev']['month']);
		$this->localvars->set("prevDay",$this->dates['prev']['day']);
		$this->localvars->set("prevYear",$this->dates['prev']['year']);

		// get the next Day
		$this->localvars->set("nextMonth",$this->dates['next']['month']);
		$this->localvars->set("nextDay",$this->dates['next']['day']);
		$this->localvars->set("nextYear",$this->dates['next']['year']);

		return TRUE;

	}

	public function buildBuildingCal($objectID,$date) {

		$calendarArray = array();

		# Get building Rooms
		$buildingObject = new building;
		$rooms = $buildingObject->getRooms($objectID);

		
		# Make sure that date is an array
		if (!is_array($date)) {
			errorHandle::newError(__FUNCTION__."() - date not given as array", errorHandle::DEBUG);
			return(FALSE);
		}

		$roomsInformation    = array();

		$calendarDisplayName = getConfig('calendarDisplayName');



		$usernameCheck = array();

		$displayHour   = getConfig("24Hour");
		$displayHour   = ($displayHour == 0)?12:24;

		$displayNameAs = getConfig("displayNameAs");
		$durationRooms = getConfig("displayDurationOnRoomsCal");
		$durationBuild = getConfig("displayDurationOnBuildingCal");

		$calendarArray['times'] = array();

		for ($I = 0;$I<=23;$I++) {

			for ($K = 0;$K<60;$K=$K+15) {

				switch($K) {
					case 0:
						$hourMarker = "hour";
						break;
					case 30:
						$hourMarker = "half";
						break;
					case 15:
						$hourMarker = "quarterPast";
						break;
					case 45:
						$hourMarker = "quarterTill";
						break;
					default:
						$hourMarker = "minor";
						break;
				}

				$calendarArray['times'][mktime($I,$K,"0",$date['month'],$date['day'],$date['year'])] = array(
					'time'    => mktime($I,$K,"0",$date['month'],$date['day'],$date['year']),
					'type'    => $hourMarker,
					'display' => ($displayHour == 24)?$I:(($I==12)?"12pm":(($I>=13)?($I-12)."pm":(($I == 0)?"12am":$I."am")))
					);

			}
		}

		foreach ($rooms as $roomIndex=>$room) {

			$roomInfo         = getRoomInfo($room['ID']);
			$bookings         = getRoomBookingsForDate($room['ID'],$date['month'],$date['day'],$date['year']);
			
			$roomArray                = array();
			$roomArray['displayName'] = $roomInfo['displayName'];
			$roomArray['roomID']      = $roomInfo['ID'];

			foreach ($calendarArray['times'] as $time=>$timeInfo) {

				$roomArray['times'][$time]['username']    = "";
				$roomArray['times'][$time]['displayTime'] = "";
				$roomArray['times'][$time]['duration']    = "";
				$roomArray['times'][$time]['reserved']    = FALSE;
				$roomArray['times'][$time]['booking']     = "";
				$roomArray['times'][$time]['hourType']    = $timeInfo['type'];

				foreach ($bookings as $bookingsIndex=>$booking) {

					if ($time >= $booking['startTime'] && $time < $booking['endTime']) {

						$roomArray['times'][$time]['booking']  = $booking['ID'];
						$roomArray['times'][$time]['reserved'] = TRUE;

						if ($durationRooms == "1" || $durationBuild == "1") {
							$roomArray['times'][$time]['duration'] = ($booking['endTime'] - $booking['startTime'])/60/60;
							$roomArray['times'][$time]['duration'] = "(".$duration." hour".(($duration!=1)?"s":"").")";
						}

						switch($displayNameAs) {
							case "username":
								$roomArray['times'][$time]['username'] = (!is_empty($booking['groupname']))?$booking['groupname']:$booking['username'];
								break;
							case "initials":
								$roomArray['times'][$time]['username'] = $booking['initials'];
								break;
							default:
								break;
						}

						if ($displayHour == "1") {
							$timeFormat = "H:i";
						}
						else {
							$timeFormat = "g:iA";
						}

						$roomArray['times'][$time]['displayTime'] = sprintf('%s - %s',
							date($timeFormat,$booking['startTime']),
							date($timeFormat,$booking['endTime'])
							);
					}
				}
			}

			$calendarArray['rooms'][] = $roomArray;

		}

		return $calendarArray;

	}

	public function buildJSON($type,$objectID,$date) {

		switch ($type) {
			case "building":
				$array = $this->buildBuildingCal($objectID,$date);
				break;
			case "room":
				break;
			default:
				break;
		}

		return json_encode($array);

	}

	private function calendarType() {
		if (isnull($this->room)) {
			return "building";
		}
		else {
			return "room";
		}
	}

}