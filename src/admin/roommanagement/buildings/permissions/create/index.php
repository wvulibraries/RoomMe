	<?php

		require_once("../../../../engineHeader.php");

		$reservationPermissions = new reservationPermissions;

		$id = isset($_GET['MYSQL']['id']) ? $_GET['MYSQL']['id'] : null;
		$building = isset($_GET['MYSQL']['building']) ? $_GET['MYSQL']['building'] : null;
		$action = $id !== null ? "Update" : "Insert";

		$localvars->set('form', $reservationPermissions->setupForm($id, $building));
		$localvars->set('action', $action);

		templates::display('header');
	?>

	<header>
		<h1>{local var="action"} a Permission</h1>
	</header>

  {local var="form"}

	<script type="text/javascript" src="{local var="roomResBaseDir"}/javascript/rooms.js"></script>

	<?php
	templates::display('footer');
	?>
