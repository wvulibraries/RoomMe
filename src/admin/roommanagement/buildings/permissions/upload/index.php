	<?php

		require_once("../../../../engineHeader.php");

		$errorMsg = "";
		$error    = FALSE;
		$id       = NULL;
		$db       = db::get($localvars->get('dbConnectionName'));

		// instantiate reservation class
		$reservationPermissions = new reservationPermissions;

    // verify and insert the contents the CSV File into the database
		$reservationPermissions->insertCSVFile();

		// Set some localvars for use in the Form and HTML below.
		$localvars->set('submitText', 'Upload a Permission file');
		$localvars->set('uploadform', $reservationPermissions->uploadForm($id));

		templates::display('header');
	?>

	<header>
		<h1>Upload a Permissions File</h1>
	</header>

	{local var="uploadform"}

	<?php
	templates::display('footer');
	?>
