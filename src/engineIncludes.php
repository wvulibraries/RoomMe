<?php

$databaseOptions = array(
	'username' => 'username',
	'password' => 'password'
);
// require_once('/home/www.libraries.wvu.edu/phpincludes/databaseConnectors/database.lib.wvu.edu.remote.php');
$databaseOptions['dbName'] = 'roomReservations';
$db                        = db::create('mysql', $databaseOptions, 'appDB');

?>