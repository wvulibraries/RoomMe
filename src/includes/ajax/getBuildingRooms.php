<?php

require_once "../../engineHeader.php";

$buildingObj = new building;
$buildingRooms = $buildingObj->getRooms($_GET['MYSQL']['buildingID']);

print json_encode($buildingRooms);

?>