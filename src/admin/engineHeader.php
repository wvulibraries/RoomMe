<?php

require_once '/home/www.libraries.wvu.edu/phpincludes/engine/engineAPI/4.0/engine.php';
$engine = EngineAPI::singleton();
errorHandle::errorReporting(errorHandle::E_ALL);

// Set localVars and engineVars variables
$localvars  = localvars::getInstance();
$enginevars = enginevars::getInstance();

recurseInsert("acl.php","php"); 
recurseInsert("vars.php","php");
recurseInsert('includes/functions.php',"php");

recurseInsert("includes/engineIncludes.php","php");

formBuilder::process();
formBuilder::ajaxHandler();
recurseInsert("includes/engineHeader.php","php");

templates::load("library2012.2col");

$localvars->set("roomResBaseDir","/services/rooms");
?>