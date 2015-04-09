<?php
require_once("../engineHeader.php");
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
$localvars->set("libraryName",htmlSanitize($buildingName));


$buildingRooms = getRoomsForBuilding($buildingID);

$localvars->set("errors",errorHandle::prettyPrint());

templates::display('header');
?>

<header>
<h3>{local var="libraryName"}</h3>
</header>

{local var="errors"}

	<ul>

		<?php if (isset($row['hoursRSS']) && !is_empty($row['hoursRSS'])) { ?>
		<li>
			<a href="hours.php?building=<?php print htmlSanitize($row['ID']) ?>">View Library Hours</a>
		</li>
		<?php } ?>

		<?php if (isset($row['url']) && !is_empty($row['url'])) { ?>
		<li>
			<a href="<?php print htmlSanitize($row['url']) ?>">View Library Homepage</a>
		</li>
		<?php } ?>

		<!-- This is no longer neccesary w/new table -->
		<!-- <li>
			<a href="#" class="calendarModal_link" data-type="building" data-id="<?php print htmlSanitize($buildingID) ?>">View Reservation Calendar &ndash; All Rooms</a>
		</li> -->
	</ul>

<section id="reservationsBuildingRoomsList">

	<header>
		<h4>Rooms</h4>
	</header>

<ul>
<?php foreach($buildingRooms as $I=>$room) { ?>

	<li>
		<a href="room/?room=<?php print htmlSanitize($room['ID']);?>"><?php if (!is_empty($room['pictureURL'])) {?><img src="<?php print htmlSanitize($room['pictureURL']);?>" class="buildingListPicture" alt="<?php print htmlSanitize($room['displayName']); ?>" /><?php } print htmlSanitize($room['displayName']); ?></a>
	</li>

<?php } ?>
</ul>

</section>

<div id="calendarModal">
</div>

<?php
templates::display('footer');
?>