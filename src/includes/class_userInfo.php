<?php

class userInfo {
	
	private $engine;
	private $authDB;
	private $db;
	private $localvars;

	public $user = NULL;
	public $reservations = array();


	function __construct() {

		$this->localvars = localvars::getInstance();
		$this->engine    = EngineAPI::singleton();
		$this->db        = db::get($this->localvars->get('dbConnectionName'));

		// database settings for vagrant box, overridden in production with require below
		$databaseOptions = array(
			'username' => 'username',
			'password' => 'password'
			);

		// @TODO this needs to be in a config file
		require '/home/www.libraries.wvu.edu/phpincludes/databaseConnectors/database.lib.wvu.edu.remote.php';
		$databaseOptions['dbName'] = "authentication";
		$this->authDB              = db::create('mysql', $databaseOptions, 'authDB');

	}

	// $user can be a number or a string. If string, looks up by username. if number, looks up by ID number
	public function get($user) {

		if (is_string($user)) {
			// This sql statement would need to be updated for other database types
			$sql = sprintf("SELECT master.*, accountUsernames.username as username FROM accountUsernames LEFT JOIN master on master.uid=accountUsernames.uid WHERE accountUsernames.username=?");
		}
		else if (is_numeric($user)) {
			$sql = sprintf("SELECT master.*, accountUsernames.username as username FROM `master` LEFT JOIN `accountUsernames` on accountUsernames.uid=master.uid WHERE master.uid=?");
		}
		else {
			return FALSE;
		}

		$sqlResult = $this->authDB->query($sql,array($user));

		if ($sqlResult->error()) {
			errorHandle::newError(__FUNCTION__."() - ".$sqlResult->errorMsg(), errorHandle::DEBUG);
			return FALSE;
		}

		if ($sqlResult->rowCount() < 1) {
			return FALSE;
		}
		else if ($sqlResult->rowCount() > 1) {
			errorHandle::newError(__FUNCTION__."() - More than one user returned.", errorHandle::DEBUG);
			return FALSE;
		}

		$this->user             = $sqlResult->fetch();
		$this->user['initials'] = $this->getInitials();
		$this->user['idNumber'] = $this->user['uid'];

		return TRUE;
	}

	// sets the $this->reservations array with all of the users reservations
	public function getReservations() {

		$sql       = sprintf("SELECT ID FROM `reservations` WHERE `username`=? ORDER BY `startTime`");
		$sqlResult = $this->db->query($sql,array($this->user['username']));
		
		if ($sqlResult->error()) {
			errorHandle::newError(__METHOD__."() - : ".$sqlResult->errorMsg(), errorHandle::DEBUG);
			return FALSE;
		}
		
		while($row = $sqlResult->fetch()) {
			$reservation                    = new reservation;
			$reservation->get($row['ID']);
			$this->reservations[$row['ID']] = $reservation;
		}
		
		return TRUE;

	}

	public function getInitials() {

		if ($this->is_new()) {
			return FALSE;
		}

		return substr($this->user['firstname'],0,1).substr($this->user['lastname'],0,1);

	}

	public function is_new() {
		if (isnull($this->user)) {
			return TRUE;
		}

		return FALSE;
	}

}

?>