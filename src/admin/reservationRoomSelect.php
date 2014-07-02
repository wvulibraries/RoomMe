<?php
require_once("engineHeader.php");
recurseInsert("includes/functions.php","php");

$error = FALSE; 

if (!isset($_POST['MYSQL']['library']) && validate::integer($_POST['MYSQL']['library']) === FALSE) {
	$error = TRUE;
}

$localvars->set("libraryID",$_POST['MYSQL']['library']);

// Get the building name
if ($error === FALSE) {
	$return = getBuildingName($_POST['MYSQL']['library']);
	if ($return === FALSE) {
		$error = TRUE;
	}
	else {
		$localvars->set("buildingName",$return);
	}
}

// build the select list
if ($error === FALSE) {

	$return = buildRoomList($_POST['MYSQL']['library']);
	if ($return === FALSE) {
		$error = TRUE;
	}
	else {
		$localvars->set("roomSelectOptions",$return);
	}
}

$type   = "reservation";
$action = "reservationCreate.php";
if (isset($_GET['HTML']['type']) && $_GET['HTML']['type'] == "series") {
	$type = "series";
	$action = "seriesCreate.php";
}

$localvars->set("type",$type);
$localvars->set("action",$action);

templates::display('header');
?>

<header>
<h1>Reservation Creation -- Select Room</h1>
</header>

<a href="reservationLibrarySelect.php?type={local var="type"}">&lt;&lt; Select a different Building</a>
<br />

<form action="{local var="action"}" method="post">
	{csrf}

	<input type="hidden" name="library" value="{local var="libraryID"}" />

	<label for="room">Rooms available in {local var="buildingName"}:</label>
	<select name="room" id="room">
		{local var="roomSelectOptions"}
	</select>

	<br /><br />
	<input type="submit" name="roomSubmit" value="Select Room" />
</form>


<?php
templates::display('footer');

function buildRoomList($building) {

	$engine = EngineAPI::singleton();
	$db     = db::get($localvars->get('dbConnectionName'));

	$sql       = sprintf("SELECT roomListDisplay, roomSortOrder FROM building WHERE ID=?");
	$sqlResult = $db->query($sql,array($engine->openDB->escape($building)));

	if ($sqlResult->error()) {
		errorHandle::newError(__METHOD__."() - ".$sqlResult['error'], errorHandle::DEBUG);
		return(FALSE);
	}

	$buildingInfo = $sqlResult->fetch();

	$sql = sprintf("SELECT * FROM `rooms` WHERE `building`=? ORDER BY rooms.%s",
		$buildingInfo['roomSortOrder']
		);

	$sqlResult = $db->query($sql,$building);

	if ($sqlResult->error()) {
		errorHandle::newError(__METHOD__."() - ".$sqlResult['error'], errorHandle::DEBUG);
		return(FALSE);
	}

	$options = "";
	while ($row = $sqlResult->fetch()) {

		$displayName = str_replace("{name}", $row['name'], $buildingInfo['roomListDisplay']);
		$displayName = str_replace("{number}", $row['number'], $displayName);

		$options .= sprintf('<option value="%s">%s</option>',
			htmlSanitize($row['ID']),
			htmlSanitize($displayName)
			);
	}

	return($options);
}

?>