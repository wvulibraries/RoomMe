<?php

require_once '/home/www.libraries.wvu.edu/phpincludes/engine/engineAPI/4.0/engine.php';
$engine = EngineAPI::singleton();
errorHandle::errorReporting(errorHandle::E_ALL);

// require_once "/home/library/public_html/includes/engineHeader.php";

recurseInsert("acl.php","php"); 

$engine->dbConnect("database","roomReservations",TRUE);

$engine->eTemplate("load","library2012.3col");

localvars::add("roomResBaseDir","/services/rooms");

?>