<?php
  $resData     =  new reservationPermissions;
  $permissions = $resData->getRecords();
  $records     = "";

  foreach($permissions as $data){
      $types    = $resData->setResourceType($data);
      $records .= sprintf("<tr>
                              <td>%s</td>
                              <td>%s</td>
                              <td>%s</td>
                              <td>%s</td>
                              <td><a href='../create/?id=%s&building=%s'>Edit</a></td>
                              <td><input type='checkbox' class='delete_restrictions' name='delete[]' value='%s' /></td>
                            </tr>",
              htmlSanitize($data['username']),
              htmlSanitize($data['email']),
              htmlSanitize($types['type']),
              htmlSanitize($types['name']),
              htmlSanitize($data['ID']),
              htmlSanitize($data['resourceID']),
              htmlSanitize($data['ID'])
      );
  }

  $localvars->set('dataTable', $records);
?>


<form action='{phpself query='true'}' method='post' onsubmit=\"return confirm('Confirm Deletes');\">
   {csrf}
   <div class='dataTable table-responsive'>
      <table class='table table-striped'>
          <thead>
              <tr class='info'>
                  <th> Username </th>
                  <th> Email </th>
                  <th> Resource Type </th>
                  <th> Resource ID </th>
                  <th> Edit </th>
                  <th> Delete </th>
              </tr>
          </thead>
          <tbody>
            {local var="dataTable"}
          </tbody>
      </table>
  </div>

  <a href="javascript:void(0)" id="check_all" class="button btn"> Select All  </a>
  <a href="javascript:void(0)" id="uncheck_all" class="button btn"> Clear All </a>
  <input type='submit'  name='multiDelete' class="button btn" value='Delete Selected Reserve Permissions' />
</form>

<script>
  $('#check_all').click(function(){
    $('.delete_restrictions').prop('checked', true);
  });

  $('#uncheck_all').click(function(){
    $('.delete_restrictions').prop('checked', false);
  });
</script>
