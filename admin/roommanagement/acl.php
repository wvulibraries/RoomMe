<?php

$engine = EngineAPI::singleton();

$engine->accessControl("ADgroup","libraryWeb_roomReservation_rooms",TRUE,FALSE);
$engine->accessControl("ADgroup","libraryWeb_roomReservation_admin",TRUE,FALSE);
$engine->accessControl("denyAll",null,null);

$engine->accessControl("build");
?>