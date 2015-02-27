<?php

$databaseOptions = array(
	'username' => 'username',
	'password' => 'password'
);
require_once('/home/www.libraries.wvu.edu/phpincludes/databaseConnectors/database.lib.wvu.edu.remote.php');
$databaseOptions['dbName'] = 'roomReservations';
$db                        = db::create('mysql', $databaseOptions, 'appDB');

recurseInsert("includes/functions.php");
recurseInsert("includes/getUserInfo.php","php");
recurseInsert("includes/class_reservation.php");
recurseInsert("includes/class_room.php");
recurseInsert("includes/class_building.php");
recurseInsert("includes/class_series.php");
recurseInsert("includes/class_userInfo.php");

?>