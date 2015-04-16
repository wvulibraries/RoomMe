<?php

require_once '/home/www.libraries.wvu.edu/phpincludes/engine/engineAPI/4.0/engine.php';
$engine = EngineAPI::singleton();
errorHandle::errorReporting(errorHandle::E_ALL);

// Set localVars and engineVars variables
$localvars  = localvars::getInstance();
$enginevars = enginevars::getInstance();

$localvars->set("currentDisplayObjectTitle","Room Reservations");
$localvars->set("policiesPage","/services/rooms/policies/");
$localvars->set("advancedSearch","/services/rooms/find/");

// require_once "/home/library/public_html/includes/engineHeader.php";

recurseInsert("acl.php","php"); 
recurseInsert("includes/vars.php","php");
recurseInsert("includes/getUserInfo.php","php");

require 'engineIncludes.php';

$messages = new messages;

recurseInsert("includes/engineHeader.php","php");
// templates::load("library2012.3col");
templates::load("rooms2015");

$localvars->set("roomResBaseDir","/services/rooms");

?>