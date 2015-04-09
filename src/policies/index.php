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

<?php templates::display('footer'); ?>