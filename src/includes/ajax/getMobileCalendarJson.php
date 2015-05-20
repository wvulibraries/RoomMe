<?php

require_once "../../engineHeader.php";


$date['month'] = date("m");
$date['day']   = date("d");
$date['year']  = date("Y");

$calendar = new calendar;

print json_encode($calendar->buildJSON("mobile", $_GET['MYSQL']['objectID'], $date));

?>