<?php
require_once("../engineHeader.php");

$error          = FALSE;
$roomTemplateID = "";

$db             = db::get($localvars->get('dbConnectionName'));

if (!isset($_GET['MYSQL']['roomTemplate'])) {
	$error = TRUE;
	errorHandle::errorMsg("Invalid or missing roomTemplate ID");
}
else {
	$roomTemplateID = $_GET['MYSQL']['roomTemplate'];
}

// Form Submission
if (is_empty($engine->errorStack) && isset($_POST['MYSQL']['addEQtoTemplate_submit'])) {

	$engine->openDB->transBegin();

	$sql       = sprintf("DELETE FROM `roomTypeEquipment` WHERE roomTemplateID=?");
	$sqlResult = $db->query($sql,array($roomTemplateID));

	if ($sqlResult->error()) {
		errorHandle::errorMsg("Error removing Equipment");
	}

	if (isset($_POST['MYSQL']['selectedEQ'])) {
		foreach ($_POST['MYSQL']['selectedEQ'] as $selectedGroupID) {
			$sql       = sprintf("INSERT INTO `roomTypeEquipment` (roomTemplateID,equipmentID) VALUES (?,?)");
			$sqlResult = $db->query($sql,array($roomTemplateID,$selectedGroupID));

			if ($sqlResult->error()) {
				errorHandle::errorMsg("Error adding Group");
				break;
			}
		}
	}

	if (isset($engine->errorStack['error'])) {
		$engine->openDB->transRollback();
	}
	else {
		$engine->openDB->transCommit();
		errorHandle::successMsg("Database successfully updated.");
	}
	$engine->openDB->transEnd();
}
// Form Submission

if (!isset($engine->errorStack['error'])) {

	// selected options
	$sql       = sprintf("SELECT equipmentID, equipement.name as name FROM roomTypeEquipment LEFT JOIN equipement ON equipement.ID=roomTypeEquipment.equipmentID WHERE roomTemplateID=? ORDER BY name");
	$sqlResult = $db->query($sql,array($roomTemplateID));

	$selectedEQ      = array();
	if (!$sqlResult->error()) {
		$selectedOptions = "";

		while($row = $sqlResult->fetch()) {
			$selectedEQ[$row['equipmentID']] = $row['name'];
			$selectedOptions .= sprintf('<option value="%s">%s</option>',
				htmlSanitize($row['equipmentID']),
				htmlSanitize($row['name'])
				);
		}
		$localvars->set("selectedOptions",$selectedOptions);
	}
	else {
		errorHandle::newError($sqlResult->errorMsg(), errorHandle::DEBUG);
	}

	// all options
	// @TODO this needs double testing/checking
	$sql       = sprintf("SELECT ID, name FROM equipement WHERE ID NOT IN (?) ORDER BY name");
	$sqlResult = $db->query($sql,array(implode("','",array_keys($selectedEQ))));

	if (!$sqlResult->error()) {
		$allOptions = "";
		while($row = $sqlResult->fetch()) {
			$allOptions .= sprintf('<option value="%s">%s</option>',
				htmlSanitize($row['ID']),
				htmlSanitize($row['name'])
				);
		}
		$localvars->set("allOptions",$allOptions);
	}
	else {
		errorHandle::newError($sqlResult->errorMsg(), errorHandle::DEBUG);
	}

}

$localvars->set("prettyPrint",errorHandle::prettyPrint());

templates::display('header');
?>

<header>
<h1>Add Equipment to Room Template</h1>
</header>

{local var="prettyPrint"}

		<form action="{phpself query="true"}" method="post">
			<table>
				<tr>
					<td>
						<strong>All Equipment</strong><br />
						<select multiple name="allEQ[]" id="allEQ" size="20">
							{local var="allOptions"}
						</select>
					</td>
					<td style="text-align:center;vertical-align:middle">
						<input type="button" id="add" value="&gt;" /><br />
						<input type="button" id="addAll" value="&gt;&gt;" /><br />
						<input type="button" id="removeAll" value="&lt;&lt;" /><br />
						<input type="button" id="remove" value="&lt;" /><br />
					</td>
					<td>
						<strong>Selected Equipment</strong><br />
						<select multiple name="selectedEQ[]" id="selectedEQ" size="20">
							{local var="selectedOptions"}
						</select>
					</td>
				</tr>
			</table>

			{engine name="csrf"}
			<input type="submit" name="addEQtoTemplate_submit" id="addEQtoTemplate_submit" value="Submit" />
		</form>

			<script type="text/javascript">
		buttonClickHandlers('allEQ', 'selectedEQ');
		setWidth('select', 'form');
	</script>


<?php
templates::display('footer');
?>