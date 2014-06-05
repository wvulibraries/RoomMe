<?php
require_once("engineHeader.php");
recurseInsert("includes/functions.php","php");

$error      = FALSE;
$buildingID = "";
if (!isset($_GET['MYSQL']['building'])) {
	$error = TRUE;
	errorHandle::errorMsg("Invalid or missing building ID");
}
else {
	$buildingID = $_GET['MYSQL']['building'];
}

$buildingName = getBuildingName($buildingID);
$localvars->set("libraryName",$buildingName);


$buildingRooms = getRoomsForBuilding($buildingID);

$localvars->set("errors",errorHandle::prettyPrint());

$engine->eTemplate("include","header");
?>

<header>
<h1>{local var="libraryName"}</h1>
</header>

{local var="errors"}

	<ul>

		<?php if (isset($row['hoursRSS']) && !is_empty($row['hoursRSS'])) { ?>
		<li>
			<a href="hours.php?building=<?php print $row['ID'] ?>">View Library Hours</a>
		</li>
		<?php } ?>

		<?php if (isset($row['url']) && !is_empty($row['url'])) { ?>
		<li>
			<a href="<?php print $row['url'] ?>">View Library Homepage</a>
		</li>
		<?php } ?>

		<li>
			<a href="#" class="calendarModal_link" data-type="building" data-id="<?php print $buildingID ?>">View Reservation Calendar &ndash; All Rooms</a>
		</li>
	</ul>

<section id="reservationsBuildingRoomsList">

	<header>
		<h1>Rooms</h1>
	</header>

<ul>
<?php foreach($buildingRooms as $I=>$room) { ?>

	<li>
		<a href="room.php?room=<?php print $room['ID'];?>"><?php print $room['displayName']; ?></a>
	</li>

<?php } ?>
</ul>

</section>

<div id="calendarModal">
</div>

<?php
$engine->eTemplate("include","footer");
?>