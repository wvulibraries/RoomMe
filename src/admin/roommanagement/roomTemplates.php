<?php
require_once("../engineHeader.php");

$listObj = new listManagement("roomTemplates");

$options = array();
$options['field'] = "ID";
$options['label'] = "ID";
$options['disabled'] = TRUE;
$options['type']  = "hidden";
$listObj->addField($options);
unset($options);

$options = array();
$options['field'] = "name";
$options['label'] = "Template Name";
$listObj->addField($options);
unset($options);

$options = array();
$options['field'] = "description";
$options['label'] = "Description";
$options['type']  = "textarea";
$options['dupes'] = TRUE;
$listObj->addField($options);
unset($options);

$options = array();
$options['field'] = "fromEmail";
$options['label'] = "From Email";
$options['dupes'] = TRUE;
$options['blank'] = TRUE;
$listObj->addField($options);
unset($options);

$options = array();
$options['field'] = "url";
$options['label'] = "URL";
$options['dupes'] = TRUE;
$options['blank'] = TRUE;
$options['validate'] = "url";
$listObj->addField($options);
unset($options);

$options = array();
$options['field'] = "mapURL";
$options['label'] = "Map URL";
$options['dupes'] = TRUE;
$options['blank'] = TRUE;
$options['validate'] = "url";
$listObj->addField($options);
unset($options);

$options = array();
$options['field'] = "policy";
$options['label'] = "Policy";
$options['dupes'] = TRUE;
$options['type']  = "select";

$options['options'] = array();
$temp = array();
$temp['value'] = 'NULL';
$temp['label'] = "-- Select Policy --";
$options['options'][] = $temp;
unset($temp);

$sql       = sprintf("SELECT ID, name FROM `policies`");
$sqlResult = $engine->openDB->query($sql);

while ($row       = $sqlResult->fetch()) {
	$temp = array();
	$temp['value'] = $row['ID'];
	$temp['label'] = $row['name'];
	$options['options'][] = $temp;
	unset($temp);
}

$listObj->addField($options);
unset($options);

$options = array();
$options['field'] = '<a href="addEQtoRoom.php?roomTemplate={ID}">Edit</a>';
$options['label'] = "Manage Equipement";
$options['type'] = "plainText";
$listObj->addField($options);
unset($options);

$errorMsg = NULL;
if(isset($_POST['MYSQL']['roomTemplates_submit'])) {
	$errorMsg = $listObj->insert();
	$errorMsg = errorHandle::prettyPrint();
}
else if (isset($_POST['MYSQL']['roomTemplates_update'])) {
	$errorMsg = $listObj->update();
	$errorMsg = errorHandle::prettyPrint();
}

$engine->eTemplate("include","header");
?>

<header>
<h1>Room Templates Management</h1>
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
<header><h1>New Room Templates</h1></header>
{listObject display="insertForm" addGet="true"}
</section>

<section>
<header><h1>Edit Room Templates</h1></header>
{listObject display="editTable" addGet="true"}
</section>


<?php
$engine->eTemplate("include","footer");
?>