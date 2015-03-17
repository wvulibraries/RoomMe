<?php

class room {
	
	private $rooms = array();
	private $engine;
	private $localvars;
	private $db;

	function __construct() {

		$this->engine    = EngineAPI::singleton();
		$this->localvars = localvars::getInstance();
		$this->db        = db::get($this->localvars->get('dbConnectionName'));

	}

	public function get($ID) {

		if (!validate::getInstance()->integer($ID)) {
			return FALSE;
		}

		if (isset($this->rooms[$ID]) && !is_empty($this->rooms[$ID]['name'])) {
			return $this->rooms[$ID]['name'];
		}

		$sql       = sprintf("SELECT * FROM rooms WHERE `ID`=?");
		$sqlResult = $this->db->query($sql,array($ID));

		if ($sqlResult->error()) {
			errorHandle::newError(__FUNCTION__."() - Error getting room name.", errorHandle::DEBUG);
			return(FALSE);
		}

		if ($sqlResult->rowCount() < 1) {
			errorHandle::errorMsg("Room not found.");
			return FALSE;
		}

		$this->rooms[$ID] = $sqlResult->fetch();

		return $this->rooms[$ID];

	}


	public function getall() {

		$sql       = sprintf("SELECT * FROM rooms ORDER BY NAME");
		$sqlResult = $this->db->query($sql,array($ID));

		if ($sqlResult->error()) {
			errorHandle::newError(__FUNCTION__."() - Error getting room name.", errorHandle::DEBUG);
			return(FALSE);
		}

		if ($sqlResult->rowCount() < 1) {
			errorHandle::errorMsg("Room not found.");
			return FALSE;
		}

		while($row = $sqlResult->fetch()) {
			$this->rooms[$ID] = $row;
		}

		return $this->rooms;

	}

	public function selectRoomListOptions($anyOption=FALSE,$buildingID=NULL,$roomID=NULL) {

		$building = new building;
		$rooms = (isnull($buildingID))?$this->getall():$building->getRooms($buildingID);

		$options = ($anyOption)?'<option value="any">Any Room</a>':"";
		foreach ($rooms as $room) {
			$options .= sprintf('<option value="%s" %s>%s - %s</option>',
				htmlSanitize($room['ID']),
				($roomID == $room['ID'])?"selected":"",
				htmlSanitize($room['name']),
				htmlSanitize($room['number'])
				);
		}

		return $options;

	}

	public function getPicture($ID) {

		$room = $this->get($ID);

		if (!isset($room['pictureURL']) || is_empty($room['pictureURL'])) {
			return "";
		}

		return sprintf('<img src="%s" id="roomPicture" alt="%s -- %s" />',$room['pictureURL'],$room['name'],$room['number']);

	}
}