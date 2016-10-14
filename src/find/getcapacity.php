<?php
		require_once("../engineHeader.php");

		$building = new building;

		$buildingID = isset($_GET['MYSQL']['building']) ? $_GET['MYSQL']['building'] : null;

		header('Content-Type: application/json');
		print (json_encode($building->getRooms($buildingID)));
?>
