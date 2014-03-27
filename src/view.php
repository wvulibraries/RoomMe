<?php
require_once("engineHeader.php");
recurseInsert("includes/functions.php","php");
$errorMsg = "";
$error    = FALSE;

$buildingID = NULL;
$roomID     = NULL;

if (isset($engine->cleanGet['MYSQL']['id'])) {

	$sql       = sprintf("SELECT username, startTime FROM reservations WHERE ID='%s'",
		$engine->cleanGet['MYSQL']['id']);
	$sqlResult = $engine->openDB->query($sql);

	if (!$sqlResult['result']) {
		errorHandle::newError(__METHOD__."() - ".$sqlResult['error'], errorHandle::DEBUG);
		errorHandle::errorMsg("Error canceling reservation.");
	}
	else {
		$row       = mysql_fetch_array($sqlResult['result'],  MYSQL_ASSOC);

		if (lc($row['username']) == lc(sessionGet("username"))) {

			$timeAdjustment      = 60 * (getConfig('adjustedDeleteTime'));
			$currentAdjustedTime = time() + $timeAdjustment;

			if ($row['startTime'] > $currentAdjustedTime) {

				$sql       = sprintf("DELETE FROM reservations WHERE ID='%s'",
					$engine->cleanGet['MYSQL']['id']);
				$sqlResult = $engine->openDB->query($sql);

				if (!$sqlResult['result']) {
					errorHandle::newError(__METHOD__."() - ".$sqlResult['error'], errorHandle::DEBUG);
					errorHandle::errorMsg("Error canceling reservation.");
				}
				else {
					errorHandle::successMsg("Reservation Deleted.");
				}
			}
			else {
				errorHandle::errorMsg("Cannot delete a reservation after it has started.");
			}
		}
		else {
			errorHandle::errorMsg("Username Mismatch. This can occur if your MyID username has changed.");
		}
	}

}

$table           = new tableObject("array");
$table->sortable = TRUE;
$table->summary  = "Room reservation listings";
$table->class    = "styledTable";

$reservations    = array();

if (isset($engine->cleanPost['MYSQL'])) {
	if (isset($engine->cleanPost['MYSQL']['building'])) {
		$buildingID = $engine->cleanPost['MYSQL']['building'];
	}
	if (isset($engine->cleanPost['MYSQL']['room'])) {
		$roomID = $engine->cleanPost['MYSQL']['room'];
	}
}


if (isset($engine->cleanGet['MYSQL']['type']) && $engine->cleanGet['MYSQL']['type']=="past") {
	$daysBack = getConfig('daysToDisplayOnCancelledPage');
	$daysBack = strtotime("-".$daysBack." day");
	$sql = sprintf("SELECT reservations.*, building.name as buildingName, rooms.number as roomNumber, rooms.name as roomName, building.roomListDisplay FROM `reservations` LEFT JOIN `rooms` on reservations.roomID=rooms.ID LEFT JOIN `building` ON building.ID=rooms.building WHERE reservations.endTime<'%s' AND reservations.endTime>'%s' AND reservations.username='%s' ORDER BY building.name, rooms.name, reservations.startTime",
		time(),
		$daysBack,
		sessionGet("username")
		);
}
else {
	$sql       = sprintf("SELECT reservations.*, building.name as buildingName, rooms.number as roomNumber, rooms.name as roomName, building.roomListDisplay FROM `reservations` LEFT JOIN `rooms` on reservations.roomID=rooms.ID LEFT JOIN `building` ON building.ID=rooms.building WHERE reservations.endTime>'%s' AND reservations.username='%s' ORDER BY building.name, rooms.name, reservations.startTime",
		time(),
		sessionGet("username")
		);
}

$sqlResult = $engine->openDB->query($sql);

if (!$sqlResult['result']) {
	$error     = TRUE;
	$errorMsg .= errorHandle::errorMsg("Error retrieving reservation list.");
	errorHandle::newError(__METHOD__."() - ".$sqlResult['error'], errorHandle::DEBUG);
}

if ($error === FALSE) {

	$hoursOnTable = getConfig("hoursOnReservationTable");

	$headers = array();
	$headers[] = "Building";
	$headers[] = "Room";
	$headers[] = "Start Time";
	$headers[] = "End Time";
	if ($hoursOnTable == "1") {
		$headers[] = "Hours";
	}
	$headers[] = "Cancel";
	$table->headers($headers);

	$hourSetting = getConfig('24hour');
	if ($hourSetting == "1") {
		$timeFormat = "m/d/Y H:i";
	}
	else {
		$timeFormat = "m/d/Y g:iA";
	}
	
	while($row       = mysql_fetch_array($sqlResult['result'],  MYSQL_ASSOC)) {

		$row['displayName'] = str_replace("{name}", $row['roomName'], $row['roomListDisplay']);
		$row['displayName'] = str_replace("{number}", $row['roomNumber'], $row['displayName']);

		$temp = array();
		$temp['building']  = $row['buildingName'];
		$temp['room']      = $row['displayName']; //$row['roomName'];
		$temp['startTime'] = date($timeFormat,$row['startTime']);
		$temp['endTime']   = date($timeFormat,$row['endTime']);
		if ($hoursOnTable == "1") {
			$temp['hoursOnReservationTable'] = ($row['endTime'] - $row['startTime'])/60/60;
		}
		if (isset($engine->cleanGet['MYSQL']['type']) && $engine->cleanGet['MYSQL']['type']=="past") {
			$temp['edit'] = "";
		}
		else {
			$temp['edit']      = sprintf('<a href="%s?id=%s" class="cancelReservation">Cancel</a>',
				$_SERVER['PHP_SELF'],
				$engine->openDB->escape($row['ID'])
				);
		}
		$reservations[] = $temp;

	}
}

localvars::add("prettyPrint",errorHandle::prettyPrint());

$engine->eTemplate("include","header");
?>

<header>
<h1>Reservation Listing</h1>
</header>

{local var="prettyPrint"}

<?php if (isset($engine->cleanGet['MYSQL']['type']) && $engine->cleanGet['MYSQL']['type']=="past") { ?>
<a href="view.php">Current Reservations</a>
<?php } else {?>
<a href="view.php?type=past">Past Reservations</a>
<?php }?>
<br /><br />

<?php print $table->display($reservations); ?>


<?php
$engine->eTemplate("include","footer");
?>