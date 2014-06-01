<?php
if (isset($_GET['MYSQL']['snippetID'])) {
	$snippetID = $_GET['MYSQL']['snippetID'];

	$sql = sprintf("SELECT * FROM `%s` WHERE ID='%s' LIMIT 1",
		"pageContent",
		$snippetID
		);
	$sqlResult = $engine->openDB->query($sql);

	if ($sqlResult['numRows'] == 0) {
		errorHandle::errorMsg("Provided ID is invalid.");
	}
	else {
		$updateInsert   = TRUE;
		$updateInsertID = "ID";

		$snippetFields  = mysql_fetch_array($sqlResult['result'], MYSQL_ASSOC);
	}
}

$listObj = new listManagement("pageContent");
$listObj->insertButtonText = "Save Snippet";
$listObj->updateButtonText = "Save Snippet";

if (isset($snippetFields)) {
	$listObj->updateInsert = $updateInsert;
	$listObj->updateInsertID = $updateInsertID;

	$options = array();
	$options['field'] = "ID";
	$options['label'] = "ID";
	$options['value'] = $snippetID;
	$options['readonly'] = TRUE;
	$options['type'] = "hidden";
	$listObj->addField($options);
	unset($options);
}

$options = array();
$options['field'] = "snippetName";
$options['label'] = "Snippet Name";
$options['size']  = "20";
if (isset($snippetFields)) {
	$options['value'] = $snippetFields['snippetName'];
}
$listObj->addField($options);
unset($options);

$options = array();
$options['field'] = "content";
$options['label'] = "Snippet Content";
$options['type']  = "wysiwyg";
$options['blank'] = TRUE;
$options['dupes'] = TRUE;
if (isset($snippetFields)) {
	$options['value'] = $snippetFields['content'];
}
$listObj->addField($options);
unset($options);
?>