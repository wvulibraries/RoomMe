<?php
require_once("../../engineHeader.php");

recurseInsert("includes/formDefinitions/form_rooms.php","php");

templates::display('header');
?>

<header>
<h1>Rooms Management</h1>
</header>

<section>
{form name="rooms" display="form" addGet="true"}
</section>

<section>
{form name="rooms" display="edit" expandable="true" addGet="true"}
</section>


<?php
templates::display('footer');
?>