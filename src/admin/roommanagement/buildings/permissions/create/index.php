	<?php

		require_once("../../../../engineHeader.php");

		$reservationPermissions = new reservationPermissions;

		$id = isset($_GET['MYSQL']['id']) ? $_GET['MYSQL']['id'] : null;
		$action = $id !== null ? "Update" : "Insert";

		$localvars->set('form', $reservationPermissions->setupForm($id));
		$localvars->set('action', $action);

		templates::display('header');
	?>

	<header>
		<h1>{local var="action"} a Permission</h1>
	</header>

  {local var="form"}

	<?php
	templates::display('footer');
	?>
