<?php
require_once("engineHeader.php");

$snippet = new Snippet("pageContent","content");

$engine->localVars("snippetID",$engine->cleanGet['HTML']['id']);

$engine->eTemplate("include","header");
?>

<!-- Page Content Goes Below This Line -->
	{snippet id="{local var="snippetID"}" field="content"}
<!-- Page Content Goes Above This Line -->

<?php
$engine->eTemplate("include","footer");
?>