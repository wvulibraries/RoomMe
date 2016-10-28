<?php

	require_once("../../../../engineHeader.php");

  // instantiate reservation class
  $reservationPermissions =  new reservationPermissions;

	if (isset($_POST['MYSQL']['multiDelete'])){
		$reservationPermissions->multiDelete($_POST['MYSQL']['delete']);
	}

	templates::display('header');
?>

<header>
	<h1>Reservation Restrictions</h1>
</header>

<section>
	<?php include ("../../../../includes/formDefinitions/form_dataTable.php"); ?>
</section>

<?php
templates::display('footer');
?>
