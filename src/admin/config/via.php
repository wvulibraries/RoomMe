<?php
require_once("../engineHeader.php");

recurseInsert("includes/formDefinitions/form_via.php","php");

templates::display('header');
?>

{form name="via" display="assets"}

<header>
<h1>Via Management</h1>
</header>

<?php
if (!isnull($errorMsg)) {
?>
<section id="actionResults">
	<header>
		<h1>Results</h1>
	</header>
	<?php print $errorMsg; ?>
</section>
<?php } ?>


<section>
<header><h1>New Via</h1></header>
{form name="via" display="form"}
</section>

<section>
<header><h1>Edit Vias</h1></header>
{form name="via" display="editTable" expandable="true"}
</section>


<?php
templates::display('footer');
?>