<?php
require_once("engineHeader.php");

$errorMsg = "";
$error    = FALSE;

$buildingID = NULL;
$roomID     = NULL;

if (isset($_GET['MYSQL']['id'])) {
	$sql       = sprintf("SELECT equipement.*, equipementTypes.name as typeName FROM equipement LEFT JOIN equipementTypes ON equipement.type=equipementTypes.ID WHERE equipement.ID='%s'",
		$_GET['MYSQL']['id']
		);
	$sqlResult = $engine->openDB->query($sql);

	if (!$sqlResult['result']) {
		errorHandle::newError(__METHOD__."() - ".$sqlResult['error'], errorHandle::DEBUG);
		errorHandle::errorMsg("Error retrieving Equipment information.");
	}
	else {
		$row = mysql_fetch_array($sqlResult['result'],  MYSQL_ASSOC);
		$localvars->set("name",$row['name']);
		$localvars->set("type",$row['typeName']);
		$localvars->set("description",$row['description']);
		$localvars->set("url",$row['url']);
	}

	
}
else {
	errorHandle::errorMsg("Equipment ID missing or invalid.");
}

$localvars->set("prettyPrint",errorHandle::prettyPrint());

$engine->eTemplate("include","header");
?>

<header>
<h1>Equipment Information</h1>
</header>

{local var="prettyPrint"}

<?php if (!isset($engine->errorStack['error'])) { ?>

<section id="equipmentListing">
	<header>
		<h1>{local var="name"}</h1>
	</header>

	<p><strong>Type: </strong> {local var="type"}</p>
	<p><strong>Description: </strong> {local var="description"}</p>
	<?php if (!isempty($row['url'])) { ?>
	<strong>More Information: </strong> <a href="{local var="url"}">{local var="url"}</a></p>
	<?php } ?>


</section>

<?php } ?>

<?php
$engine->eTemplate("include","footer");
?>