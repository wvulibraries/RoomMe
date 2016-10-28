	<?php

		require_once("../../../../engineHeader.php");

		//$localvars = localvars::getInstance();

		$id = isset($_GET['MYSQL']['id']) ? $_GET['MYSQL']['id'] : null;
		$building = isset($_GET['MYSQL']['building']) ? $_GET['MYSQL']['building'] : null;
		$type = isset($_GET['MYSQL']['type']) ? $_GET['MYSQL']['type'] : 3;
		$action = $id !== null ? "Update" : "Insert";

		//$localvars->set('form', $reservationPermissions->setupForm($id, $building));
		$localvars->set('action', $action);
		$localvars->set('building', $building);
		$localvars->set('id', $id);
		//$localvars->set('type', $type);

		//$reservationPermissions = new reservationPermissions;
		recurseInsert("includes/formDefinitions/form_permissions.php","php");

		templates::display('header');
	?>

	<header>
		<h1>{local var="action"} a Restriction</h1>
	</header>

	<section>
	{form name="createPermissions" display="form" addGet="true"}
	</section>

	<!-- <section>
	{form name="createPermissions" display="edit" expandable="true" addGet="true"}
	</section> -->

  <!-- {local var="form"} -->

	<script type="text/javascript" src="{local var="roomResBaseDir"}/javascript/rooms.js"></script>

	<?php
	templates::display('footer');
	?>
