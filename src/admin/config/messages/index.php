<?php
require_once("../../engineHeader.php");
recurseInsert("includes/functions.php","php");

$messages = new messages;


if(isset($_POST['MYSQL']['sysconfig_submit'])) {

	$success = $messages->setMessages();

	if ($success) errorHandle::successMsg("All fields updated successfully");

}

$localvars->set("prettyPrint",errorHandle::prettyPrint());
$localvars->set("messagesEditTable",$messages->buildEditTable());

templates::display('header');
?>

<header>
<h1>Result Message Configuration</h1>
</header>

{local var="prettyPrint"}

<form action="" method="post">

	{csrf}

	{local var="messagesEditTable"}

	<input type="submit" name="sysconfig_submit" value="submit" />
</form>


<?php
templates::display('footer');
?>