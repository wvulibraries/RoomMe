<?php

$engine = EngineAPI::singleton();

// $engine->accessControl("ADgroup","libraryWeb_roomReservation",TRUE,FALSE);
// $engine->accessControl("ADgroup","libraryWeb_roomReservation_rooms",TRUE,FALSE);
// $engine->accessControl("ADgroup","libraryWeb_roomReservation_admin",TRUE,FALSE);
// $engine->accessControl("denyAll",null,null);

accessControl::accessControl("allowAll",null,null);

accessControl::build();
?>