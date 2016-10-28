<?php
  $resData     =  new reservationPermissions;
  $dataRecord = $resData->getBuildings();;
  $records     = "";

  foreach($dataRecord as $data){
      $records .= sprintf(" <option value=%s>%s</option>",
              htmlSanitize($data['ID']),
              htmlSanitize($data['name'])
      );
  }

  $localvars->set('resourceList', $records);
?>

<form action={phpself query='true'} method='post' enctype='multipart/form-data'>
  {csrf}
  <div class='permissionUploadForm'>
    <ul>
      <li>
        <label class="makeBold">Building:</label>
        <select name='resourceID' required class='resourceID'>
          {local var="resourceList"}
        </select>
      </li>

      <li>
      <label class="makeBold">Type:</label>
      <select name='resourceType' required class='resourceType'>
        <option value=0>Building</option>
        <!-- <option value=1>Policy</option> -->
        <!-- <option value=2>Template</option> -->
        <option value=3>Room</option>
      </select>
    </li>

      <li>
        <label>Room:</label>
        <select name='roomID' class='rooms'>
         <option value='NULL'>Select a Room</option>
        </select>
      </li>
      <li>
        <label class="makeBold"> CSV Upload:</label>
        <input type='file' name='uploadedfile' id='fileToUpload'><br><br>
        <input type='submit' value='Upload CSV File' name='submit'>
      </li>
    </ul>

  </div>
</form>
