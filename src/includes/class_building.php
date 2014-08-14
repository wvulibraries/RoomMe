<?php

class building {
	
	private $buildings = array();

	function __construct() {
	}

	public function get($ID) {

		if (!validate::getInstance()->integer($ID)) {
			return FALSE;
		}

		if (isset($this->buildings[$ID]) && !is_empty($this->buildings[$ID]['name'])) {
			return $this->buildings[$ID]['name'];
		}

		$engine    = EngineAPI::singleton();
		$localvars = localvars::getInstance();
		$db        = db::get($localvars->get('dbConnectionName'));

		$sql       = sprintf("SELECT * FROM building WHERE `ID`=? LIMIT 1");
		$sqlResult = $db->query($sql,array($ID));

		if ($sqlResult->error()) {
			errorHandle::newError(__FUNCTION__."() - Error getting building name.", errorHandle::DEBUG);
			return(FALSE);
		}

		if ($sqlResult->rowCount() < 1) {
			errorHandle::errorMsg("Building not found.");
			return FALSE;
		}

		$this->buildings[$ID] = $sqlResult->fetch();

		return $this->buildings[$ID];

	}

}

?>