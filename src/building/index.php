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


$buildingRooms = getRoomsForBuilding($buildingID,TRUE);

$localvars->set("errors",errorHandle::prettyPrint());

templates::display('header');
?>

<h3 class="roomH3" style="display: inline-block;">{local var="libraryName"}</h3>

<!-- Extra Links -->
<a class="policyLink roomTabletDesktop" href="{local var="advancedSearch"}">Advanced Search <i class="fa fa-cog"></i></a>
<a class="policyLink3 roomTabletDesktop" href="{local var="policiesPage"}">Reservation Policies 
	<i class="fa fa-exclamation-circle"></i>
</a>

<hr class="roomHR roomTabletDesktop" />

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

	</ul>

<section id="reservationsBuildingRoomsList">
	<h4>Rooms</h4>
	<ul>
	<?php foreach($buildingRooms as $I=>$room) { ?>

		<li class="whhp">
			<a href="room/?room=<?php print htmlSanitize($room['ID']);?>"><?php if (!is_empty($room['pictureURL'])) {?>
				<img src="<?php print htmlSanitize($room['pictureURL']);?>" class="buildingListPicture" alt="<?php print htmlSanitize($room['displayName']); ?>" /><span class="reservationsBuildingRoomsListName">
					<?php } print htmlSanitize($room['displayName']); ?>
				</span></a>
		</li>

	<?php } ?>
	</ul>
</section>

<div id="calendarModal">
</div>

	<!-- Advanced Search -->
	<div style="clear:both;"></div>
	<hr class="roomHR roomMobile" />
	<a href="{local var="advancedSearch"}" id="asbutton" class="bSubmit roomMobile"><i class="fa fa-cog"></i> Advanced Search</a>

	<div class="clear:both;"></div>
	<br>

	<!-- Rooms Navigation -->
	<?php recurseInsert("includes/roomsByBuilding.php","php") ?>

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