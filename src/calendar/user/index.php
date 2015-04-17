<?php
require_once("../../engineHeader.php");
recurseInsert("includes/functions.php","php");
$errorMsg = "";
$error    = FALSE;

$buildingID = NULL;
$roomID     = NULL;

// @TODO : need error checking on all of these db::get() method calls
$db = db::get($localvars->get('dbConnectionName'));

if (isset($_GET['MYSQL']['id'])) {

	$sql       = sprintf("SELECT username, startTime FROM reservations WHERE ID=?");
	$sqlResult = $db->query($sql,array($_GET['MYSQL']['id']));

	if ($sqlResult->error()) {
		errorHandle::newError($sqlResult->errorMsg(), errorHandle::DEBUG);
		errorHandle::errorMsg("Error canceling reservation.");
	}
	else {
		$row       = $sqlResult->fetch();

		if (strtolower($row['username']) == strtolower(session::get("username"))) {

			$timeAdjustment      = 60 * (getConfig('adjustedDeleteTime'));
			$currentAdjustedTime = time() + $timeAdjustment;

			if ($row['startTime'] > $currentAdjustedTime) {

				$sql       = sprintf("DELETE FROM reservations WHERE ID=?");
				$sqlResult = $db->query($sql,array($_GET['MYSQL']['id']));

				if ($sqlResult->error()) {
					errorHandle::newError($sqlResult->errorMsg(), errorHandle::DEBUG);
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

if (isset($_POST['MYSQL'])) {
	if (isset($_POST['MYSQL']['building'])) {
		$buildingID = $_POST['MYSQL']['building'];
	}
	if (isset($_POST['MYSQL']['room'])) {
		$roomID = $_POST['MYSQL']['room'];
	}
}


if (isset($_GET['MYSQL']['type']) && $_GET['MYSQL']['type']=="past") {
	$daysBack = getConfig('daysToDisplayOnCancelledPage');
	$daysBack = strtotime("-".$daysBack." day");
	$sql      = sprintf("SELECT reservations.*, building.name as buildingName, rooms.number as roomNumber, rooms.name as roomName, building.roomListDisplay FROM `reservations` LEFT JOIN `rooms` on reservations.roomID=rooms.ID LEFT JOIN `building` ON building.ID=rooms.building WHERE reservations.endTime<? AND reservations.endTime>? AND reservations.username=? ORDER BY building.name, rooms.name, reservations.startTime");
	$options  = array(time(), $daysBack, session::get("username"));
}
else {
	$sql     = sprintf("SELECT reservations.*, building.name as buildingName, rooms.number as roomNumber, rooms.name as roomName, building.roomListDisplay FROM `reservations` LEFT JOIN `rooms` on reservations.roomID=rooms.ID LEFT JOIN `building` ON building.ID=rooms.building WHERE reservations.endTime>? AND reservations.username=? ORDER BY building.name, rooms.name, reservations.startTime");
	$options = array(time(), session::get("username"));
}

$sqlResult = $db->query($sql,$options);

if ($sqlResult->error()) {
	$error     = TRUE;
	$errorMsg .= errorHandle::errorMsg("Error retrieving reservation list.");
	errorHandle::newError($sqlResult->errorMsg(), errorHandle::DEBUG);
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
	
	while($row       = $sqlResult->fetch()) {

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
		if (isset($_GET['MYSQL']['type']) && $_GET['MYSQL']['type']=="past") {
			$temp['edit'] = "";
		}
		else {
			$temp['edit']      = sprintf('<a href="%s?id=%s" class="cancelReservation">Cancel</a>',
				$_SERVER['PHP_SELF'],
				htmlSanitize($row['ID'])
				);
		}
		$reservations[] = $temp;

	}
}

$localvars->set("prettyPrint",errorHandle::prettyPrint());

templates::display('header');
?>

<h3 class="roomH3" style="display: inline-block;">Reservation Listing</h3>

<!-- Extra Links -->
<a class="policyLink roomTabletDesktop" href="{local var="advancedSearch"}">Advanced Search <i class="fa fa-cog"></i></a>
<a class="policyLink3 roomTabletDesktop" href="{local var="policiesPage"}">Reservation Policies 
	<i class="fa fa-exclamation-circle"></i>
</a>

<hr class="roomHR roomTabletDesktop" />

{local var="prettyPrint"}

<?php if (isset($_GET['MYSQL']['type']) && $_GET['MYSQL']['type']=="past") { ?>
<a href="?">Current Reservations</a>
<?php } else {?>
<a href="?type=past">Past Reservations</a>
<?php }?>
<br /><br />

<?php print $table->display($reservations); ?>

	<!-- Advanced Search -->
	<div style="clear:both;"></div>
	<hr class="roomHR roomMobile" />
	<a href="{local var="advancedSearch"}" id="asbutton" class="bSubmit roomMobile"><i class="fa fa-cog"></i> Advanced Search</a>

	<div class="clear:both;"></div>
	<br>

	<!-- Rooms Navigation -->
	<h4 style="float:left;">Rooms by Building:</h4>
	<hr class="roomHR"></hr>
	<nobr><a class="policyLink1" href="/services/rooms/building/?building=2"><i class="fa fa-building"></i>Downtown Campus Library</a></nobr>
	<nobr><a class="policyLink1" href="/services/rooms/building/?building=1"><i class="fa fa-building"></i>Evansdale Library</a></nobr>
	<nobr><a class="policyLink1" href="http://home.hsc.wvu.edu/its/forms/library-study-room-reservation/" target="_blank"><i class="fa fa-building"></i>Health Sciences Library</a></nobr>
	<hr class="roomHR"></hr>
	<br>

	<!-- Mobile UI -->			
	<a class="policyLink roomMobile" href="{local var="policiesPage"}">Reservation Policies <i class="fa fa-exclamation-circle"></i></a>

	<?php if (is_empty(session::get("username"))) { ?>
		<a id="userLoginSubmit" href="{local var="loginURL"}" class="roomMobile bSubmit">
			<i class="fa fa-user"></i> User Login
		</a>
	<?php } else { ?>
		<a id="userLoginSubmit" href="{local var="roomReservationHome"}/calendar/user/" class="roomMobile bSubmit">
			<i class="fa fa-check"></i> My Reservations
		</a>
		<a id="userLoginSubmit" href="{engine var="logoutPage"}?csrf={engine name="csrfGet"}" class="roomMobile bSubmit">
			<i class="fa fa-user"></i> User Logout
		</a>
	<?php } ?>

<?php
templates::display('footer');
?>