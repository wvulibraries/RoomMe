	<?php

		require_once("../../../../engineHeader.php");

		$errorMsg = "";
		$error    = FALSE;
		$id       = NULL;

		$db       = db::get($localvars->get('dbConnectionName'));

		$reservationPermissions = new reservationPermissions;

		try {
			// Is this an Update?
			if (isset($_GET['MYSQL']['id']) ) {

				$id = $_GET['MYSQL']['id'];
				$data = $reservationPermissions->getRecords($ID);
				$action = 'Update';
			}

			else {
				$action = 'Add';
			}

    }
		catch(Exception $e) {
 			errorHandle::errorMsg($e->getMessage());
    }

		// Set some localvars for use in the Form and HTML below.
		$localvars->set('action', $action);
		$localvars->set('submitText', $action . ' a Permission');

		$localvars->set('form', $reservationPermissions->setupForm($id));

		templates::display('header');
	?>

	<header>
		<h1>{local var="action"} a Permission</h1>
	</header>

  {local var="form"}

	<?php
	templates::display('footer');
	?>
