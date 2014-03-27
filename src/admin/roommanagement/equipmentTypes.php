<?php
require_once("../engineHeader.php");

$listObj = new listManagement("equipementTypes");

$options = array();
$options['field'] = "name";
$options['label'] = "Equipment Type";
$listObj->addField($options);
unset($options);

$errorMsg = NULL;
if(isset($engine->cleanPost['MYSQL']['equipementTypes_submit'])) {
	$errorMsg = $listObj->insert();
	$errorMsg = errorHandle::prettyPrint();
}
else if (isset($engine->cleanPost['MYSQL']['equipementTypes_update'])) {
	$errorMsg = $listObj->update();
	$errorMsg = errorHandle::prettyPrint();
}

$engine->eTemplate("include","header");
?>

<header>
<h1>Equipment Type Management</h1>
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
<header><h1>New Equipement Type</h1></header>
{listObject display="insertForm" addGet="true"}
</section>

<section>
<header><h1>Edit Equipement Types</h1></header>
{listObject display="editTable" addGet="true"}
</section>


<?php
$engine->eTemplate("include","footer");
?>