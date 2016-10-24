	<?php

		require_once("../../../../engineHeader.php");

		// instantiate reservation class
		$reservationPermissions = new reservationPermissions;

    // verify and insert the contents the CSV File into the database
		$reservationPermissions->insertCSVFile();

		// Set some localvars for use in the Form and HTML below.
		$localvars->set('submitText', 'Upload a Permission file');

		templates::display('header');
	?>

	<header>
		<h1>Upload a Permissions File</h1>
	</header>

	<!-- {local var="uploadform"} -->

	<section>
		<?php include ("../../../../includes/formDefinitions/form_upload.php"); ?>
	</section>

	<script type="text/javascript" src="{local var="roomResBaseDir"}/javascript/rooms.js"></script>

	<?php
	templates::display('footer');
	?>
