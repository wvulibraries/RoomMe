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
recurseInsert("includes/getUserInfo.php","php");

require 'engineIncludes.php';

recurseInsert("includes/engineHeader.php","php");
templates::load("library2012.3col");

$localvars->set("roomResBaseDir","/services/rooms");

?>