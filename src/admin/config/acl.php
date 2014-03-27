<?php

$engine = EngineAPI::singleton();

$engine->accessControl("ADgroup","libraryWeb_roomReservation_admin",TRUE);
$engine->accessControl("denyAll",null,null);

$engine->accessControl("build");
?>