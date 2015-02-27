<ul>
	<li><a href="{local var="roomResBaseDir"}/admin/index.php">Admin Home</a></li>
	<li>Reservation Management
		<ul>
			<!-- <li><a href="">View Calendar</a></li> -->
			<li><a href="{local var="roomResBaseDir"}/admin/reservationLibrarySelect.php">Create Reservation</a></li>
			<li><a href="{local var="roomResBaseDir"}/admin/reservationsList.php">List all Reservations</a></li>
			<li><a href="{local var="roomResBaseDir"}/admin/reservationLibrarySelect.php?type=series">Create Series</a></li>
			<li><a href="{local var="roomResBaseDir"}/admin/seriesList.php">List all Series</a></li>
			<li><a href="{local var="roomResBaseDir"}/admin/printReservations.php">Print Reservations</a></li>
			<li><a href="{local var="roomResBaseDir"}/admin/search/">Search</a></li>
		</ul>
	</li>
	<?php if (checkGroup("libraryWeb_roomReservation_rooms") || checkGroup("libraryWeb_roomReservation_admin")) { ?>
	<li>Room Management
		<ul>
			<li><a href="{local var="roomResBaseDir"}/admin/roommanagement/buildings.php">Buildings</a></li>
			<li><a href="{local var="roomResBaseDir"}/admin/roommanagement/roomPolicies.php">Room Policies</a></li>
			<li><a href="{local var="roomResBaseDir"}/admin/roommanagement/roomTemplates.php">Room Templates</a></li>
			<li><a href="{local var="roomResBaseDir"}/admin/roommanagement/rooms.php">Rooms</a></li>
			<li><a href="{local var="roomResBaseDir"}/admin/roommanagement/equipment.php">Equipment</a></li>
			<li><a href="{local var="roomResBaseDir"}/admin/roommanagement/equipmentTypes.php">Equipment Types</a></li>
		</ul>
	</li>
	<?php } ?>
	<?php if (checkGroup("libraryWeb_roomReservation_admin")) { ?>
	<li>Configuration
		<ul>
			<li><a href="{local var="roomResBaseDir"}/admin/config/resultsMsgs.php">Result Messages</a></li>
			<li><a href="{local var="roomResBaseDir"}/admin/config/snippets.php">Snippets</a></li>
			<li><a href="{local var="roomResBaseDir"}/admin/config/stats.php">Stats</a></li>
			<li><a href="{local var="roomResBaseDir"}/admin/config/sysconfig.php">System Configuration</a></li>
			<li><a href="{local var="roomResBaseDir"}/admin/config/via.php">Via Management</a></li>
			<!-- <li><a href="{local var="roomResBaseDir"}/admin/config/emails.php">Email Responses</a></li> -->
		</ul>
	</li>
	<?php } ?>
</ul>