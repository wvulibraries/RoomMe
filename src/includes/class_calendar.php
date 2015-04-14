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
		$this->localvars->set("calType",$this->calendarType);

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

	private function calendarType() {
		if (isnull($this->room)) {
			return "building";
		}
		else {
			return "room";
		}
	}

}