<?php
require_once("../engineHeader.php");
recurseInsert("includes/functions.php","php");

function defineFields() {
	$fields                             = array();
	$fields['reservationCreated']       = getResultMessage('reservationCreated');
	$fields['reservationUpdated']       = getResultMessage('reservationUpdated');
	$fields['dataValidationError']      = getResultMessage('dataValidationError');
	$fields['invalidUsername']          = getResultMessage('invalidUsername');
	$fields['invalidDate']              = getResultMessage('invalidDate');
	$fields['endBeforeStart']           = getResultMessage('endBeforeStart');
	$fields['duplicateReservation']     = getResultMessage('duplicateReservation');
	$fields['reservationConflict']      = getResultMessage('reservationConflict');
	$fields['policyError']              = getResultMessage('policyError');
	$fields['sameDayReservation']    	= getResultMessage('sameDayReservation');
	$fields['systemsPolicyError']       = getResultMessage('systemsPolicyError');
	$fields['maxFineExceeded']          = getResultMessage('maxFineExceeded');
	$fields['multipleRoomBookings']     = getResultMessage('multipleRoomBookings');
	$fields['patronReservationInfo']    = getResultMessage('patronReservationInfo');
	$fields['policyLabel']              = getResultMessage('policyLabel');
	$fields['libraryClose']             = getResultMessage('libraryClose');
	$fields['reservationInPast']        = getResultMessage('reservationInPast');
	$fields['reservationLengthTooLong'] = getResultMessage('reservationLengthTooLong');
	$fields['userOverSystemHours']      = getResultMessage('userOverSystemHours');
	$fields['userOverLibraryHours']     = getResultMessage('userOverLibraryHours');
	$fields['userOverPolicyHours']      = getResultMessage('userOverPolicyHours');
	$fields['userOverSystemBookings']   = getResultMessage('userOverSystemBookings');
	$fields['userOverBuildingBookings'] = getResultMessage('userOverBuildingBookings');
	$fields['userOverPolicyBookings']   = getResultMessage('userOverPolicyBookings');
	$fields['errorInserting']           = getResultMessage('errorInserting');
	$fields['tooFarInFuture']           = getResultMessage('tooFarInFuture');
	
	return($fields);
}

$fields = defineFields();

if(isset($_POST['MYSQL']['sysconfig_submit'])) {

	$error = FALSE;

	foreach ($fields as $name=>$value) {

		if (is_empty($_POST['MYSQL'][$name])) {
			errorHandle::errorMsg($name." left blank.");
			$error = TRUE;
		}

		if ($error === FALSE) {
			$fields[$name] = $_POST['MYSQL'][$name];

			$db        = db::get($localvars->get('dbConnectionName'));
			$sql       = sprintf("UPDATE `resultMessages` SET `value`=? WHERE `name`=?");
			$sqlResult = $db->query($sql,array($_POST['MYSQL'][$name],$name));

			if ($sqlResult->error()) {
				errorHandle::newError(__METHOD__."() - ".$sqlResult['error'], errorHandle::DEBUG);
				errorHandle::errorMsg($name." not updated correctly. Other fields may still be updated.");
				$error = TRUE;
			}

		}
	}

	if ($error === FALSE) {
		errorHandle::successMsg("All fields updated successfully");
	}

	$fields = defineFields();
}

$localvars->set("prettyPrint",errorHandle::prettyPrint());

templates::display('header');
?>

<header>
<h1>Result Message Configuration</h1>
</header>

{local var="prettyPrint"}

<form action="" method="post">

	{csrf}

	<table>
		<?php foreach($fields as $name=>$value) { ?>

		<tr>
			<td>
				<label for="<?php print $name; ?>"><?php print $name; ?></label>
			</td>
			<td>

				<textarea id="<?php print $name; ?>" name="<?php print $name; ?>"><?php print $value; ?></textarea>

			</td>
		</tr>

		<?php }	?>
	</table>

	<input type="submit" name="sysconfig_submit" value="submit" />
</form>


<?php
templates::display('footer');
?>