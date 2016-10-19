<?php
		require_once("../../engineHeader.php");

		$reservationPermissions = new reservationPermissions;

		$building = isset($_GET['MYSQL']['building']) ? $_GET['MYSQL']['building'] : null;

		header('Content-Type: application/json');
		print (json_encode($reservationPermissions->getRooms($building)));
?>
