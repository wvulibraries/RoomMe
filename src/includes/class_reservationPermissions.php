<?php
class reservationPermissions {

  private $localvars;
  private $engine;
  private $db;
  private $validate;

  function __construct() {
    $this->engine    = EngineAPI::singleton();
    $this->localvars = localvars::getInstance();
    $this->db        = db::get($this->localvars->get('dbConnectionName'));
    $this->validate  = new validate;
  }

  public function getRecords($id = null){
    $sql = "SELECT * FROM `reservePermissions`";
    try {
      if(!isnull($id) && !$this->validate->integer($id)){
        throw new Exception("Invalid ID provided.");
      }
      if(!isnull($id)){
        $sql .= "WHERE id = ? LIMIT 1";
      }
      $sqlResult = $this->db->query($sql,array($id));
      if ($sqlResult->error()) {
        throw new Exception("ERROR SQL" . $sqlResult->errorMsg());
      }
      return $sqlResult->fetchAll();
    } catch (Exception $e) {
      errorHandle::newError(__METHOD__."() - ".$e->getMessage, errorHandle::DEBUG);
      return false;
    }
  }

  public function getBuildings($id = null){
    $sql       = "SELECT * FROM `building`";
    try {
      if(!isnull($id) && !$this->validate->integer($id)){
        throw new Exception("Invalid ID provided.");
      }
      if(!isnull($id)){
        $sql .= "WHERE id = ? LIMIT 1";
      }
      $sqlResult = $this->db->query($sql,array($id));
      if ($sqlResult->error()) {
        throw new Exception("ERROR SQL" . $sqlResult->errorMsg());
      }
      return $sqlResult->fetchAll();
    } catch (Exception $e) {
      errorHandle::newError(__METHOD__."() - ".$e->getMessage, errorHandle::DEBUG);
      return false;
    }
  }

  public function permissionsSet($id = null){
      // checks to see if the building ID passed is in the reservePermissions table
      // if it finds it it returns true
      try {
        if(!isnull($id) && !$this->validate->integer($id)){
          throw new Exception("Invalid ID provided.");
        }

        $sql = "SELECT * FROM `reservePermissions` WHERE resourceID = ?";
        $sqlResult = $this->db->query($sql,array($id));
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
        errorHandle::newError(__METHOD__."() - ".$e->getMessage, errorHandle::DEBUG);
        return FALSE;
      }
  }

  public function checkBuilding($id = null){
    try {
        // test to see if Id is present and valid
        if(!isnull($id) && !$this->validate->integer($id)){
            throw new Exception("Invalid ID provided.");
        }

        $sql = "SELECT * FROM `reservePermissions` WHERE resourceID = ? AND resourceType = 0 LIMIT 1";

        // get the results of the query
        $sqlResult = $this->db->query($sql, array($id));

        if ($sqlResult->error()){
            throw new Exception("ERROR SQL" . $sqlResult->errorMsg());
        }

        //check and see if permissions exist on the resource
        if ($sqlResult->rowCount() < 1) {
           return FALSE;
        }
        else {
           return TRUE;
        }

    } catch (Exception $e){
        errorHandle::errorMsg(__METHOD__."() - ".$e->getMessage, errorHandle::DEBUG);
        return false;
    }
  }

  public function checkRoom($id = null){
    try {
        // test to see if Id is present and valid
        if(!isnull($id) && !$this->validate->integer($id)){
            throw new Exception("Invalid ID provided.");
        }

        $sql = "SELECT * FROM `reservePermissions` WHERE roomID = ? AND resourceType = 3";

        // get the results of the query
        $sqlResult = $this->db->query($sql, array($id));

        if ($sqlResult->error()){
            throw new Exception("ERROR SQL" . $sqlResult->errorMsg());
        }

        //check and see if permissions exist on the resource
        if ($sqlResult->rowCount() < 1) {
           return FALSE;
        }
        else {
           return TRUE;
        }

    } catch (Exception $e){
        errorHandle::errorMsg(__METHOD__."() - ".$e->getMessage, errorHandle::DEBUG);
        return false;
    }
  }

  public function checkBuildingPermissions($id = null, $email = null){
    try {
        // test to see if Id is present and valid
        if(!isnull($id) && !$this->validate->integer($id)){
            throw new Exception("Invalid ID provided.");
        }

        // test to see if Id is present and valid
        if(!isnull($email) && !$this->validate->emailAddr($email)){
            throw new Exception("Invalid Email provided.");
        }

        $sql = "SELECT * FROM `reservePermissions` WHERE resourceID = ? AND email = ? LIMIT 1";

        // get the results of the query
        $sqlResult = $this->db->query($sql, array($id, $email));

        if ($sqlResult->error()){
            throw new Exception("ERROR SQL" . $sqlResult->errorMsg());
        }

        //check and see if permissions exist on the resource
        if ($sqlResult->rowCount() < 1) {
           return FALSE;
        }
        else {
           return TRUE;
        }

    } catch (Exception $e){
        errorHandle::errorMsg(__METHOD__."() - ".$e->getMessage, errorHandle::DEBUG);
        return false;
    }
  }

  public function checkRoomPermissions($id = null, $email = null){
    try {
        // test to see if Id is present and valid
        if(!isnull($id) && !$this->validate->integer($id)){
            throw new Exception("Invalid ID provided.");
        }

        // test to see if Id is present and valid
        if(!isnull($email) && !$this->validate->emailAddr($email)){
            throw new Exception("Invalid Email provided.");
        }

        $sql = "SELECT * FROM `reservePermissions` WHERE roomID = ? AND email = ? LIMIT 1";

        // get the results of the query
        $sqlResult = $this->db->query($sql, array($id, $email));

        if ($sqlResult->error()){
            throw new Exception("ERROR SQL" . $sqlResult->errorMsg());
        }

        //check and see if permissions exist on the resource
        if ($sqlResult->rowCount() < 1) {
           return FALSE;
        }
        else {
           return TRUE;
        }

    } catch (Exception $e){
        errorHandle::errorMsg(__METHOD__."() - ".$e->getMessage, errorHandle::DEBUG);
        return false;
    }
  }

  public function setupForm($id = null, $buildingID = null){
    try {
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
        if(!$this->validate->integer($id) && !isnull($id)){
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
            'label'    => 'Building:',
            'type'     => 'select',
            'value'    => $buildingID,
            'fieldClass' => 'resourceID',
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
            'label'      => 'Type:',
            'type'       => 'select',
            'fieldClass' => 'resourceType',
            'value'      => "Building",
            'options'    => array("Building", "Policy", "Template", "Room"),
            'required'   => TRUE,
            'duplicates' => TRUE
        ));

        $form->addField(array(
            'name'     => 'roomID',
            'label'    => 'Room:',
            'type'     => 'select',
            'value'    => $id,
            'fieldClass' => 'rooms',
            'blankOption' => 'Select a Room',
            'linkedTo' => array(
                  'foreignSQL' => "SELECT `ID`, CONCAT(`name`, ' - ', `number`) AS `name` FROM `rooms` ORDER BY `number`",
                  'foreignTable' => 'rooms',
                  'foreignField' => 'ID',
                  'foreignLabel' => 'name',
                ),
            'required' => FALSE
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
        return false;
    }
  }

  public function getRooms($building = null){
    //function requires the building ID and returns a list of all rooms for that building
    try {
        // test to see if Id is present and valid
        if(!isnull($building) && $this->validate->integer($building)){
            $sql = "SELECT `ID`, CONCAT(`name`, ' - ', `number`) AS `name`, `building` FROM `rooms` WHERE `building` = ? ORDER BY `number`";
            // get the results of the query
            $sqlResult = $this->db->query($sql, array($building));
        }
        else {
            $sql = "SELECT `ID`, CONCAT(`name`, ' - ' , `number`) AS `name`, `building` FROM `rooms` ORDER BY `number`";
            // get the results of the query
            $sqlResult = $this->db->query($sql);
        }

        if ($sqlResult->error()) {
            throw new Exception("ERROR SQL" . $sqlResult->errorMsg());
        }

        return $sqlResult->fetchAll();

    } catch (Exception $e) {
        errorHandle::errorMsg($e->getMessage());
    }
  }

  public function getRoom($id = null){
    $sql       = "SELECT * FROM `rooms`";
    try {
      if(!isnull($id) && !$this->validate->integer($id)){
        throw new Exception("Invalid ID provided.");
      }
      if(!isnull($id)){
        $sql .= "WHERE id = ? LIMIT 1";
      }
      $sqlResult = $this->db->query($sql,array($id));
      if ($sqlResult->error()) {
        throw new Exception("ERROR SQL" . $sqlResult->errorMsg());
      }
      return $sqlResult->fetchAll();
    } catch (Exception $e) {
      errorHandle::newError(__METHOD__."() - ".$e->getMessage, errorHandle::DEBUG);
      return false;
    }

  }

  public function deleteRecord($id = null){
    try {
        // test to see if Id is present and valid
        if(!isnull($id) && !$this->validate->integer($id)){
            throw new Exception("Invalid ID provided.");
        }

        $sql = "DELETE FROM `reservePermissions` WHERE id = ? LIMIT 1";

        // get the results of the query
        $sqlResult = $this->db->query($sql, array($id));

        if ($sqlResult->error()) {
            throw new Exception("ERROR SQL" . $sqlResult->errorMsg());
        }

        return $sqlResult->fetchAll();

    } catch (Exception $e) {
        errorHandle::errorMsg(__METHOD__."() - ".$e->getMessage, errorHandle::DEBUG);
        return false;
    }
  }

  public function insertRecord($id, $type, $room, $email){
    try {
        // test to see if Id is present and valid
        if(!isnull($id) && !$this->validate->integer($id)){
            throw new Exception("Invalid ID provided.");
        }

        if(!isnull($type) && !$this->validate->integer($type)){
            throw new Exception("Invalid Type provided.");
        }

        if(!isnull($room) && !$this->validate->integer($room)){
            throw new Exception("Invalid Room ID provided.");
        }

        if(!isnull($email) && !$this->validate->emailAddr($email)){
            throw new Exception("Invalid ID provided.");
        }

        $sql = "INSERT INTO `reservePermissions` (resourceID, resourceType, roomID, email) VALUES (?, ?, ?, ?)";

        // get the results of the query
        $sqlResult = $this->db->query($sql, array($id, $type, $room, $email));

        if ($sqlResult->error()) {
            throw new Exception("ERROR SQL" . $sqlResult->errorMsg());
        }

        return true;

    } catch (Exception $e) {
        errorHandle::errorMsg(__METHOD__."() - ".$e->getMessage, errorHandle::DEBUG);
        return false;
    }
  }

  public function renderDataTable(){
    try {
        $dataRecord = self::getRecords();
        $records    = "";

        foreach($dataRecord as $data){
            switch ($data['resourceType']) {
               case 0:
                   //get building record
                   $building = self::getBuildings($data['resourceID']);
                   $name = $building[0]['name'];
                   $type = "Building";
                   break;
               case 1:
                   //get Policy record
                   //$temp = self::getBuildings($data['resourceID']);
                   $type = "Policy";
                   break;
               case 2:
                   //get Template record
                   //$temp = self::getBuildings($data['resourceID']);
                   $type = "Template";
                   break;
               case 3:
                  //get Room record
                   $room = self::getRoom($data['roomID']);
                   $building = self::getBuildings($data['resourceID']);
                   $name = $building[0]['name'] . ' - ' . $room[0]['name'] . ' - ' . $room[0]['number'];
                   $type = "Room";
                   break;
               default:
                   echo "";
            }

            $records .= sprintf("<tr>
                                    <td>%s</td>
                                    <td>%s</td>
                                    <td>%s</td>
                                    <td><a href='../create/?id=%s&building=%s'>Edit</a></td>
                                    <td><input type='checkbox' name='delete[]' value='%s' /></td>
                                </tr>",

                    htmlSanitize($data['email']),
                    htmlSanitize($type),
                    htmlSanitize($name),
                    htmlSanitize($data['ID']),
                    htmlSanitize($data['resourceID']),
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
                                                <th> Email </th>
                                                <th> Resource Type </th>
                                                <th> Resource ID </th>
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
        return false;
    }
  }

  public function uploadForm(){
    try {
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
                                      <label>Building:</label> <select name='resourceID' required class='resourceID'>%s</select>
                                      <div>
                                        <label>Type:</label>
                                        <select name='resourceType' required class='resourceType'>
                                          <option value=0>Building</option>
                                          <!-- <option value=1>Policy</option> -->
                                          <!-- <option value=2>Template</option> -->
                                          <option value=3>Room</option>
                                        </select>
                                        <br>Room:
                                        <select name='roomID' class='rooms'>
                                         <option value='NULL'>Select a Room</option>
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
        return false;
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

        if (isset($_POST['MYSQL']['roomID'])){
          $roomID     = $_POST['MYSQL']['roomID'];
        }
        else {
          $roomID     = NULL;
        }

        // open file
        $file = fopen($_FILES['uploadedfile']['tmp_name'],'r');

        // use class with csv data
        while(!feof($file)){
         $temp = fgetcsv($file);
         if (!isnull($temp[0])){
           self::insertRecord($resourceID, $resourceType, $roomID, $temp[0]);
         }
        }
        fclose($file);
    }
    catch(Exception $e) {
      errorHandle::errorMsg($e->getMessage());
      return false;
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
      return false;
    }
  }

}
?>
