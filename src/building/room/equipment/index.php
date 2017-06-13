<?php
require_once("../../../engineHeader.php");

$errorMsg = "";
$error    = FALSE;

$buildingID = NULL;
$roomID     = NULL;

if (isset($_GET['MYSQL']['id'])) {

	$db  = db::get($localvars->get('dbConnectionName'));

	$sql = sprintf("SELECT equipement.*, equipementTypes.name as typeName FROM equipement LEFT JOIN equipementTypes ON equipement.type=equipementTypes.ID WHERE equipement.ID=?");
	$sqlResult = $db->query($sql,array($_GET['MYSQL']['id']));

	if ($sqlResult->error()) {
		errorHandle::newError($sqlResult->errorMsg(), errorHandle::DEBUG);
		errorHandle::errorMsg("Error retrieving Equipment information.");
	}
	else {
		$row = $sqlResult->fetch();
		$localvars->set("name",$row['name']);
		$localvars->set("type",$row['typeName']);
		$localvars->set("description",$row['description']);
		$localvars->set("url",$row['url']);
	}


}
else {
	errorHandle::errorMsg("Equipment ID missing or invalid.");
}

$localvars->set("prettyPrint",errorHandle::prettyPrint());

templates::display('header');
?>

<h3 class="roomH3" style="display: inline-block;">Equipment Information</h3>

<!-- Extra Links -->
<a class="policyLink roomTabletDesktop" href="{local var="advancedSearch"}">Advanced Search <span class="fa fa-cog"></span></a>
<a class="policyLink3 roomTabletDesktop" href="{local var="policiesPage"}">Reservation Policies
	<span class="fa fa-exclamation-circle"></span>
</a>
<a class="policyLink roomTabletDesktop" href="{local var="helpPage"}">Help
	<span class="fa fa-question-circle"></span>
</a>
<hr class="roomHR roomTabletDesktop" />


{local var="prettyPrint"}

<?php if (!isset($engine->errorStack['error'])) { ?>

<section id="equipmentListing">
	<h4>{local var="name"}</h4>
	<p><strong>Type: </strong> {local var="type"}</p>
	<p><strong>Description: </strong> {local var="description"}</p>
	<?php if (!is_empty($row['url'])) { ?>
	<strong>More Information: </strong> <a href="{local var="url"}">{local var="url"}</a></p>
	<?php } ?>


</section>

<?php } ?>

<!-- Advanced Search -->
<div style="clear:both;"></div>
<hr class="roomHR roomMobile" />
<a href="{local var="advancedSearch"}" id="asbutton" class="bSubmit roomMobile"><span class="fa fa-cog"></span> Advanced Search</a>

<div class="clear:both;"></div>
<br>

<!-- Rooms Navigation -->
<?php recurseInsert("includes/roomsByBuilding.php","php") ?>

<!-- Mobile UI -->
<a class="policyLink roomMobile" href="{local var="policiesPage"}">Reservation Policies <span class="fa fa-exclamation-circle"></span></a>

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

<?php
templates::display('footer');
?>
