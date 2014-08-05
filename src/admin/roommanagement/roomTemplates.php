<?php
require_once("../engineHeader.php");

recurseInsert("includes/formDefinitions/form_roomTemplates.php","php");

templates::display('header');
?>

<header>
<h1>Room Templates Management</h1>
</header>

<section>
{form name="roomTemplates" display="form" addGet="true"}
</section>

<section>
{form name="roomTemplates" display="edit" expandable="true" addGet="true"}
</section>


<?php
templates::display('footer');
?>