<?php

$localvars = localvars::getInstance();
$building  = new building;
$localvars->set("buildingCalendars",$building->calendarList());

?>
<ul>
	<li><a href="{local var="roomResBaseDir"}/admin/index.php">Admin Home</a></li>
	<li>Reservation Management
		<ul>
			<!-- <li><a href="">View Calendar</a></li> -->
			<li><a href="{local var="roomResBaseDir"}/admin/reservations/create/">Create Reservation</a></li>
			<li><a href="{local var="roomResBaseDir"}/admin/reservations/list/">List all Reservations</a></li>
			<li><a href="{local var="roomResBaseDir"}/admin/reservations/series/create/">Create Series</a></li>
			<li><a href="{local var="roomResBaseDir"}/admin/reservations/series/list/">List all Series</a></li>
			<li><a href="{local var="roomResBaseDir"}/admin/reservations/print/">Print Reservations</a></li>
			<li><a href="{local var="roomResBaseDir"}/admin/reservations/search/">Search</a></li>
		</ul>
	</li>
	<?php if (checkGroup("libraryWeb_roomReservation_rooms") || checkGroup("libraryWeb_roomReservation_admin")) { ?>
	<li>Room Management
		<ul>
			<li><a href="{local var="roomResBaseDir"}/admin/roommanagement/buildings/">Buildings</a></li>
			<li><a href="{local var="roomResBaseDir"}/admin/roommanagement/buildings/permissions/create/">Create Reservation Restrictions</a></li>
			<li><a href="{local var="roomResBaseDir"}/admin/roommanagement/buildings/permissions/upload/">Upload Reservation Restrictions</a></li>
			<li><a href="{local var="roomResBaseDir"}/admin/roommanagement/buildings/permissions/list/">List all Reservation Restrictions</a></li>
			<li><a href="{local var="roomResBaseDir"}/admin/roommanagement/policies/">Room Policies</a></li>
			<li><a href="{local var="roomResBaseDir"}/admin/roommanagement/templates/">Room Templates</a></li>
			<li><a href="{local var="roomResBaseDir"}/admin/roommanagement/rooms/">Rooms</a></li>
			<li><a href="{local var="roomResBaseDir"}/admin/roommanagement/equipment/">Equipment</a></li>
			<li><a href="{local var="roomResBaseDir"}/admin/roommanagement/equipment/types/">Equipment Types</a></li>
		</ul>
	</li>
	<?php } ?>
	<?php if (checkGroup("libraryWeb_roomReservation_admin")) { ?>
	<li>Configuration
		<ul>
			<li><a href="{local var="roomResBaseDir"}/admin/config/messages/">Messages</a></li>
			<li><a href="{local var="roomResBaseDir"}/admin/config/snippets/">Snippets</a></li>
			<li><a href="{local var="roomResBaseDir"}/admin/config/statistics/">Statistics</a></li>
			<li><a href="{local var="roomResBaseDir"}/admin/config/settings/">System Settings</a></li>
			<li><a href="{local var="roomResBaseDir"}/admin/config/via/">Via Management</a></li>
		</ul>
	</li>
	<?php } ?>
	<li> Building Calendars
		<ul>
			{local var="buildingCalendars"}
		</ul>
	</li>
</ul>
