<?php
if (isset($_GET['MYSQL']['snippetID'])) {
	$snippetID = $_GET['MYSQL']['snippetID'];

	$db  = db::get($localvars->get('dbConnectionName'));
	$sql = sprintf("SELECT * FROM `%s` WHERE ID=? LIMIT 1",
			"pageContent"
		);
	$sqlResult = $db->query($sql,array($snippetID));

	if ($sqlResult->rowCount() == 0) {
		errorHandle::errorMsg("Provided ID is invalid.");
	}
	else {
		$updateInsert   = TRUE;
		$updateInsertID = "ID";

		$snippetFields  = $sqlResult->fetch();
	}
}

$listObj = new listManagement("pageContent",db::get("appDB"));
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
