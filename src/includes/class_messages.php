<?php

class messages {

	private $engine;
	private $localvars;
	private $db;
	private $messages = array();

	function __construct() {
		$this->engine    = EngineAPI::singleton();
		$this->localvars = localvars::getInstance();
		$this->db        = db::get($this->localvars->get('dbConnectionName'));
		$this->messages  = $this->getMessages();
	}

	public function get($name=NULL) {

		if (isnull($name)) {
			return $messages;
		}

		if (isset($messages[$name])) return $messages[$name];

		return "";

	}

	private function getMessages() {
		$messages                             = array();
		$messages['reservationCreated']       = $this->getMessage('reservationCreated');
		$messages['reservationUpdated']       = $this->getMessage('reservationUpdated');
		$messages['dataValidationError']      = $this->getMessage('dataValidationError');
		$messages['invalidUsername']          = $this->getMessage('invalidUsername');
		$messages['invalidDate']              = $this->getMessage('invalidDate');
		$messages['endBeforeStart']           = $this->getMessage('endBeforeStart');
		$messages['duplicateReservation']     = $this->getMessage('duplicateReservation');
		$messages['reservationConflict']      = $this->getMessage('reservationConflict');
		$messages['policyError']              = $this->getMessage('policyError');
		$messages['sameDayReservation']       = $this->getMessage('sameDayReservation');
		$messages['systemsPolicyError']       = $this->getMessage('systemsPolicyError');
		$messages['maxFineExceeded']          = $this->getMessage('maxFineExceeded');
		$messages['multipleRoomBookings']     = $this->getMessage('multipleRoomBookings');
		$messages['patronReservationInfo']    = $this->getMessage('patronReservationInfo');
		$messages['policyLabel']              = $this->getMessage('policyLabel');
		$messages['libraryClose']             = $this->getMessage('libraryClose');
		$messages['reservationInPast']        = $this->getMessage('reservationInPast');
		$messages['reservationLengthTooLong'] = $this->getMessage('reservationLengthTooLong');
		$messages['userOverSystemHours']      = $this->getMessage('userOverSystemHours');
		$messages['userOverLibraryHours']     = $this->getMessage('userOverLibraryHours');
		$messages['userOverPolicyHours']      = $this->getMessage('userOverPolicyHours');
		$messages['userOverSystemBookings']   = $this->getMessage('userOverSystemBookings');
		$messages['userOverBuildingBookings'] = $this->getMessage('userOverBuildingBookings');
		$messages['userOverPolicyBookings']   = $this->getMessage('userOverPolicyBookings');
		$messages['errorInserting']           = $this->getMessage('errorInserting');
		$messages['tooFarInFuture']           = $this->getMessage('tooFarInFuture');
		$messages['emailNotProvided']         = $this->getMessage('emailNotProvided');

		return $messages;
	}

	private function getMessage($name) {

		$sql       = sprintf("SELECT `value` FROM `resultMessages` WHERE `name`=?");
		$sqlResult = $this->db->query($sql,array($name));

		if ($sqlResult->error()) {
			errorHandle::newError(__FUNCTION__."() - ".$sqlResult->errorMsg(), errorHandle::DEBUG);
			return("");
		}

		$row       = $sqlResult->fetch();
		return($row['value']);

	}

	public function setMessages() {

		$success = TRUE;

		foreach ($this->messages as $name=>$value) {
			if (isset($_POST['MYSQL'][$name]) && !is_empty($_POST['MYSQL'][$name]) && $_POST['MYSQL'][$name] != $value) {
				$result = $this->setMessage($name,$_POST['MYSQL'][$name]);
				if (!$result) $success = $result;
			}
		}

		$this->messages = $this->getMessages();

		return $success;
	}

	private function setMessage($name,$value) {

		$sql       = "UPDATE `resultMessages` SET `value`=? WHERE `name`=? LIMIT 1";
		$sqlResult = $this->db->query($sql,array($value,$name));

		if ($sqlResult->error()) {
			errorHandle::newError($sqlResult->errorMsg(), errorHandle::DEBUG);
			errorHandle::errorMsg($name." not updated correctly. Other fields may still be updated.");
			return FALSE;
		}

		return TRUE;

	}

	public function buildEditTable() {
		$output = "<table>";
		
		foreach ($this->messages as $name=>$value) {
			$output .= sprintf('<tr><td><label for="%s">%s</label></td><td><textarea id="%s" name="%s">%s</textarea></td></tr>',
				htmlSanitize($name),
				htmlSanitize($name),
				htmlSanitize($name),
				htmlSanitize($name),
				htmlSanitize($value)
				);

		}

		$output .= "</table>";

		return $output;

	}

}


?>