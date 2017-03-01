<?php
function processInsert(){
  $localvars = localvars::getInstance();

  $_POST['MYSQL']['roomID'] = isset($_POST['MYSQL']['roomID']) ?$_POST['MYSQL']['roomID'] : null;

  try {
    if (!reservationPermissions::duplicatePermissionsCheck($_POST['MYSQL']['resourceID'], $_POST['MYSQL']['resourceType'], $_POST['MYSQL']['roomID'], $_POST['MYSQL']['email'])) {
     // return formInfo this is not a Duplicate Entry
     return $_POST['MYSQL'];
    } else {
     $localvars->set("feedbackStatus",'<div class="error"><font color="red">Error: Duplicate Entry</font></div><script>$(function(){$(".formErrors").hide();});</script>');
     throw new exception;
    }
  }

  //catch exception
  catch(Exception $e) {
    errorHandle::newError(__FUNCTION__."() - Error Duplicate Entry.", errorHandle::DEBUG); // debug can be replaced with the severity, including info
    errorHandle::errorMsg("Duplicate Entry");
  }

}

?>
