<?php

require_once("../../../../engineHeader.php");
recurseInsert("../includes/functions.php","php");

$errorMsg = "";
$error    = FALSE;

$table           = new tableObject("array");
$table->sortable = TRUE;
$table->summary  = "Reserve Permissions";
$table->class    = "styledTable";

$permissions    = array();

$db        = db::get($localvars->get('dbConnectionName'));
$sql       = sprintf("SELECT * FROM reservePermissions ");
$sqlResult = $db->query($sql);

if ($sqlResult->error()) {
	$error     = TRUE;
	$errorMsg .= errorHandle::errorMsg("Error retrieving reserve premissions list.");
	errorHandle::newError($sqlResult->errorMsg(), errorHandle::DEBUG);
}

if ($error === FALSE) {

	$headers = array();
	$headers[] = "Resource ID";
	$headers[] = "Resource Type";
	$headers[] = "Username";
	$headers[] = "Edit";
	$headers[] = "Delete";
	$table->headers($headers);

	while($row       = $sqlResult->fetch()) {

		$temp = array();
		$temp['resourceID']  = $row['resourceID'];
		$temp['resourceType']  = $row['resourceType'];
		$temp['resourceType']  = $row['resourceType'];
		$temp['edit']      = sprintf('<a href="../create/?id=%s">Edit</a>',
			htmlSanitize($row['ID'])
			);
		$temp['delete']    = sprintf('<input type="checkbox" name="delete[]" value="%s" />',
			htmlSanitize($row['ID'])
			);
		$permissions[] = $temp;

	}
}

templates::display('header');
?>
<header>
<h1>Reserve Permissions</h1>
</header>

<form action="{phpself query="true"}" method="post" onsubmit="return confirm('Confirm Deletes');">
	{csrf}

	<input type="submit" name="multiDelete" value="Delete Selected Reserve Permissions" />
	<?php print $table->display($permissions); ?>
</form>



<?php
templates::display('footer');
?>
