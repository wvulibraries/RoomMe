<?php
function processInsert(){
  $localvars = localvars::getInstance();
  $formInfo  = $_POST['MYSQL'];

  $roomID = isset($formInfo['roomID']) ? $formInfo['roomID'] : NULL;

  $dupCheck  = reservationPermissions::duplicatePermissionsCheck($formInfo['resourceID'], $formInfo['resourceType'], $roomID, $formInfo['email']);

  if (!$dupCheck) {
   // return formInfo this is not a Duplicate Entry
   return $formInfo;
  } else {
   $localvars->set("feedbackStatus",'<div class="error"> Error: Duplicate Entry </div><script>$(function(){$(".formErrors").hide();});</script>');
   return FALSE;
  }

}

?>
