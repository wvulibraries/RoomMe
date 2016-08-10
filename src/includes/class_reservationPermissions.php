<?php
class reservatoinPermissions {

  private $db;
  private $engine;
  private $localvars;

  function __construct() {
    $this->engine    = EngineAPI::singleton();
    $this->localvars = localvars::getInstance();
    $this->db        = db       ::get($this->localvars->get('dbConnectionName'));
  }

  // Data can be an array or string
  // if string, it assumes file path and loads the file into an array
  public function import($data,$resourceID,$resourceType) {

    if (!is_string($data) && is_readable($data)) {
      if (($data = file($data, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)) === FALSE) {
        return false;
      }
    }

    foreach ($data as $username) {
      $sql       = "INSERT INTO `reservePermissions` (`username`,`resourceType`,`resourceID`) VALUES('%s','%s','%s')";
      $sqlResult = $this->db->query($sql,array($username,$resourceType,$resourceID));
    }

    if ($sqlResult->error()) {
      errorHandle::newError(__FUNCTION__."() - Error inserting permissions name.", errorHandle::DEBUG);
      return false;
    }

    return true;

  }

  // if both args are left null, truncate the table.
  public function delete($resourceID=NULL,$resourceType=NULL) {

    if ((isnull($resourceID)  && !isnull($resourceType)) ||
        (!isnull($resourceID) && isnull($resourceType))) {
          errorHandle::newError(__METHOD__."() - Null missmatch.", errorHandle::DEBUG);
      return FALSE;
    }
    else if (isnull($resourceID)  && isnull($resourceType)) {
      $sql            = "TRUNCATE TABLE `reservePermissions`";
      $prepared_array = array();
    }
    else {
      $sql            = "DELETE FROM TABLE `reservePermissions` WHERE `resourceType` =? AND `resourceID` =?";
      $prepared_array = array($resourceType,$resourceID);
    }

    $sqlResult = $this->db->query($sql,$prepared_array);

    if ($sqlResult->error()) {
      errorHandle::newError(__METHOD__."() - Error deleting from permissions table. ", errorHandle::DEBUG);
      return false;
    }

    return true;
  }

  // returns true if a resource will require permissions
  // this function is NOT recursive. If you give it a room, it will not check
  // policy, temmplate, building. It will only check the room.
  // for recursive, use has_permissions_chain
  public function has_permissions($resourceID,$resourceType) {

    $sql       = "SELECT ID FROM `reservePermissions` WHERE `resourceType`=? AND `resourceID`=?";
    $sqlResult = $db->query($sql,array($resourceType,$resourceID));

    if ($sqlResult->error()) {
      errorHandle::newError(__METHOD__."() - ".$sqlResult->errorMsg(), errorHandle::DEBUG);
    	return false;
    }

    if ($sqlResult->rowCount() >= 1) {
      return true;
    }

    return true;

  }

  // Given a resource type, will return an array if any of the elements "above"
  // it in the heirarchy require a specific username.
  public function has_permissions_chain($resourceID,$resourceType) {

    $chain = array(ROOM_RESOURCE_TYPE, TEMPLATE_RESOURCE_TYPE, POLICY_RESOURCE_TYPE, BUILDING_RESOURCE_TYPE);

    foreach ($chain as $chain_resourceType) {

      if ($resourceType == $chain_resourceType) $found = true;
      if (!$found) continue;

      switch($chain_resourceType) {
        case ROOM_RESOURCE_TYPE && $resourceType == ROOM_RESOURCE_TYPE:
          $chain_resourceID = $resourceID;
          break;

        case TEMPLATE_RESOURCE_TYPE:
          $chain_resourceID = ($resourceType == TEMPLATE_RESOURCE_TYPE)?$resourceID:get_template_id(); // function not written. Can probably just get it from the room directly
          break;

        case POLICY_RESOURCE_TYPE:
          $chain_resourceID = ($resourceType == POLICY_RESOURCE_TYPE)?$resourceID:get_policy_id(); // function not written. Can probably just get it from the room or template directly
          break;

        case BUILDING_RESOURCE_TYPE:
          $chain_resourceID = ($resourceType == BUILDING_RESOURCE_TYPE)?$resourceID:get_building_id(); // get directly from room. no need for function.
        break;

        default:
          return array("error" => "invalid resource type.");
          break;
      }

      if (($return = $this->has_permissions($chain_resourceType,$chain_resourceID)) === TRUE) {
        return array("resourceType" => $chain_resourceType, "resourceID" => $chain_resourceID);
      }

    }

    return false;

  }

}
?>
