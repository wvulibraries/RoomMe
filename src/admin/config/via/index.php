<?php
require_once("../../engineHeader.php");

recurseInsert("includes/formDefinitions/form_via.php","php");

templates::display('header');
?>

<header>
<h1>Via Management</h1>
</header>

<section>
{form name="Via" display="form"}
</section>

<section>
{form name="Via" display="editTable"}
</section>


<?php
templates::display('footer');
?>