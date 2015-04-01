<?php
require_once("engineHeader.php");
recurseInsert("includes/functions.php","php");

// $localVars = localVars::getInstance();
$db        = db::get($localvars->get('dbConnectionName'));

$sql       = sprintf("SELECT * FROM building ORDER BY name");
$sqlResult = $db->query($sql);

$localvars->set("policyLabel",$messages->get("policyLabel"));

templates::display('header'); ?>

	<!-- Room Reservation Header -->
	<div class="roomReservation">
	    <div class="wrap">
	        <h2>Room Reservations</h2>
        		<?php if (is_empty(session::get("username"))) { ?>
        			<a class="userLogin roomTabletDesktop" href="{local var="loginURL"}">
        				<i class="fa fa-user"></i>
        				User Login
        			</a>
				<?php } else { ?>
					<a class="userLogin roomTabletDesktop" href="{engine var="logoutPage"}?csrf={engine name="csrfGet"}">
						<i class="fa fa-user"></i>User Logout
					</a>				
					<a class="userLogin roomTabletDesktop" href="{local var="roomReservationHome"}/calendar/user/" class="roomMobile bSubmit">
						<i class="fa fa-check"></i>My Reservations
					</a>
				<?php } ?>
	    </div>            
	</div>

	<!-- CMS Backpage Wrapper -->
	<div class="wrap hpcard">
	    <section class="bp-body-1c">

		<?php include 'includes/reservations.php';?>

		<!-- Calendar Call -->

		<!-- Mobile UI -->
		<a class="policyLink roomMobile" href="#">Reservation Policies <i class="fa fa-exclamation-circle"></i></a>

		<?php if (is_empty(session::get("username"))) { ?>
			<a id="userLoginSubmit" href="{local var="loginURL"}" class="roomMobile bSubmit">
				<i class="fa fa-user"></i> User Login
			</a>
		<?php } else { ?>
			<a id="userLoginSubmit" href="{local var="roomReservationHome"}/calendar/user/" class="roomMobile bSubmit">
				<i class="fa fa-check"></i> My Reservations
			</a>
			<a id="userLoginSubmit" href="{engine var="logoutPage"}?csrf={engine name="csrfGet"}" class="roomMobile bSubmit">
				<i class="fa fa-user"></i> User Logout
			</a>
		<?php } ?>

<?php templates::display('footer'); ?>