<?php

	require_once("../../../../engineHeader.php");

	$errorMsg = "";
	$error    = FALSE;
	$id       = NULL;

	$db       = db::get($localvars->get('dbConnectionName'));

	$reservationPermissions = new reservationPermissions;

	try {

		if (isset($_POST['MYSQL']['multiDelete'])) {
			foreach ($_POST['MYSQL']['delete'] as $reservationID) {
				$reservationPermissions->deleteRecord($reservationID);
			}
		}

	}
	catch (Exception $e) {
		errorHandle::errorMsg($e->getMessage());
	}

	$localvars->set('table', $reservationPermissions->renderDataTable());

	templates::display('header');
?>

<header>
	<h1>Reservation Permissions</h1>
</header>

{local var="table"}

<?php
templates::display('footer');
?>
