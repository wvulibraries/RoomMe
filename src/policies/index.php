<?php
require_once("../engineHeader.php");
recurseInsert("includes/functions.php","php");

$snippet = new Snippet("pageContent","content");

$localvars->set("errors",errorHandle::prettyPrint());

templates::display('header');
?>


<h3 class="roomH3" style="display: inline-block;">Policies</h3>

<!-- Extra Links -->
<a class="policyLink roomTabletDesktop" href="{local var="advancedSearch"}">Advanced Search <i class="fa fa-cog"></i></a>
<a class="policyLink3 roomTabletDesktop" href="{local var="policiesPage"}">Reservation Policies 
	<i class="fa fa-exclamation-circle"></i>
</a>

<hr class="roomHR roomTabletDesktop" />


{local var="errors"}

{snippet id="1" field="content"}

{snippet id="5" field="content"}

{snippet id="7" field="content"}

{snippet id="3" field="content"}

{snippet id="4" field="content"}

<br>
<br>

	<!-- Advanced Search -->
	<div style="clear:both;"></div>
	<hr class="roomHR roomMobile" />
	<a href="{local var="advancedSearch"}" id="asbutton" class="bSubmit roomMobile"><i class="fa fa-cog"></i> Advanced Search</a>

	<div class="clear:both;"></div>
	<br>

	<!-- Rooms Navigation -->
	<?php recurseInsert("includes/roomsByBuilding.php","php") ?>

<!-- Mobile UI -->			
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