<?php
// @TODO This file is a mess. It is in need of a refactoring/cleanup
//
require_once("../../../../engineHeader.php");



$errorMsg = "";
$error    = FALSE;

$db       = db::get($localvars->get('dbConnectionName'));

// we are editing a reservation
$reservationInfo = NULL;
$resourceID      = "";
$resourceType    = "";
$username        = "";
$submitError     = FALSE;

$reservationPermissions = new reservationPermissions;
//$reservation = new Reservation;

// Check to see if we want to create a new reservation with the patron information
// we want to make sure the patron information remains, but all the reservation information is removed ... as if it is a new reservation.
// if (isset($_POST['MYSQL']['createNewFromOld'])) {
//
// 	unset($_GET['MYSQL']['id']);
// 	unset($_POST['MYSQL']['reservationID']);
//
// }

// try {

	// Is this an Update?
	// Currently checking for this in both get and post.
	// if (isset($_GET['MYSQL']['id']) || (isset($_POST['MYSQL']['reservationID']) && !is_empty($_POST['MYSQL']['reservationID'])) ) {
	//
	// 	$reservationID = (isset($_POST['MYSQL']['reservationID']) && !is_empty($_POST['MYSQL']['reservationID']))?$_POST['MYSQL']['reservationID']:$_GET['MYSQL']['id'];
	//
	// 	if ($reservation->get($reservationID) === FALSE) {
	// 		throw new Exception("Error retrieving reservation.");
	// 	}
	//
	// }


// 	if (isset($_POST['MYSQL']['createSubmit'])) {
//
//
// 		$reservation->setBuilding($_POST['MYSQL']['library']);
// 		$reservation->setRoom($_POST['MYSQL']['room']);
//
// 		if (!$reservation->create()) {
// 			throw new Exception("Error Creating Reservation.");
// 		}
//
// 	}
// 	else if (isset($_POST['MYSQL']['deleteSubmit'])) {
//
// 		if (!$reservation->delete()) {
// 			throw new Exception("Error deleting reservation.");
// 		}
//
// 		// @TODO this should not be hard coded.
// 		header('Location: ../list/');
//
// 	}
//
// }
// catch(Exception $e) {
// 	errorHandle::errorMsg($e->getMessage());
//
// 	// Setup to repopulate form on error
// 	$submitError = TRUE;
// }

// Set some localvars for use in the HTML below.
// $localvars->set("resourceID",($reservation->isNew())?"":$reservation->reservation['resourceID']);
// $localvars->set("resourceType",($reservation->isNew())?"":$reservation->reservation['resourceType']);
// $localvars->set("username",($reservation->isNew())?"":$reservation->reservation['username']);


// Check to see if we want to create a new reservation with the patron information
// we want to make sure the patron information remains, but all the reservation information is removed ... as if it is a new reservation.
if (isset($_POST['MYSQL']['createNewFromOld'])) {

	$localvars->set("resourceID",(isset($_POST['HTML']['resourceID']) && !is_empty($_POST['HTML']['resourceID']))?$_POST['HTML']['resourceID']:"");
	$localvars->set("resourceType",(isset($_POST['HTML']['resourceType']) && !is_empty($_POST['HTML']['resourceType']))?$_POST['HTML']['resourceType']:"");
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

<?php //if (count($engine->errorStack) > 0) {	?>
<!-- <section id="actionResults"> -->
	<!-- <header>
		<h1>Results</h1>
	</header> -->
	<?php //print errorHandle::prettyPrint(); ?>
<!-- </section> -->
<?php //} ?>

	<form action="{phpself query="true"}" method="post">
		{csrf}

		<input type="hidden" name="ID" value="{local var="ID"}" />

		<fieldset>
			<legend>Permission Information</legend>
			<label for="resourceID" class="requiredField">Resource ID:</label> &nbsp; <input type="text" id="resourceID" name="resourceID" value="{local var="resourceID"}" required/>
			<br />
			<label for="resourceType" class="requiredField">Resource Type:</label> &nbsp; <input type="text" id="resourceType" name="resourceType" value="{local var="resourceType"}" required/>
			<br />
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
