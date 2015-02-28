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

try {

	// We don't want to try submitting if there was an error from above, but we also don't want to set $error
	// this is horribly hacky. 
	if ($error) {
		throw new Exception("");
	}

	if (isset($_POST['HTML']['sendEmail'])) {

		// quick validation
		if (!isset($_POST['HTML']['subject']) || is_empty($_POST['HTML']['subject'])) {
			throw new Exception("Subject is required.");
		}
		if (!isset($_POST['HTML']['email']) || is_empty($_POST['HTML']['email'])) {
			throw new Exception("Email Message is required.");
		}

		$mail = new mailSender();
		$mail->addRecipient($reservation->reservation['email']);
		$mail->addSender($reservation->building['fromEmail'], "WVU Libraries");
		$mail->addSubject($_POST['HTML']['subject']);
		$mail->addBody($_POST['HTML']['email']);

		if (!$mail->sendEmail()) {
			throw new Exception("Error Sending Email. Due to the nature of this error, please contact your administrator");
		}

		errorHandle::successMsg("Email Sent Successfully");

	}

}
catch (Exception $e) {
	errorHandle::errorMsg($e->getMessage());
	$localvars->set("subject",$_POST['HTML']['subject']);
	$localvars->set("emailMessage",$_POST['HTML']['email']);
}


$localvars->set("id",$reservation->reservation['ID']);
$localvars->set("email",$reservation->reservation['email']);
$localvars->set("username",$reservation->reservation['username']);
$localvars->set("groupname",$reservation->reservation['groupname']);

templates::display('header');
?>

<header>
<h1>Email Patron</h1>
</header>

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
			<a href="mailto:{local var="email"}">{local var="email"}</a>
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