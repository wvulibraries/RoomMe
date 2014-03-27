<?php
require_once("../engineHeader.php");

$listObj = new listManagement("via");

$options = array();
$options['field'] = "name";
$options['label'] = "Via";
$listObj->addField($options);
unset($options);

$errorMsg = NULL;
if(isset($engine->cleanPost['MYSQL']['via_submit'])) {
	$errorMsg = $listObj->insert();
	$errorMsg = errorHandle::prettyPrint();
}
else if (isset($engine->cleanPost['MYSQL']['via_update'])) {
	$errorMsg = $listObj->update();
	$errorMsg = errorHandle::prettyPrint();
}

$engine->eTemplate("include","header");
?>

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
{listObject display="insertForm" addGet="true"}
</section>

<section>
<header><h1>Edit Vias</h1></header>
{listObject display="editTable" addGet="true"}
</section>


<?php
$engine->eTemplate("include","footer");
?>