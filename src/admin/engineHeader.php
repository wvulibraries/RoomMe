<?php

require_once("/home/library/public_html/includes/engineHeader.php");

recurseInsert("acl.php","php"); 

$options = array(
	'username' => 'username',
	'password' => 'password',
	'dbName'   => 'roomReservations',
);
require_once '/home/www.libraries.wvu.edu/phpincludes/databaseConnectors/database.lib.wvu.edu.remote.php';
$db = db::create('mysql', $options, 'appDB');

templates::load("library2012.2col");

$localvars->set("roomResBaseDir","/services/rooms");
?>