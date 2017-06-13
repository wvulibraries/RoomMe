<?php
require_once("../engineHeader.php");
recurseInsert("includes/functions.php","php");

$snippet = new Snippet("pageContent","content");

$localvars->set("errors",errorHandle::prettyPrint());

templates::display('header');
?>


<h3 class="roomH3" style="display: inline-block;">Policies</h3>

<!-- Extra Links -->
<a class="policyLink roomTabletDesktop" href="{local var="advancedSearch"}">Advanced Search <span class="fa fa-cog"></span></a>
<a class="policyLink3 roomTabletDesktop" href="{local var="policiesPage"}">Reservation Policies
	<span class="fa fa-exclamation-circle"></span>
</a>
<a class="policyLink roomTabletDesktop" href="{local var="helpPage"}">Help
	<span class="fa fa-question-circle"></span>
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
	<a href="{local var="advancedSearch"}" id="asbutton" class="bSubmit roomMobile"><span class="fa fa-cog"></span> Advanced Search</a>

	<div class="clear:both;"></div>
	<br>

	<!-- Rooms Navigation -->
	<?php recurseInsert("includes/roomsByBuilding.php","php") ?>

<!-- Mobile UI -->
<?php if (is_empty(session::get("username"))) { ?>
	<a id="userLoginSubmit" href="{local var="loginURL"}" class="roomMobile bSubmit">
		<span class="fa fa-user"></span> User Login
	</a>
<?php } else { ?>
	<a id="userLoginSubmit" href="{local var="roomReservationHome"}/calendar/user/" class="roomMobile bSubmit">
		<span class="fa fa-check"></span> My Reservations
	</a>
	<a id="userLoginSubmit" href="{engine var="logoutPage"}" class="roomMobile bSubmit">
		<span class="fa fa-user"></span> User Logout
	</a>
<?php } ?>

<?php templates::display('footer'); ?>
