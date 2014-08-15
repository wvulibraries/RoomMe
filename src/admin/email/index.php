<?php
require_once("engineHeader.php");

$errorMsg = "";
$error    = FALSE;

$db       = db::get($localvars->get('dbConnectionName'));

try {

	if (!isset($_GET['MYSQL']['id'])) {
		throw new Exception("No reservation ID provided.");
	}

	if ($reservation->get($_GET['MYSQL']['id']) === FALSE) {
		throw new Exception("Error retrieving reservation.");
	}

	if (!$reservation->hasEmail()) {
		throw new Exception("Email was not provided for this reservation.");
	}



}
catch (Exception $e) {
	errorHandle::errorMsg($e->getMessage());

	$error = TRUE;
}

templates::display('header');
?>

<?php if (count($engine->errorStack) > 0) {	?>
<section id="actionResults">
	<header>
		<h1>Results</h1>
	</header>
	<?php print errorHandle::prettyPrint(); ?>
</section>
<?php } ?>

<?php if (!$error) { ?>

<form action="" method="post">

<label for="subject">Email Subject: </label> <input type="text" name="subject" id="subject" />

<br />

<label for="email">Email Message: </label><br />
<textarea name="email" id="email"></textarea>
<br /><br />
<input type="submit" name="sendEmail" value="Send Email" />

</form>

<?php } ?>

<?php
templates::display('footer');
?>