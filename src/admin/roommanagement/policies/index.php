<?php
require_once("../../engineHeader.php");

recurseInsert("includes/formDefinitions/form_roomPolicies.php","php");

templates::display('header');
?>

<header>
<h1>Policies Management</h1>
</header>

<section>
{form name="roomPolicies" display="form" addGet="true"}
</section>

<section>
{form name="roomPolicies" display="edit" expandable="true" addGet="true"}
</section>


<?php
templates::display('footer');
?>