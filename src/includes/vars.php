<?php

$localvars = localvars::getInstance();

$localvars->set('dbConnectionName', 'appDB');
$localvars->set("roomReservationHome","/services/rooms");

define("ROOM_RESOURCE_TYPE",     "room");
define("TEMPLATE_RESOURCE_TYPE", "template");
define("POLICY_RESOURCE_TYPE",   "policy");
define("BUILDING_RESOURCE_TYPE", "building");

?>
