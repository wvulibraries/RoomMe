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
	$fields['patronReservationInfo']    = getResultMessage('patronReservationInfo');
	$fields['libraryClose']             = getResultMessage('libraryClose');
	$fields['reservationLengthTooLong'] = getResultMessage('reservationLengthTooLong');
	$fields['userOverSystemHours']      = getResultMessage('userOverSystemHours');
	$fields['userOverLibraryHours']     = getResultMessage('userOverLibraryHours');
	$fields['userOverPolicyHours']      = getResultMessage('userOverPolicyHours');
	$fields['userOverSystemBookings']   = getResultMessage('userOverSystemBookings');
	$fields['userOverBuildingBookings'] = getResultMessage('userOverBuildingBookings');
	$fields['userOverPolicyBookings']   = getResultMessage('userOverPolicyBookings');
	$fields['errorInserting']           = getResultMessage('errorInserting');
	$fields['tooFarInFuture']           = getResultMessage('tooFarInFuture');
	$fields['policyLabel']              = getResultMessage('policyLabel');

	return($fields);
}

$fields = defineFields();

if(isset($engine->cleanPost['MYSQL']['sysconfig_submit'])) {

	$error = FALSE;

	foreach ($fields as $name=>$value) {

		if (isempty($engine->cleanPost['MYSQL'][$name])) {
			errorHandle::errorMsg($name." left blank.");
			$error = TRUE;
		}

		if ($error === FALSE) {
			$fields[$name] = $engine->cleanPost['MYSQL'][$name];

			$sql       = sprintf("UPDATE `resultMessages` SET `value`='%s' WHERE `name`='%s'",
				$engine->cleanPost['MYSQL'][$name],
				$engine->openDB->escape($name)
				);
			$sqlResult = $engine->openDB->query($sql);

			if (!$sqlResult['result']) {
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

localvars::add("prettyPrint",errorHandle::prettyPrint());

$engine->eTemplate("include","header");
?>

<header>
<h1>Result Message Configuration</h1>
</header>

{local var="prettyPrint"}

<form action="" method="post">

	{csrf insert="post"}

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
$engine->eTemplate("include","footer");
?>