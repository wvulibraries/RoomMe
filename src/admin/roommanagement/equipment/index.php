<?php
require_once("../../engineHeader.php");

recurseInsert("includes/formDefinitions/form_equipment.php","php");

templates::display('header');
?>

<header>
<h1>Equipement Management</h1>
</header>

<section>
{form name="equipement" display="form" addGet="true"}
</section>

<section>
{form name="equipement" display="edit" expandable="true" addGet="true"}
</section>


<?php
templates::display('footer');
?>