<?php
require_once("../engineHeader.php");

recurseInsert("includes/formDefinitions/form_buildings.php","php");

templates::display('header');
?>

<header>
<h1>Building Management</h1>
</header>

<section>
{form name="buildings" display="form" addGet="true"}
</section>

<section>
{form name="buildings" display="edit" expandable="true" addGet="true"}
</section>


<?php
templates::display('footer');
?>