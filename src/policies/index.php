<?php
require_once("../engineHeader.php");
recurseInsert("includes/functions.php","php");

$snippet = new Snippet("pageContent","content");

$localvars->set("errors",errorHandle::prettyPrint());

templates::display('header');
?>

<header>
<h3>Policies</h3>
</header>

{local var="errors"}

{snippet id="1" field="content"}

{snippet id="5" field="content"}

{snippet id="7" field="content"}

{snippet id="3" field="content"}

{snippet id="4" field="content"}

<br>
<br>

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