<?php

require_once("/home/library/public_html/includes/engineHeader.php");

recurseInsert("acl.php","php"); 

$engine->dbConnect("database","roomReservations",TRUE);

$engine->eTemplate("load","library2012.2col");

$localvars->set("roomResBaseDir","/services/rooms");
?>