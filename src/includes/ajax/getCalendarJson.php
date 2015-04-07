<?php

require_once "../../engineHeader.php";


$date['month'] = $_GET['MYSQL']['month'];
$date['day']   = $_GET['MYSQL']['day'];
$date['year']  = $_GET['MYSQL']['year'];

$calendar = new calendar;

print json_encode($calendar->buildJSON($_GET['MYSQL']['type'], $_GET['MYSQL']['objectID'], $date));

?>