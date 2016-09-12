<?php
// @TODO This file is a mess. It is in need of a refactoring/cleanup
//
require_once("../../../engineHeader.php");

$errorMsg = "";
$error    = FALSE;

$db       = db::get($localvars->get('dbConnectionName'));

$resourceID      = "";
$resourceType    = "";
$username        = "";
$submitError     = FALSE;

// Set some localvars for use in the HTML below.
$localvars->set("resourceID",($reservation->isNew())?"":$reservation->reservation['resourceID']);
$localvars->set("resourceType",($reservation->isNew())?"":$reservation->reservation['resourceType']);
$localvars->set("username",($reservation->isNew())?"":$reservation->reservation['username']);

if (isset($_POST['MYSQL']['createNewFromOld'])) {
	$localvars->set("resourceID", (isset($_POST['HTML']['resourceID']) && !is_empty($_POST['HTML']['resourceID']))?$_POST['HTML']['resourceID']:"");
	$localvars->set("resourceType", (isset($_POST['HTML']['resourceType']) && !is_empty($_POST['HTML']['resourceType']))?$_POST['HTML']['resourceType']:"");
	$localvars->set("username", (isset($_POST['HTML']['username']) && !is_empty($_POST['HTML']['username']))?$_POST['HTML']['username']:"");
}

if ($submitError) {

	$localvars->set("resourceID",$_POST['HTML']['resourceID']);
	$localvars->set("resourceType",$_POST['HTML']['resourceType']);
	$localvars->set("username",$_POST['HTML']['username']);

}

templates::display('header');
?>

<header>
	<h1>{local var="action"} a Permission</h1>
</header>

<?php if (count($engine->errorStack) > 0) {	?>
<section id="actionResults">
	<header>
		<h1>Results</h1>
	</header>
	<?php print errorHandle::prettyPrint(); ?>
</section>
<?php } ?>

	<form action="{phpself query="true"}" method="post">
		{csrf}

		<input type="hidden" name="reservationID" value="{local var="reservationID"}" />

		<fieldset>
			<legend>User Information</legend>
			<label for="resourceID" class="requiredField">resourceID:</label> &nbsp; <input type="text" id="resourceID" name="resourceID" value="{local var="resourceID"}" required/>
			<br />
			<legend>User Information</legend>
			<label for="resourceType" class="requiredField">resourceType:</label> &nbsp; <input type="text" id="resourceType" name="resourceType" value="{local var="resourceType"}" required/>
			<br />
			<legend>User Information</legend>
			<label for="username" class="requiredField">Username:</label> &nbsp; <input type="text" id="username" name="username" value="{local var="username"}" required/>
			<br />
		</fieldset>
		<br /><br />
		<input type="submit" name="createSubmit" value="{local var="submitText"}"/> &nbsp;&nbsp;

		<?php if (!$reservation->isNew()) { ?>

		<input type="submit" name="deleteSubmit" value="Delete" id="deletePermissions"/>
		&nbsp;
		<input type="submit" name="createNewFromOld" value="Create Another" id="createNewFromOld" />

		<?php }	?>

	</form>

	<?php
	templates::display('footer');
	?>
