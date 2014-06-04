<?php

require_once '/home/www.libraries.wvu.edu/phpincludes/engine/engineAPI/4.0/engine.php';
$engine = EngineAPI::singleton();
errorHandle::errorReporting(errorHandle::E_ALL);

// require_once "/home/library/public_html/includes/engineHeader.php";

recurseInsert("acl.php","php"); 

$options = array(
	'username' => 'username',
	'password' => 'password',
	'dbName'   => 'roomReservations',
);
require_once '/home/www.libraries.wvu.edu/phpincludes/databaseConnectors/database.lib.wvu.edu.remote.php';
$db = db::create('mysql', $options, 'appDB');

$engine->eTemplate("load","library2012.3col");

localvars::add("roomResBaseDir","/services/rooms");

?>