<?php

require_once '/home/www.libraries.wvu.edu/phpincludes/engine/engineAPI/4.0/engine.php';
$engine = EngineAPI::singleton();
errorHandle::errorReporting(errorHandle::E_ALL);

// Set localVars and engineVars variables
$localvars  = localvars::getInstance();
$enginevars = enginevars::getInstance();

// require_once "/home/library/public_html/includes/engineHeader.php";

recurseInsert("acl.php","php"); 
recurseInsert("vars.php","php");

$options = array(
	'username' => 'username',
	'password' => 'password',
	'dbName'   => 'roomReservations',
);
require_once '/home/www.libraries.wvu.edu/phpincludes/databaseConnectors/database.lib.wvu.edu.remote.php';
$db = db::create('mysql', $options, $localvars->get('dbConnectionName'));

recurseInsert("includes/engineHeader.php","php");
templates::load("library2012.3col");

$localvars->set("roomResBaseDir","/services/rooms");

?>