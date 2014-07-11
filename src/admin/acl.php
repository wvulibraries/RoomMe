<?php

$engine = EngineAPI::singleton();

accessControl::accessControl("ADgroup","libraryWeb_roomReservation",TRUE,FALSE);
accessControl::accessControl("ADgroup","libraryWeb_roomReservation_rooms",TRUE,FALSE);
accessControl::accessControl("ADgroup","libraryWeb_roomReservation_admin",TRUE,FALSE);
accessControl::accessControl("denyAll",null,null);

accessControl::build();
?>