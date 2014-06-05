<?php
require_once("../engineHeader.php");

$listObj = new listManagement("rooms");

$options = array();
$options['field'] = "name";
$options['label'] = "Room Name";
$options['dupes'] = TRUE;
$listObj->addField($options);
unset($options);

$options = array();
$options['field'] = "number";
$options['label'] = "Room Number";
$options['dupes'] = TRUE;
$listObj->addField($options);
unset($options);

$options = array();
$options['field'] = "building";
$options['label'] = "Building";
$options['type']  = "select";
$options['dupes'] = TRUE;

$sql       = sprintf("SELECT * FROM building ORDER BY name");
$sqlResult = $engine->openDB->query($sql);

if ($sqlResult->error()) {
	errorHandle::newError(__METHOD__."() - ".$sqlResult['error'], errorHandle::DEBUG);
}

$temp                 = array();
$temp['value']        = "NULL";
$temp['label']        = "-- Select File Type --";
$options['options'][] = $temp;

while($row       = mysql_fetch_array($sqlResult['result'],  MYSQL_ASSOC)) {

	$selectValues          = array();
	$selectValues['value'] = $row['ID'];
	$selectValues['label'] = $row['name'];
	$options['options'][]  = $selectValues;

}
$listObj->addField($options);
unset($options);

$options = array();
$options['field'] = "roomTemplate";
$options['label'] = "Room Template";
$options['type']  = "select";
$options['dupes'] = TRUE;

$sql       = sprintf("SELECT * FROM roomTemplates ORDER BY name");
$sqlResult = $engine->openDB->query($sql);

if ($sqlResult->error()) {
	errorHandle::newError(__METHOD__."() - ".$sqlResult['error'], errorHandle::DEBUG);
}

$temp                 = array();
$temp['value']        = "NULL";
$temp['label']        = "-- Select File Type --";
$options['options'][] = $temp;

while($row       = mysql_fetch_array($sqlResult['result'],  MYSQL_ASSOC)) {

	$selectValues          = array();
	$selectValues['value'] = $row['ID'];
	$selectValues['label'] = $row['name'];
	$options['options'][]  = $selectValues;

}
$listObj->addField($options);


$errorMsg = NULL;
if(isset($_POST['MYSQL']['rooms_submit'])) {
	$errorMsg = $listObj->insert();
	$errorMsg = errorHandle::prettyPrint();
}
else if (isset($_POST['MYSQL']['rooms_update'])) {
	$errorMsg = $listObj->update();
	$errorMsg = errorHandle::prettyPrint();
}

$engine->eTemplate("include","header");
?>

<header>
<h1>Rooms Management</h1>
</header>

<?php
if (!isnull($errorMsg)) {
?>
<section id="actionResults">
	<header>
		<h1>Results</h1>
	</header>
	<?php print $errorMsg ?>
</section>
<?php } ?>


<section>
<header><h1>New Room</h1></header>
{listObject display="insertForm" addGet="true"}
</section>

<section>
<header><h1>Edit Rooms</h1></header>
{listObject display="editTable" addGet="true"}
</section>


<?php
$engine->eTemplate("include","footer");
?>