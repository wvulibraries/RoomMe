<?php
require_once("../../../engineHeader.php");

recurseInsert("includes/formDefinitions/form_equipmentTypes.php","php");

templates::display('header');
?>

<header>
<h1>Equipment Type Management</h1>
</header>

<section>
{form name="equipmentTypes" display="form" addGet="true"}
</section>

<section>
{form name="equipmentTypes" display="edit" expandable="true" addGet="true"}
</section>


<?php
templates::display('footer');
?>