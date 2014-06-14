<?php
require_once("../engineHeader.php");
recurseInsert("includes/functions.php","php");

function defineFields() {
	$fields                                 = array();
	$fields['24hour']                       = getConfig('24hour');
	$fields['adjustedDeleteTime']           = getConfig('adjustedDeleteTime');
	$fields['allowMultipleBookings']        = getConfig('allowMultipleBookings');
	$fields['calendarDisplayName']          = getConfig('calendarDisplayName');
	$fields['defaultReservationIncrements'] = getConfig('defaultReservationIncrements');
	$fields['displayNameAs']                = getConfig('displayNameAs');
	$fields['maxBookingsAllowedSystem']     = getConfig('maxBookingsAllowedSystem');
	$fields['maxFineAllowedSystem']         = getConfig('maxFineAllowedSystem');
	$fields['maxHoursAllowedSystem']        = getConfig('maxHoursAllowedSystem');
	$fields['maxHoursAllowedSystem']        = getConfig('maxHoursAllowedSystem');
	$fields['daysToDisplayOnCancelledPage'] = getConfig('daysToDisplayOnCancelledPage');
	$fields['displayDurationOnRoomsCal']    = getConfig('displayDurationOnRoomsCal');
	$fields['displayDurationOnBuildingCal'] = getConfig('displayDurationOnBuildingCal');
	$fields['hoursOnReservationTable']      = getConfig('hoursOnReservationTable');

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

		if ($name == "displayNameAs" && ($_POST['MYSQL'][$name] != "username" &&  $_POST['MYSQL'][$name] != "initials")) {
			errorHandle::errorMsg($name." must be 'username' or 'initials'");
			$error = TRUE;
		}


		if ($error === FALSE) {
			$fields[$name] = $_POST['MYSQL'][$name];

			$db        = db::get($localvars->get('dbConnectionName'));
			$sql       = sprintf("UPDATE `siteConfig` SET `value`=? WHERE `name`=?");
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
<h1>System Configuration</h1>
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
				<?php 
				// I hate how i'm doing this. product of needing to get it done instead of doing it well. 
				// TODO: Fix this!
				if ($name == "24hour" || $name=="allowMultipleBookings" || $name=="displayDurationOnRoomsCal" || $name=="displayDurationOnBuildingCal" || $name=="hoursOnReservationTable") { ?>

				<select name="<?php print $name; ?>">
					<option value="0" <?php print ($value=="0")?"selected":""; ?>>No</option>
					<option value="1" <?php print ($value=="1")?"selected":""; ?>>Yes</option>
				</select>

				<?php } else { ?>

				<input type="text" id="<?php print $name; ?>" name="<?php print $name; ?>" value="<?php print $value; ?>" />

				<?php } ?>
			</td>
		</tr>

		<?php }	?>
	</table>

	<input type="submit" name="sysconfig_submit" value="submit" />
</form>


<?php
templates::display('footer');
?>