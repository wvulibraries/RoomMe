<?php

global $updateTable;
global $listObj;
if (isset($listObj)) {
	unset($listObj);
}

$listObj = new listManagement("emailMessages");

if (isset($_GET['MYSQL']['ID']) && $updateTable === FALSE) {
	$listObj->updateInsert   = TRUE;
	$listObj->updateInsertID = "ID";
	
	$db         = db::get($localvars->get('dbConnectionName'));
	$sql        = sprintf("SELECT * FROM emailMessages WHERE ID=?");
	
	$sqlResult  = $db->query($sql,array($_GET['MYSQL']['ID']));
	$emailItems = $sqlResult->fetch();

	$options = array();
	$options['field'] = "ID";
	$options['label'] = "ID";
	$options['value'] = $_GET['MYSQL']['ID'];
	$options['readonly'] = TRUE;
	$options['type'] = "hidden";
	$listObj->addField($options);
	unset($options);
}
else if ($updateTable === TRUE) {
	$options = array();
	$options['field'] = "ID";
	$options['label'] = "ID";
	$options['readonly'] = TRUE;
	$options['type'] = "hidden";
	$listObj->addField($options);
	unset($options);
}

$options = array();
$options['field'] = "name";
$options['label'] = "Email Name";
if (!isnull($emailItems)) {
	$options['value'] = $emailItems['name'];
}
$listObj->addField($options);
unset($options);

$options = array();
$options['field'] = "subject";
$options['label'] = "Email Subject";
$options['dupes'] = TRUE;
if (!isnull($emailItems)) {
	$options['value'] = $emailItems['subject'];
}
$listObj->addField($options);
unset($options);

$options = array();
$options['field'] = "message";
$options['label'] = "Email Body";
$options['type']  = "textarea";
$options['dupes'] = TRUE;
if (!isnull($emailItems)) {
	$options['value'] = $emailItems['message'];
}
$listObj->addField($options);
unset($options);

$options = array();
$options['field'] = '<a href="'.$_SERVER['PHP_SELF'].'?ID={ID}">Edit</a>';
$options['label'] = "Edit";
$options['type']  = "plainText";
$listObj->addField($options);
unset($options);

?>