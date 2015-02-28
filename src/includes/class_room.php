<?php

class room {
	
	private $rooms = array();

	function __construct() {
	}

	public function get($ID) {

		if (!validate::getInstance()->integer($ID)) {
			return FALSE;
		}

		if (isset($this->rooms[$ID]) && !is_empty($this->rooms[$ID]['name'])) {
			return $this->rooms[$ID]['name'];
		}

		$engine    = EngineAPI::singleton();
		$localvars = localvars::getInstance();
		$db        = db::get($localvars->get('dbConnectionName'));

		$sql       = sprintf("SELECT * FROM rooms WHERE `ID`=?");
		$sqlResult = $db->query($sql,array($ID));

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

	public function getPicture($ID) {

		$room = $this->get($ID);

		if (!isset($room['pictureURL']) || is_empty($room['pictureURL'])) {
			return "";
		}

		return sprintf('<img src="%s" id="roomPicture" />',$room['pictureURL']);

	}
}