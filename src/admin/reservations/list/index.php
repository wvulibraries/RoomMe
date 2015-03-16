<?php
require_once("../../engineHeader.php");

$errorMsg = "";
$error    = FALSE;

$currentMonth = date("n");
$currentDay   = date("j");
$currentYear  = date("Y");

$buildingID = NULL;
$roomID     = NULL;

$table           = new tableObject("array");
$table->sortable = TRUE;
$table->summary  = "Room reservation listings";
$table->class    = "styledTable";

$reservations    = array();

$db              = db::get($localvars->get('dbConnectionName'));

try {

	if (isset($_POST['MYSQL']['multiDelete'])) {

		$db->beginTransaction();

		foreach ($_POST['MYSQL']['delete'] as $reservationID) {
			$reservation = new reservation;
			$reservation->get($reservationID);

			if (!$reservation->delete()) {
				$db->rollback();
				throw new Exception("Error deleting reservations.");
			}
		}

		$db->commit();
	}

}
catch (Exception $e) {
	errorHandle::errorMsg($e->getMessage());
}

// Building the building dropdown list
$building = new building;
$localvars->set("buildingSelectOptions",$building->selectBuildingListOptions(TRUE));

if (isset($_POST['MYSQL'])) {
	if (isset($_POST['MYSQL']['building']) && $_POST['MYSQL']['building'] != "any") {
		$buildingID = $_POST['MYSQL']['building'];
	}
	if (isset($_POST['MYSQL']['room']) && $_POST['MYSQL']['room']!= "any") {
		$roomID = $_POST['MYSQL']['room'];
	}
}

$time = NULL;
if (isset($_POST['MYSQL']['allFutureDate']) && $_POST['MYSQL']['allFutureDate'] == 1) {
	$time = NULL;
}
else if (isset($_POST['MYSQL']['submitListDate'])) {
	$time     = mktime(0,0,0,$_POST['MYSQL']['start_month'],$_POST['MYSQL']['start_day'],$_POST['MYSQL']['start_year']);
	$time_end = mktime(23,59,0,$_POST['MYSQL']['start_month'],$_POST['MYSQL']['start_day'],$_POST['MYSQL']['start_year']);
}

$sql       = sprintf("SELECT reservations.*, building.name as buildingName, building.roomListDisplay as roomListDisplay, rooms.name as roomName, rooms.number as roomNumber FROM `reservations` LEFT JOIN `rooms` on reservations.roomID=rooms.ID LEFT JOIN `building` ON building.ID=rooms.building WHERE %s %s %s ORDER BY building.name, rooms.name, reservations.username, reservations.startTime ",
	(isnull($time))?"reservations.endTime>'".time()."'":"reservations.startTime>='".$time."' AND reservations.startTime<='".$time_end."'",
	(!isnull($buildingID))?"AND building.ID=".$buildingID:"",
	(!isnull($roomID))?"AND rooms.ID=".$roomID:""
	);
$sqlResult = $db->query($sql);


if ($sqlResult->error()) {
	$error     = TRUE;
	$errorMsg .= errorHandle::errorMsg("Error retrieving reservation list.");
	errorHandle::newError($sqlResult->errorMsg(), errorHandle::DEBUG);
}

if ($error === FALSE) {

	$hoursOnTable = getConfig("hoursOnReservationTable");

	$headers = array();
	$headers[] = "Username";
	$headers[] = "Building";
	$headers[] = "Room";
	$headers[] = "Start Time";
	$headers[] = "End Time";
	if ($hoursOnTable == "1") {
		$headers[] = "Hours";
	}
	$headers[] = "Edit";
	$headers[] = "Delete";
	$headers[] = "Created By";
	$table->headers($headers);

	$timeFormat = getTimeFormat();
	
	while($row       = $sqlResult->fetch()) {

		$displayName = $row['username'];
		if (isset($row['groupname']) && !is_empty($row['groupname'])) {
			$displayName .= " (".$row['groupname'].")";
		}

		$roomDisplayName = str_replace("{name}", $row['roomName'], $row['roomListDisplay']);
		$roomDisplayName = str_replace("{number}", $row['roomNumber'], $roomDisplayName);

		$temp = array();
		$temp['username']  = $displayName; //$row['username'];
		$temp['building']  = $row['buildingName'];
		$temp['room']      = $roomDisplayName; //$row['roomName'];
		$temp['startTime'] = date($timeFormat,$row['startTime']);
		$temp['endTime']   = date($timeFormat,$row['endTime']);
		if ($hoursOnTable == "1") {
			$reserveTime = ($row['endTime'] - $row['startTime'])/60/60;
			$temp['hoursOnReservationTable'] = ($reserveTime > 23.6)?"24":$reserveTime;
		}
		$temp['edit']      = sprintf('<a href="../create/reservationCreate.php?id=%s">Edit</a>',
			htmlSanitize($row['ID'])
			);
		$temp['delete']    = sprintf('<input type="checkbox" name="delete[]" value="%s" />',
			htmlSanitize($row['ID'])
			);
		$temp['createdBy'] = ($row['createdVia'] != 'Public Interface')?$row['createdBy']:"";

		$reservations[] = $temp;

	}
}

templates::display('header');
?>

<header>
<h1>Reservation Listing</h1>
</header>

<form action="{phpself query="true"}" method="post">
	{csrf}
	<table>
		<tr>
			<td>
				<label for="listBuildingSelect">Building</label>
				<select name="building" id="listBuildingSelect">
					{local var="buildingSelectOptions"}
				</select>
			</td>
			<td>
				<label for="listBuildingRoomsSelect">Room</label>
				<select name="room" id="listBuildingRoomsSelect">
					<option value="any">Any Room</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>
				<label for="start_month">Month:</label>
				<select name="start_month" id="start_month" >
					<?php

					for($I=1;$I<=12;$I++) {
						printf('<option value="%s" %s>%s</option>',
							($I < 10)?"0".$I:$I,
							($I == $currentMonth)?"selected":"",
							$I);
					}
					?>
				</select>
			</td>
			<td>
				<label for="start_day">Day:</label>
				<select name="start_day" id="start_day" >
					<?php

					for($I=1;$I<=31;$I++) {
						printf('<option value="%s" %s>%s</option>',
							($I < 10)?"0".$I:$I,
							($I == $currentDay)?"selected":"",
							$I);
					}
					?>
				</select>
			</td>
			<td>
				<label for="start_year">Year:</label>
				<select name="start_year" id="start_year" >
					<?php

					for($I=$currentYear;$I<=$currentYear+10;$I++) {
						printf('<option value="%s">%s</option>',
							$I,
							$I);
					}
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<input type="checkbox" name="allFutureDate" id="allFutureDate" value="1" checked/>
				<label for="allFutureDate" style="display:inline;">All Dates (overrides date selected above)</label>
			</td>
		</tr>
		<tr>
			<td style="vertical-align:bottom">
				<input type="submit" name="submitListDate" value="Update List" />
			</td>
		</tr>
	</table>
	
</form>

<form action="{phpself query="true"}" method="post" onsubmit="return confirm('Confirm Deletes');">
	{csrf}

	<input type="submit" name="multiDelete" value="Delete Selected Reservations" />
	<?php print $table->display($reservations); ?>
</form>

<?php
templates::display('footer');
?>