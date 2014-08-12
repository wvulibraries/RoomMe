<?php
require_once("engineHeader.php");

$snippet = new Snippet("pageContent","content");

$localvars->set("snippetID",$_GET['HTML']['id']);

templates::display('header');
?>

<!-- Page Content Goes Below This Line -->
	{snippet id="{local var="snippetID"}" field="content"}
<!-- Page Content Goes Above This Line -->

<?php
templates::display('footer');
?>