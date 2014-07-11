<?php

$engine = EngineAPI::singleton();

accessControl::accessControl("ADgroup","libraryWeb_roomReservation_admin",TRUE);
accessControl::accessControl("denyAll",null,null);

accessControl::build();
?>