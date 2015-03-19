<?php

require_once '/home/www.libraries.wvu.edu/phpincludes/engine/engineAPI/4.0/engine.php';
$engine = EngineAPI::singleton();
errorHandle::errorReporting(errorHandle::E_ALL);

// Set localVars and engineVars variables
$localvars  = localvars::getInstance();
$enginevars = enginevars::getInstance();

$localvars->set("currentDisplayObjectTitle","Room Reservations");

// require_once "/home/library/public_html/includes/engineHeader.php";

recurseInsert("acl.php","php"); 
recurseInsert("includes/vars.php","php");
recurseInsert("includes/getUserInfo.php","php");

require 'engineIncludes.php';

$messages = new messages;

recurseInsert("includes/engineHeader.php","php");
// templates::load("library2012.3col");
templates::load("library2014-backpage");

$localvars->set("roomResBaseDir","/services/rooms");

?>