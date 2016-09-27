<?php
class reservationPermissions {

  public function getRecords($id = null){
      try {
          // call engine
          $engine    = EngineAPI::singleton();
          $localvars = localvars::getInstance();
          $db        = db::get($localvars->get('dbConnectionName'));
          $sql       = "SELECT * FROM `reservePermissions`";
          $validate  = new validate;

          if (isset($id)) {
           $id        = dbSanitize($id);
          }

          // test to see if Id is present and valid
          if(!isnull($id) && $validate->integer($id)){
              $sql .= sprintf('WHERE id = %s LIMIT 1', $id);
          }

          // if no valid id throw an exception
          if(!$validate->integer($id) && !isnull($id)){
              throw new Exception("I don't want to be tried!");
          }

          // get the results of the query
          $sqlResult = $db->query($sql);
          // if return no results
          // else return the data
          if ($sqlResult->error()) {
              throw new Exception("ERROR SQL" . $sqlResult->errorMsg());
          }
          if ($sqlResult->rowCount() < 1) {
             return "There are no records in the database.";
          }
          else {
              $data = array();
              while($row = $sqlResult->fetch()){
                  $data[] = $row;
              }
              return $data;
          }
      } catch (Exception $e) {
          errorHandle::errorMsg($e->getMessage());
      }
  }

  public function getBuildings($id = null){
      try {
          // call engine
          $engine    = EngineAPI::singleton();
          $localvars = localvars::getInstance();
          $db        = db::get($localvars->get('dbConnectionName'));
          $sql       = "SELECT * FROM `building`";
          $validate  = new validate;

          if (isset($id)) {
           $id        = dbSanitize($id);
          }

          // test to see if Id is present and valid
          if(!isnull($id) && $validate->integer($id)){
              $sql .= sprintf('WHERE id = %s LIMIT 1', $id);
          }

          // if no valid id throw an exception
          if(!$validate->integer($id) && !isnull($id)){
              throw new Exception("I don't want to be tried!");
          }

          // get the results of the query
          $sqlResult = $db->query($sql);
          // if return no results
          // else return the data
          if ($sqlResult->error()) {
              throw new Exception("ERROR SQL" . $sqlResult->errorMsg());
          }
          if ($sqlResult->rowCount() < 1) {
             return "There are no records in the database.";
          }
          else {
              $data = array();
              while($row = $sqlResult->fetch()){
                  $data[] = $row;
              }
              return $data;
          }
      } catch (Exception $e) {
          errorHandle::errorMsg($e->getMessage());
      }
  }

  public function verifyBuildingPermissions($id = null){
      // checks to see if the id passed is in the permissions database
      // if it finds it it returns true
      try {
          // call engine
          $engine    = EngineAPI::singleton();
          $localvars = localvars::getInstance();
          $db        = db::get($localvars->get('dbConnectionName'));
          $sql       = "SELECT * FROM `reservePermissions`";
          $validate  = new validate;

          if (isset($id)) {
           $id        = dbSanitize($id);
          }

          // test to see if Id is present and valid
          if(!isnull($id) && $validate->integer($id)){
              $sql .= sprintf('WHERE resourceID = %s LIMIT 1', $id);
          }

          // if no valid id throw an exception
          if(!$validate->integer($id) && !isnull($id)){
              throw new Exception("I don't want to be tried!");
          }

          // get the results of the query
          $sqlResult = $db->query($sql);
          // if return no results
          // else return the data
          if ($sqlResult->error()) {
              throw new Exception("ERROR SQL" . $sqlResult->errorMsg());
          }

          if ($sqlResult->rowCount() < 1) {
             return FALSE;
          }
          else {
             return TRUE;
          }
      } catch (Exception $e) {
          errorHandle::errorMsg($e->getMessage());
      }
  }

  public function checkPermissions($id = null, $email = null){
      // checks to see if the id passed is in the permissions database
      // if it finds it it returns true
      try {
          // call engine
          $engine    = EngineAPI::singleton();
          $localvars = localvars::getInstance();
          $db        = db::get($localvars->get('dbConnectionName'));
          $sql       = "SELECT * FROM `reservePermissions`";
          $validate  = new validate;

          if (isset($id)) {
           $id        = dbSanitize($id);
          }

          if (isset($email)) {
           $email     = dbSanitize($email);
          }

          // test to see if Id and Email is present and valid
          if(!isnull($id) && $validate->integer($id) && !isnull($email) && $validate->emailAddr($email)){
              $sql .= sprintf(' WHERE resourceID="%s" AND email="%s" LIMIT 1', $id, $email);
          }

          // if no valid id throw an exception
          if(!$validate->integer($id) && !isnull($id)){
              throw new Exception("Error No Valid Resource ID");
          }

          // if no valid email throw an exception
          if(!$validate->emailAddr($email) && !isnull($email)){
              throw new Exception("Error No Valid Email Address");
          }

          // get the results of the query
          $sqlResult = $db->query($sql);

          if ($sqlResult->error()) {
              throw new Exception("ERROR SQL" . $sqlResult->errorMsg());
          }

          //check and see if permissions exist on the resource
          if ($sqlResult->rowCount() < 1) {
             return FALSE;
          }
          else {
             return TRUE;
          }
      } catch (Exception $e) {
          errorHandle::errorMsg($e->getMessage());
      }
  }

  public function setupForm($id = null){
       try {
          // call engine
          $engine    = EngineAPI::singleton();
          $localvars = localvars::getInstance();
          $validate  = new validate;
          if (isset($id)) {
           $id        = dbSanitize($id);
          }

          // create customer form
          $form = formBuilder::createForm('createPermissions');
          $form->linkToDatabase( array(
              'table' => 'reservePermissions'
          ));

          // form titles
          $form->insertTitle = "Add Permissions";
          $form->editTitle   = "Edit Permissions";
          $form->updateTitle = "Update Permissions";

          // if no valid id throw an exception
          if(!$validate->integer($id) && !isnull($id)){
              throw new Exception(__METHOD__.'() - Not a valid integer, please check the integer and try again.');
          }

          // form information
          $form->addField(array(
              'name'    => 'ID',
              'type'    => 'hidden',
              'value'   => $id,
              'primary' => TRUE,
              'fieldClass' => 'id',
              'showIn'     => array(formBuilder::TYPE_INSERT, formBuilder::TYPE_UPDATE),
          ));
          $form->addField(array(
              'name'     => 'resourceID',
              'label'    => 'Resource ID:',
              'type'     => 'select',
              'blankOption' => 'Select a Building',
              'linkedTo' => array(
                    'foreignTable' => 'building',
                    'foreignField' => 'ID',
                    'foreignLabel' => 'name',
                  ),
              'required' => TRUE
          ));
          $form->addField(array(
              'name'       => 'resourceType',
              'label'      => 'Resource Type:',
              'type'       => 'hidden',
              'value'      => "Building",
              'options'    => array("Building", "Policy", "Template", "Room"),
              'required'   => TRUE,
              'duplicates' => TRUE
          ));
          $form->addField(array(
              'name'     => 'email',
              'label'    => 'Email:',
              'required' => TRUE
          ));

          // buttons and submissions
          $form->addField(array(
              'showIn'     => array(formBuilder::TYPE_UPDATE),
              'name'       => 'update',
              'type'       => 'submit',
              'fieldClass' => 'submit',
              'value'      => 'Update Permissions'
          ));
          $form->addField(array(
              'showIn'     => array(formBuilder::TYPE_UPDATE),
              'name'       => 'delete',
              'type'       => 'delete',
              'fieldClass' => 'delete hidden',
              'value'      => 'Delete'
          ));
          $form->addField(array(
              'showIn'     => array(formBuilder::TYPE_INSERT),
              'name'       => 'insert',
              'type'       => 'submit',
              'fieldClass' => 'submit something',
              'value'      => 'Save Permissions'
          ));

          return '{form name="createPermissions" display="form"}';
      } catch (Exception $e) {
          errorHandle::errorMsg($e->getMessage());
      }
  }

  public function deleteRecord($id = null){
      try {
          // call engine
          $engine    = EngineAPI::singleton();
          $localvars = localvars::getInstance();
          $db        = db::get($localvars->get('dbConnectionName'));
          $validate  = new validate;

          if (isset($id)) {
           $id        = dbSanitize($id);
          }

          // test to see if Id is present and valid
          if(isnull($id) || !$validate->integer($id)){
              throw new Exception(__METHOD__.'() -Delete failed, improper id or no id was sent');
          }

          // SQL Results
          $sql = sprintf("DELETE FROM `reservePermissions` WHERE id=%s LIMIT 1", $id);
          $sqlResult = $db->query($sql);
          if(!$sqlResult) {
              throw new Exception(__METHOD__.'Failed to delete permissions.');
          }
          else {
              return "Successfully deleted the permissions";
          }
      } catch (Exception $e) {
          errorHandle::errorMsg($e->getMessage());
          return $e->getMessage();
      }
  }

  public function insertRecord($id, $type, $email){
      try {
          // call engine
          $engine    = EngineAPI::singleton();
          $localvars = localvars::getInstance();
          $db        = db::get($localvars->get('dbConnectionName'));
          $validate  = new validate;

          if (isset($id)) {
           $id        = dbSanitize($id);
          }

          if (isset($type)) {
           $type      = dbSanitize($type);
          }

          if (isset($email)) {
           $email     = dbSanitize($email);
          }

          // test to see if Id is present and valid
          if(isnull($id) || !$validate->integer($id)){
              throw new Exception(__METHOD__.'() -insert failed, improper resource id or no id was sent');
          }

          // test to see if type is present and valid
          if(isnull($type) || !$validate->integer($type)){
              throw new Exception(__METHOD__.'() -insert failed, improper resource type or no type was sent');
          }

          // test to see if email is present and valid
          if(isnull($email) || !$validate->emailAddr($email)){
              throw new Exception(__METHOD__.'() -insert failed, improper email address or no email was sent');
          }

          // SQL Results
          $sql = sprintf("INSERT INTO `reservePermissions` (resourceID, resourceType, email) VALUES (?, ?, ?)");
          $sqlResult = $db->query($sql, array($id, $type, $email));

          if(!$sqlResult) {
              throw new Exception(__METHOD__.'Failed to delete permissions.');
          }
          else {
              return "Successfully deleted the permissions";
          }

      } catch (Exception $e) {
          errorHandle::errorMsg($e->getMessage());
          return $e->getMessage();
      }
  }

  public function renderDataTable(){
    try {
        $engine     = EngineAPI::singleton();
        $localvars  = localvars::getInstance();
        $validate   = new validate;
        $dataRecord = self::getRecords();
        $records    = "";

        foreach($dataRecord as $data){
            //get building record
            $temp = self::getBuildings($data['resourceID']);

            $records .= sprintf("<tr>
                                    <td>%s</td>
                                    <td>%s</td>
                                    <td><a href='../create/?id=%s'>Edit</a></td>
                                    <td><input type='checkbox' name='delete[]' value='%s' /></td>
                                </tr>",
                    htmlSanitize($temp[0]['name']),
                    htmlSanitize($data['email']),
                    htmlSanitize($data['ID']),
                    htmlSanitize($data['ID'])
            );
        }

        $output     = sprintf("	 <form action='{phpself query='true'}' method='post' onsubmit=\"return confirm('Confirm Deletes');\">
  	                             {csrf}
                                 <input type='submit' name='multiDelete' value='Delete Selected Reserve Permissions' />
                                  <div class='dataTable table-responsive'>
                                    <table class='table table-striped'>
                                        <thead>
                                            <tr class='info'>
                                                <th> Resource ID </th>
                                                <th> Email </th>
                                                <th> Edit </th>
                                                <th> Delete </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            %s
                                        </tbody>
                                    </table>
                                </div>",
            $records
        );
        return $output;
    } catch (Exception $e) {
        errorHandle::errorMsg($e->getMessage());
        return $e->getMessage();
    }
  }

  public function uploadForm(){
    try {
        $engine     = EngineAPI::singleton();
        $localvars  = localvars::getInstance();
        $validate   = new validate;

        $dataRecord = self::getBuildings();
        $records    = "";
        $records .= sprintf(" <option value='NULL'>Select a Building</option>");
        foreach($dataRecord as $data){
            $records .= sprintf(" <option value=%s>%s</option>",
                    htmlSanitize($data['ID']),
                    htmlSanitize($data['name'])
            );
        }

        $output     = sprintf("	  <h3>Upload Permissions File</h3>
                                  <form action={phpself query='true'} method='post' enctype='multipart/form-data'>
                                    {csrf}
                                    <div class='uploadForm'>
                                      <br>Resource ID: <select name='resourceID' required>%s</select>
                                      <div hidden>
                                        <br>Resource Type:
                                          <select name='resourceType' required hidden>
                                            <option value=0>Building</option>
                                            <option value=1>Policy</option>
                                            <option value=2>Template</option>
                                            <option value=3>Room</option>
                                          </select>
                                     </div>
                                    <br><br>Select CSV to upload:<br><br>
                                    <input type='file' name='uploadedfile' id='fileToUpload'><br><br>
                                    <input type='submit' value='Upload CSV File' name='submit'>
                                    </div>
                                  </form>",
          $records
        );

        return $output;
    } catch (Exception $e) {
        errorHandle::errorMsg($e->getMessage());
        return $e->getMessage();
    }
  }

  public function insertCSVFile() {
    try {
        // check resource types future enhancement
        if (!isset($_POST['MYSQL']['resourceID']) && !isset($_POST['MYSQL']['resourceType'])) {
          throw new Exception('No resources indicated, please identify your resources');
        }

        // throw exception if not an uploaded file or if there was an error with the upload
        if ($_FILES['uploadedfile']['error'] == 0  && !is_uploaded_file($_FILES['uploadedfile']['tmp_name'])) {
          throw new Exception('File never uploaded!');
        }

        // declare resources
        $resourceID   = $_POST['MYSQL']['resourceID'];
        $resourceType = $_POST['MYSQL']['resourceType'];

        // open file
        $file = fopen($_FILES['uploadedfile']['tmp_name'],'r');

        // use class with csv data
        while(! feof($file)) {
         $temp = fgetcsv($file);
         self::insertRecord($resourceID, $resourceType, $temp[0]);
        }
        fclose($file);
    }
    catch(Exception $e) {
      errorHandle::errorMsg($e->getMessage());
    }
  }

  public function multiDelete($items = null){
    try{
  		foreach ($items as $reservationID){
  			self::deleteRecord($reservationID);
  		}
    }
    catch (Exception $e){
    	errorHandle::errorMsg($e->getMessage());
    }
  }

}
?>
