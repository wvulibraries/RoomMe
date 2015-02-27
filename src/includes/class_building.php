<?php

class building {
	
	private $buildings = array();

	private $db;
	private $engine;
	private $localvars;

	function __construct() {

		$this->engine    = EngineAPI::singleton();
		$this->localvars = localvars::getInstance();
		$this->db        = db::get($this->localvars->get('dbConnectionName'));

	}

	public function get($ID) {

		if (!validate::getInstance()->integer($ID)) {
			return FALSE;
		}

		if (isset($this->buildings[$ID]) && !is_empty($this->buildings[$ID]['name'])) {
			return $this->buildings[$ID]['name'];
		}

		$sql       = sprintf("SELECT * FROM building WHERE `ID`=? LIMIT 1");
		$sqlResult = $this->db->query($sql,array($ID));

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

	public function getall() {

		$sql       = sprintf("SELECT * FROM building ORDER BY name");
		$sqlResult = $this->db->query($sql);

		if ($sqlResult->error()) {
			errorHandle::newError(__FUNCTION__."() - Error getting building name.", errorHandle::DEBUG);
			return(FALSE);
		}

		if ($sqlResult->rowCount() < 1) {
			errorHandle::errorMsg("No Buildings Found");
			return FALSE;
		}

		while ($row = $sqlResult->fetch()) {
			$this->buildings[$row['ID']] = $row;
		}

		return $this->buildings;

	}

	public function calendarList() {

		$buildings = $this->getall();

		$output = "<ul>";
		foreach ($buildings as $building) {

			if (is_empty($building['externalURL'])) { 
				$url = sprintf('%s/calendar/building/?building=%s',
					$this->localvars->get("roomResBaseDir"),
					$building['ID']
					);
			}
			else {
				$url = $building['externalURL'];
			}
				
			$output .= sprintf('<li><a href="%s">%s</a></li>',
				$url,
				htmlSanitize($building['name'])
				);
		}
		$output .= "</ul>";

		return $output;

	}


}

?>