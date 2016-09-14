<?php
class reservationPermissions {

  //public $reservationPermissions = NULL;

  public function getRecords($id = null){
      try {
          // call engine
          $engine    = EngineAPI::singleton();
          $localvars = localvars::getInstance();
          $db        = db::get($localvars->get('dbConnectionName'));
          $sql       = "SELECT * FROM `reservePermissions`";
          $validate  = new validate;
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
  public function setupForm($id = null){
       try {
          // call engine
          $engine    = EngineAPI::singleton();
          $localvars = localvars::getInstance();
          $validate  = new validate;
          // create customer form
          $form = formBuilder::createForm('createPermissions');
          $form->linkToDatabase( array(
              'table' => 'reservePermissions'
          ));
          // if(!is_empty($_POST) || session::has('POST')) {
          //     $processor = formBuilder::createProcessor();
          //     $processor->processPost();
          // }
          // form titles
          $form->insertTitle = "Add Permissions";
          $form->editTitle   = "Edit Permissions";
          $form->updateTitle = "Edit Permissions";
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
              'required' => TRUE
          ));
          $form->addField(array(
              'name'     => 'resourceType',
              'label'    => 'Resource Type:',
              'required' => TRUE
          ));
          $form->addField(array(
              'name'     => 'username',
              'label'    => 'Username:',
              'required' => TRUE
          ));
          // buttons and submissions
          $form->addField(array(
              'showIn'     => array(formBuilder::TYPE_INSERT),
              'name'       => 'insert',
              'type'       => 'submit',
              'fieldClass' => 'submit',
              'value'      => $localvars->get('submitText')
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
public function renderDeleteData($id){
  try {
      $engine    = EngineAPI::singleton();
      $localvars = localvars::getInstance();
      $validate  = new validate;
      if(isnull($id) || !$validate->integer($id)){
          throw new Exception('Id is null or not an integer.  Please try again.');
      }
      else {
          $dataRecord = self::getRecords($id);
          $output = "";
          foreach($dataRecord as $data){
               $output .= sprintf("<div class='permissionsRecord'>
                                      <h2 class='permissions'>%s</h2>
                                      <div class='name'>
                                          <strong>Customer Name:</strong>
                                          %s
                                      </div>
                                      <div class='contactInfo'>
                                          <div class='email'>%s</div>
                                          <div class='phone'>%s</div>
                                          <div class='website'><a href='%s'>%s</a></div>
                                      </div>
                                      <div class='actions'>
                                          <a href='/customers/delete/%s'> <span class='glyphicon glyphicon-ok'></span> </a>
                                          <a href='/customers'> <span class='glyphicon glyphicon-remove'></span> </a>
                                      </div>
                                  </div>",
                      $data['companyName'],
                      $data['firstName']." ".$data['lastName'],
                      $data['email'],
                      $data['phone'],
                      $data['website'],
                      $data['website'],
                      $data['ID']
              );
          }
          return $output;
      }
  } catch (Exception $e) {
      errorHandle::errorMsg($e->getMessage());
      return $e->getMessage();
  }
}
public function renderSingleRecord($id){
  try {
      $engine    = EngineAPI::singleton();
      $localvars = localvars::getInstance();
      $validate  = new validate;
      if(isnull($id) || !$validate->integer($id)){
          throw new Exception('Id is null or not an integer.  Please try again.');
      }
      else {
          $dataRecord = self::getRecords($id);
          $output = "";
          foreach($dataRecord as $data){
              $output .= sprintf("<div class='customerRecord'>
                                      <h2 class='company'>%s</h2>
                                      <div class='name'>
                                          <strong>Customer Name:</strong>
                                          %s
                                      </div>
                                      <div class='contactInfo'>
                                          <div class='email'>%s</div>
                                          <div class='phone'>%s</div>
                                          <div class='website'><a href='%s'>%s</a></div>
                                      </div>
                                      <div class='actions'>
                                          <a href='/customers/edit/%s'> <span class='glyphicon glyphicon-edit'></span> </a>
                                          <a href='/customers/delete/%s'><span class='glyphicon glyphicon-trash'></span> </a>
                                      </div>
                                  </div>",
                      $data['companyName'],
                      $data['firstName']." ".$data['lastName'],
                      $data['email'],
                      $data['phone'],
                      $data['website'],
                      $data['website'],
                      $data['ID'],
                      $data['ID']
              );
          }
          return $output;
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
          $records .= sprintf("<tr>
                                  <td>%s</td>
                                  <td>%s</td>
                                  <td>%s</td>
                                  <td>%s</td>
                                  <td>%s</td>
                                  <td>%s</td>
                                  <td><a href='customers/edit/%s'><span class='glyphicon glyphicon-edit'></span> </a></td>
                                  <td><a href='customers/confirmDelete/%s'> <span class='glyphicon glyphicon-trash'></span> </a></td>
                              </tr>",
                  $data['companyName'],
                  $data['firstName'],
                  $data['lastName'],
                  $data['email'],
                  $data['phone'],
                  $data['website'],
                  $data['ID'],
                  $data['ID']
          );
      }
      $output     = sprintf("<div class='dataTable table-responsive'>
                                  <table class='table table-striped'>
                                      <thead>
                                          <tr class='info'>
                                              <th> Company Name </th>
                                              <th> First name </th>
                                              <th> Last Name </th>
                                              <th> Email </th>
                                              <th> Phone Number </th>
                                              <th> Website </th>
                                              <th> </th>
                                              <th> </th>
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
public function getJSON($id = null){
  $validate = new validate;
  if(!isnull($id) && $validate->integer($id)){
      $data = self::getRecords($id);
  } else {
      $data = self::getRecords();
  }
  return json_encode($data);
}

public function insert($username,$resourceID,$resourceType) {

  $sql       = "INSERT INTO `reservePermissions` (`username`,`resourceType`,`resourceID`) VALUES('%s','%s','%s')";
  $sqlResult = $this->db->query($sql,array($username,$resourceType,$resourceID));

  if ($sqlResult->error()) {
    errorHandle::newError(__FUNCTION__."() - Error inserting permissions name.", errorHandle::DEBUG);
    return false;
  }

  return true;

}

}
?>
