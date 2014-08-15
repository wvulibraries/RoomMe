<?php
require_once("../engineHeader.php");

$errorMsg = "";
$error    = FALSE;

$db       = db::get($localvars->get('dbConnectionName'));

$reservation = new Reservation;

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

<table class="table">
	<tr>
		<td>
			<strong>User Name</strong>
		</td>	
		<td>
			{local var="username"}
		</td>
	</tr>
		<tr>
		<td>
			<strong>Group Name</strong>
		</td>	
		<td>
			{local var="groupname"}
		</td>
	</tr>
		<tr>
		<td>
			<strong>Email</strong>
		</td>	
		<td>
			{local var="email"}
		</td>
	</tr>
</table>

<form action="{phpself query="true"}" method="post">
{csrf}
<input type="hidden" name="id" value="{local var="id"}"/>

<label for="subject"><strong>Email Subject:</strong></label> <input type="text" name="subject" id="subject" value="{local var="subject"}"style="width: 400px;" />

<br />

<label for="email"><strong>Email Message:</strong></label><br />
<textarea name="email" id="email" style="width: 500px; height: 300px;">{local var="emailMessage"}</textarea>
<br /><br />
<input type="submit" name="sendEmail" value="Send Email" />

</form>

<?php } ?>

<?php
templates::display('footer');
?>