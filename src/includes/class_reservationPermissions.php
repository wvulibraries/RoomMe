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

  /**
   * Function permissionsSet
   * Name : Tracy McCormick
   * Function requires Building ID to be passed
   * Description
   * Checks to see if the building ID that was passed is in the
   * reservePermissions table if it finds it function returns true
   */
  public function permissionsSet($buildingID = null){
      try {
        if(!isnull($buildingID) && !$this->validate->integer($buildingID)){
          throw new Exception("Invalid Building ID provided.");
        }

        $sql = "SELECT * FROM `reservePermissions` WHERE resourceID = ?";
        $sqlResult = $this->db->query($sql,array($buildingID));
        if ($sqlResult->error()) {
          throw new Exception("ERROR SQL" . $sqlResult->errorMsg());
        }

        if ($sqlResult->rowCount() < 1) {
           return FALSE;
        }

        return true;

      } catch (Exception $e) {
        errorHandle::newError(__METHOD__."() - ".$e->getMessage, errorHandle::DEBUG);
        return FALSE;
      }
  }

  /**
   * Function checkBuilding
   * Name : Tracy McCormick
   * Description
   * Check and sees if the building ID is listed in the reservePermission table
   * returns true if found false otherwise
   */
  public function checkBuilding($buildingID = null){
    try {
        // test to see if Id is present and valid
        if(!isnull($buildingID) && !$this->validate->integer($buildingID)){
            throw new Exception("Invalid Building ID provided.");
        }

        $sql = "SELECT * FROM `reservePermissions` WHERE resourceID = ? AND resourceType = 0 LIMIT 1";

        // get the results of the query
        $sqlResult = $this->db->query($sql, array($buildingID));

        if ($sqlResult->error()){
            throw new Exception("ERROR SQL" . $sqlResult->errorMsg());
        }

        //check and see if permissions exist on the resource
        if ($sqlResult->rowCount() < 1) {
          return false;
        }

        return TRUE;

    } catch (Exception $e){
        errorHandle::errorMsg(__METHOD__."() - ".$e->getMessage, errorHandle::DEBUG);
        return false;
    }
  }

  /**
   * Function checkRoom
   * Name : Tracy McCormick
   * Description
   * Check and sees if the room ID is listed in the reservePermission table
   * returns true if found false otherwise
   */
  public function checkRoom($roomID = null){
    try {
        // test to see if Id is present and valid
        if(!isnull($roomID) && !$this->validate->integer($roomID)){
            throw new Exception("Invalid ID provided.");
        }

        $sql = "SELECT * FROM `reservePermissions` WHERE roomID = ? AND resourceType = 3";
        $sqlResult = $this->db->query($sql, array($roomID));

        if ($sqlResult->error()){
            throw new Exception("ERROR SQL" . $sqlResult->errorMsg());
        }

        //check and see if permissions exist on the resource
        if ($sqlResult->rowCount() < 1) {
           return TRUE;
        }

        return FALSE;

    } catch (Exception $e){
        errorHandle::errorMsg(__METHOD__."() - ".$e->getMessage, errorHandle::DEBUG);
        return false;
    }
  }

  public function checkBuildingPermissions($buildingID = null, $email = null){
    try {
        // test to see if Id is present and valid
        if(!isnull($buildingID) && !$this->validate->integer($buildingID)){
            throw new Exception("Invalid ID provided.");
        }

        // test to see if Id is present and valid
        if(!isnull($email) && !$this->validate->emailAddr($email)){
            throw new Exception("Invalid Email provided.");
        }

        $sql = "SELECT * FROM `reservePermissions` WHERE resourceID = ? AND email = ? LIMIT 1";

        // get the results of the query
        $sqlResult = $this->db->query($sql, array($buildingID, $email));

        if ($sqlResult->error()){
            throw new Exception("ERROR SQL" . $sqlResult->errorMsg());
        }

        //check and see if permissions exist on the resource
        if ($sqlResult->rowCount() < 1) {
           return TRUE;
        }

        return FALSE;

    } catch (Exception $e){
        errorHandle::errorMsg(__METHOD__."() - ".$e->getMessage, errorHandle::DEBUG);
        return false;
    }
  }

  public function checkRoomPermissions($roomID = null, $email = null){
    try {
        // test to see if Id is present and valid
        if(!isnull($roomID) && !$this->validate->integer($roomID)){
            throw new Exception("Invalid ID provided.");
        }

        // test to see if Id is present and valid
        if(!isnull($email) && !$this->validate->emailAddr($email)){
            throw new Exception("Invalid Email provided.");
        }

        $sql = "SELECT * FROM `reservePermissions` WHERE roomID = ? AND email = ? LIMIT 1";

        // get the results of the query
        $sqlResult = $this->db->query($sql, array($roomID, $email));

        if ($sqlResult->error()){
            throw new Exception("ERROR SQL" . $sqlResult->errorMsg());
        }

        //check and see if permissions exist on the resource
        if ($sqlResult->rowCount() < 1) {
           return TRUE;
        }

        return FALSE;

    } catch (Exception $e){
        errorHandle::errorMsg(__METHOD__."() - ".$e->getMessage, errorHandle::DEBUG);
        return false;
    }
  }

  /**
   * Function permissionsCheck
   * Name : Tracy McCormick
   * Date : 12/20/2016
   * Description
   * Function requires Building ID, Email Address and Room ID
   * It will verify that the email address is allowed to reserve the room.
   * Returns false if they are not allowed true is allowed.
   */
  public function permissionsCheck($buildingID, $email, $roomID){
    try {
        //check if there are any permissions currently in place on current buildingID
        if ($this->permissionsSet($buildingID)) {
            if(isset($email) && $this->checkBuildingPermissions($buildingID, $email)) {
              errorHandle::errorMsg(" Error email address not on Permissions list for this Room ");
              return FALSE;
            }

          }
        else {
              if ($this->checkRoom($roomID) && (isset($email) && $this->checkRoomPermissions($roomID, $email))) {
                errorHandle::errorMsg(" Error email address not on Permissions list for this Room");
                return FALSE;
              }
          }

        return TRUE;

     } catch (Exception $e) {
         errorHandle::errorMsg($e->getMessage());
         return FALSE;
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

  public static function duplicatePermissionsCheck($id, $type, $room, $email) {
    /**
     * Function duplicatePermissionsCheck
     * Name : Tracy McCormick
     * Description
     * Function performs a delete on all records passed from the form
     */
    $engine    = EngineAPI::singleton();
    $localvars = localvars::getInstance();
    $db        = db::get($localvars->get('dbConnectionName'));

    // checks to make sure record doesn't already exist
    $sql = "SELECT * FROM `reservePermissions` WHERE resourceID = ? AND resourceType = ? AND email = ?";

    if(!isnull($room)){
      $sql .= " AND roomID = ?";
      // get the results of the query
      $sqlResult = $db->query($sql, array($id, $type, $email, $room));
    } else {
      $sqlResult = $db->query($sql, array($id, $type, $email));
    }

    if ($sqlResult->error()) {
      errorHandle::newError(__FUNCTION__."() - ".$sqlResult->errorMsg(), errorHandle::DEBUG);
      return TRUE;
    }
    else {
      if ($sqlResult->rowCount() > 0) {
        return TRUE;
      }
    }
    return FALSE;
  }

  public function insertRecord($id, $type, $room, $email){

    try {
        //test to see if Id is present and valid
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

        if (self::duplicatePermissionsCheck($id, $type, $room, $email) === FALSE) {
          $sql = "INSERT INTO `reservePermissions` (resourceID, resourceType, roomID, email) VALUES (?, ?, ?, ?)";

          // get the results of the query
          $sqlResult = $this->db->query($sql, array($id, $type, $room, $email));

          if ($sqlResult->error()) {
              throw new Exception("ERROR SQL" . $sqlResult->errorMsg());
          }
        }

        return true;

    } catch (Exception $e) {
        errorHandle::errorMsg(__METHOD__."() - ".$e->getMessage, errorHandle::DEBUG);
        return false;
    }
  }

  public function setResourceType($data){
    switch ($data['resourceType']) {
       case 0:
           //get building record
           $building = $this->getBuildings($data['resourceID']);
           $name = $building[0]['name'];
           $type = "Building";
           break;
       case 1:
           $type = "Policy";
           break;
       case 2:
           $type = "Template";
           break;
       case 3:
          //get Room record
           $room = $this->getRoom($data['roomID']);
           $building = $this->getBuildings($data['resourceID']);
           $name = $building[0]['name'] . ' - ' . $room[0]['name'] . ' - ' . $room[0]['number'];
           $type = "Room";
           break;
       default:
           $name = "";
           $type = "";
    }

    return array(
      "name" => $name,
      "type" => $type
    );
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

        // error checking.
        if($file === false) {
           throw new Exception("Error opening " . $_FILES['uploadedfile']['tmp_name']);
        }

        // use class with csv data
        while(!feof($file)){
         $temp = fgetcsv($file);
         if (!isnull($temp[0])){
           $this->insertRecord($resourceID, $resourceType, $roomID, $temp[0]);
         }
        }
        fclose($file);
    }
    catch(Exception $e) {
      errorHandle::errorMsg($e->getMessage());
      return false;
    }
  }

  /**
   * Function multiDelete
   * Name : Tracy McCormick
   * Description
   * Function performs a delete on all records passed from the form
   */
  public function multiDelete($items = null){
    try{
  		foreach ($items as $reservationID){
  			$this->deleteRecord($reservationID);
  		}
    }
    catch (Exception $e){
    	errorHandle::errorMsg($e->getMessage());
      return false;
    }
  }

}
?>
