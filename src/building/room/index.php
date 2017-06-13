<?php
require_once("../../engineHeader.php");
recurseInsert("includes/functions.php","php");
recurseInsert("includes/createReservations.php","php");

$snippet = new Snippet("pageContent","content");

$roomClosed = FALSE;
$error      = FALSE;
$roomID = "";
if (!isset($_GET['MYSQL']['room'])) {
  $error = TRUE;
  errorHandle::errorMsg("Invalid or missing room ID");
}
else {
  $roomID = $_GET['MYSQL']['room'];
}

$room         = getRoomInfo($roomID);

if ($room !== FALSE && isset($room['building']) && $room['publicViewing'] == '1') {

  $roomObj      = new room;
  $roomPolicy   = getRoomPolicy($roomID);
  $buildingName = getBuildingName($room['building']);

  // is the room closed?
  if ($roomPolicy['roomsClosed'] == '1' || $room['roomClosed'] == "1") {
    $roomClosed        = TRUE;
    $localvars->set("roomClosedMessage",(!isnull($roomPolicy['roomsClosedSnippet']) && $roomPolicy['roomsClosedSnippet'] > 0)?$roomPolicy['roomsClosedSnippet']:getResultMessage("roomClosed"));
  }

  $userinfo = new userInfo();
  if ($userinfo->get(session::get("username"))) {
    $localvars->set("useremail",   $userinfo->user['email']);
  }

  $localvars->set("roomName",    htmlSanitize($room['name']));
  $localvars->set("roomNumber",  htmlSanitize($room['number']));
  $localvars->set("policyURL",   htmlSanitize($room['policyURL']));
  $localvars->set("username",    session::get("username"));
  $localvars->set("buildingID",  htmlSanitize($room['building']));
  $localvars->set("roomID",      htmlSanitize($room['ID']));
  $localvars->set("buildingName",htmlSanitize($buildingName));
  $localvars->set("prettyPrint", errorHandle::prettyPrint());
  $localvars->set("loginURL",    $engineVars['loginPage'].'?page='.$_SERVER['PHP_SELF']."&qs=".(urlencode($_SERVER['QUERY_STRING'])));
  $localvars->set("mapURL",      htmlSanitize($room['mapURL']));
  $localvars->set("displayName", htmlSanitize($room['displayName']));
  $localvars->set("capacity",    htmlSanitize($room['capacity']));

  $localvars->set("roomPicture", $roomObj->getPicture($room['ID']));

}
else {

  $roomPolicy   = NULL;
  $buildingName = NULL;

  $localvars->set("roomName",    "Error");
  $localvars->set("roomNumber",  "Error");
  $localvars->set("policyURL",   "Error");
  $localvars->set("username",    session::get("username"));
  $localvars->set("buildingID",  "Error");
  $localvars->set("roomID",      "Error");
  $localvars->set("buildingName","Error");
  $localvars->set("prettyPrint", errorHandle::prettyPrint());
  $localvars->set("loginURL",    $engineVars['loginPage'].'?page='.$_SERVER['PHP_SELF']."&qs=".(urlencode($_SERVER['QUERY_STRING'])));
  $localvars->set("mapURL",      "Error");
  $localvars->set("displayName", "Error");

}

$currentMonth = (!isset($_GET['MYSQL']['reservationSTime']))?date("n"):date("n",$_GET['MYSQL']['reservationSTime']);
$currentDay   = (!isset($_GET['MYSQL']['reservationSTime']))?date("j"):date("j",$_GET['MYSQL']['reservationSTime']);
$currentYear  = (!isset($_GET['MYSQL']['reservationSTime']))?date("Y"):date("Y",$_GET['MYSQL']['reservationSTime']);
$currentHour  = (!isset($_GET['MYSQL']['reservationSTime']))?date("G"):date("G",$_GET['MYSQL']['reservationSTime']);
$currentMin   = (!isset($_GET['MYSQL']['reservationSTime']))?"00":date("i",$_GET['MYSQL']['reservationSTime']);
$nextHour     = (!isset($_GET['MYSQL']['reservationETime']))?(date("G")+1):date("G",$_GET['MYSQL']['reservationETime']);
$nextMin      = (!isset($_GET['MYSQL']['reservationSTime']))?"00":date("i",$_GET['MYSQL']['reservationSTime']);

$sql        = sprintf("SELECT value FROM siteConfig WHERE name='24hour'");
$sqlResult  = $db->query($sql);

$displayHour = 24;
if (!$sqlResult->error()) {
  $row        = $sqlResult->fetch();
  $displayHour = ($row['value'] == 1)?24:12;
}

if (isset($_POST['MYSQL']['createSubmit'])) {

  $buildingID = $_POST['MYSQL']['library'];
  $roomID     = $_POST['MYSQL']['room'];

  $reservation = new Reservation;
  $reservation->setBuilding($buildingID);
  $reservation->setRoom($roomID);

  $reservation->create();

  $localvars->set("prettyPrint",errorHandle::prettyPrint());
}

$localvars->set("policyLabel",htmlSanitize(getResultMessage("policyLabel")));

$date = new date;

// @TODO display on month dropdown should be configurable via interface
$localvars->set("monthSelect", $date->dropdownMonthSelect(1,$currentMonth,array("name"=>"start_month", "id"=>"start_month")));
$localvars->set("daySelect",   $date->dropdownDaySelect($currentDay,array("name"=>"start_day", "id"=>"start_day")));
$localvars->set("yearSelect",  $date->dropdownYearSelect(0,10,$currentYear,array("name"=>"start_year", "id"=>"start_year")));
$localvars->set("shourSelect", $date->dropdownHourSelect(($displayHour == 12)?TRUE:FALSE,$currentHour,array("name"=>"start_hour", "id"=>"start_hour")));
$localvars->set("sminSelect",  $date->dropdownMinuteSelect("15",0,array("name"=>"start_minute", "id"=>"start_minute"))); // @TODO need to pull increment from room config
$localvars->set("ehourSelect", dropdownDurationSelect(1,array("name"=>"end_hour", "id"=>"end_hour"),$roomPolicy['hoursAllowed']));
$localvars->set("eminSelect",  $date->dropdownMinuteSelect("15",0,array("name"=>"end_minute", "id"=>"end_minute"))); // @TODO need to pull increment from room config

$localvars->set("monthSelect_modal", $date->dropdownMonthSelect(1,$currentMonth,array("id"=>"start_month_modal")));
$localvars->set("daySelect_modal",   $date->dropdownDaySelect($currentDay,array("id"=>"start_day_modal")));
$localvars->set("yearSelect_modal",  $date->dropdownYearSelect(0,10,$currentYear,array("id"=>"start_year_modal")));

templates::display('header');
?>

  {local var="prettyPrint"}

<h3 class="roomH3" style="display: inline-block;">
<?php if ($room['publicViewing'] == '1') { ?>
  {local var="displayName"} in {local var="buildingName"}
<?php } else { ?>
  Room Not Found
<?php } ?>

</h3>

<!-- Extra Links -->
<a class="policyLink roomTabletDesktop" href="{local var="advancedSearch"}">Advanced Search <i class="fa fa-cog"></i></a>
<a class="policyLink3 roomTabletDesktop" href="{local var="policiesPage"}">Reservation Policies
  <i class="fa fa-exclamation-circle"></i>
</a>
<a class="policyLink roomTabletDesktop" href="{local var="helpPage"}">Help
  <i class="fa fa-question-circle"></i>
</a>
<hr class="roomHR roomTabletDesktop" />

<!-- Room Information -->
<?php if ($room['publicViewing'] == '1') { ?>
<section id="reservationsRoomInformation">
  <h4>Room Information</h4>
  <hr class="roomHR" />
  <table id="roomInformationTable">
    <tr>
      <td><strong>Room Name:</strong></td>
      <td>{local var="roomName"}</td>
    </tr>
    <tr>
      <td><strong>Room Number:</strong></td>
      <td>{local var="roomNumber"}</td>
    </tr>
    <tr>
      <td><strong>Building:</strong></td>
      <td>{local var="buildingName"}</td>
    </tr>

    <?php if (isset($room['capacity']) && !is_empty($room['capacity'])) { ?>
    <tr>
      <td><strong>Capacity:</strong></td>
      <td>{local var="capacity"}</td>
    </tr>
    <?php } ?>

    <?php if (isset($room['policyURL']) && !is_empty($room['policyURL'])) { ?>
    <tr>
      <td><strong>{local var="policyLabel"} Information:</strong></td>
      <td><a href="{local var="policyURL"}">View Policies</a></td>
    </tr>
    <?php } ?>

    <?php if (count($room['equipment']) > 0) { ?>
    <tr>
      <td><strong>Equipment:</strong></td>
      <td>
        <ul>
          <?php
            foreach ($room['equipment'] as $I=>$equipment) {
          ?>
            <li>
              <a href="equipment/?id=<?php print htmlSanitize($equipment['ID']); ?>"><?php print htmlSanitize($equipment['name']); ?></a>
            </li>
          <?php } ?>
        </ul>
      </td>
    </tr>
    <?php } ?>
  </table>
  <div id="roomPictureContainer">
    <h4>Room Picture</h4>
    {local var="roomPicture"}
  </div>
</section>

<!-- Reserve Room -->
<section id="reservationsReserveRoom">
  <h4>Reserve Room</h4>
  <hr class="roomHR" />
  <?php if ($roomClosed) { ?>

  <div id="closedMessageContainer">
    <?php if (is_numeric($localvars->get("roomClosedMessage"))) { ?>
    {snippet id="{local var="roomClosedMessage"}" field="content"}
    <?php } else { ?>
    <p id="genericClosedMessage">{local var="roomClosedMessage"}</p>
    <?php } ?>
  </div>

  <?php } else if(isset($roomPolicy['publicScheduling']) && $roomPolicy['publicScheduling']=="1") { // public scheduling?>

  <?php if(is_empty(session::get("username"))) { ?>

  <p>You must be logged in to reserve a room. </p>
  <a href="{local var="loginURL"}">Login</a>

  <?php }
    $reservationPermissions = new reservationPermissions;
    $check = $reservationPermissions->permissionsCheck($room['building'], $userinfo->user['email'],
    $userinfo->user['username'], $roomID);
    if (!$check) {
  ?>

  <p> You are unable to reserve this room due to restrictions that have been set. </p>

  <?php } else { ?>

  <form action="{phpself query="true"}" method="post">
    {csrf}

    <input type="hidden" name="library" value="{local var="buildingID"}" />
    <input type="hidden" name="room" value="{local var="roomID"}" />
    <input type="hidden" id="username" name="username" value="{local var="username"}"/>

    <strong>Select The Date:</strong>
    <div class="roomReservationRows">
      <span class="reserveRoomInput"><label for="start_month">Month:</label>
      {local var="monthSelect"}</span>
      <span class="reserveRoomInput"><label for="start_day">Day:</label>
      {local var="daySelect"}</span>
      <span class="reserveRoomInput"><label for="start_year">Year:</label>
      {local var="yearSelect"}</span>
    </div>
    <strong>Select The Start Time:</strong>
    <div class="roomReservationRows">
      <span class="reserveRoomInput"><label for="start_hour">Hour:</label>
      {local var="shourSelect"}</span>
      <span class="reserveRoomInput"><label for="start_minute">Minute:</label>
      {local var="sminSelect"}</span>
    </div>
    <strong>Select The Duration:</strong>
    <div class="roomReservationRows">
      <span class="reserveRoomInput"><label for="end_hour">Hour:</label>
      {local var="ehourSelect"}</span>
      <span class="reserveRoomInput"><label for="end_minute">Minute:</label>
      {local var="eminSelect"}</span>
    </div>
    <strong>Provide Additional Information:</strong>
    <div class="roomReservationRows">
      <?php if (getConfig('showOpenEvent')) { ?>
      <span class="reserveRoomInput"><label for="openEvent">Is this an open, public, event?</label>
      <select name="openEvent" id="openEvent">
        <option value="0">No</option>
        <option value="1">Yes</option>
      </select></span>

      <br />
      <label for="openEventDescription" class="openEventDescription" style="display:none;">Describe your event:</label>
      <textarea id="openEventDescription" name="openEventDescription"  class="openEventDescription" rows="5" style="display:none;"></textarea>

      <br><br>
      <?php } ?>

      <label name="notificationEmail" class="requiredField" >Email Address:</label>
      <input type="email" name="notificationEmail" id="notificationEmail" placeholder="" value="{local var="useremail"}" required />
    </div>
    <input id="nowSubmit" type="submit" name="createSubmit" value="Reserve this Room" />
  </form>

  <?php } ?>
  <?php } else { // public scheduling?>

    {snippet id="8" field="content"}

  <?php } ?>
</section>

<div style="clear:both;"</div>

<!-- Room Availability -->
<?php if (!$roomClosed) { ?>
<section class="roomAvailability">
    <br>
    <br>
    <h4>Room Availability</h4>
    <hr class="roomHR" />

    <input type="hidden" id="building_modal" value="{local var="buildingID"}" />
    <input type="hidden" id="room_modal" value="{local var="roomID"}" />

    <div class="styled-select">
      {local var="monthSelect_modal"}
    </div>
    <div class="styled-select">
      {local var="daySelect_modal"}
    </div>
    <div class="styled-select">
      {local var="yearSelect_modal"}
    </div>
    <a id="calUpdateFormSubmit" class="bSubmit">
      <i class="fa fa-calendar"></i> Change Date
    </a>

    <div style="clear:both"></div>

    <br>
    <br>

    <table id="reservationsRoomTable" class="iroomTable" cellspacing="0" cellpadding="0">
      <thead>
        <tr id="reservationsRoomTableHeaderRow">
        </tr>
      </thead>
      <tbody id="reservationsRoomTableBody">

      </tbody>
    </table>
</section>
<?php } ?>

<!-- Advanced Search -->
<div style="clear:both;"></div>
<hr class="roomHR roomMobile" />
<a href="{local var="advancedSearch"}" id="asbutton" class="bSubmit roomMobile"><i class="fa fa-cog"></i> Advanced Search</a>

<?php } // if room is publically viewable ?>

<div class="clear:both;"></div>
<br>

<!-- Rooms Navigation -->
<?php recurseInsert("includes/roomsByBuilding.php","php") ?>

<!-- Mobile UI -->
<a class="policyLink roomMobile" href="{local var="policiesPage"}">Reservation Policies <i class="fa fa-exclamation-circle"></i></a>

<?php if (is_empty(session::get("username"))) { ?>
  <a id="userLoginSubmit" href="{local var="loginURL"}" class="roomMobile bSubmit">
    <i class="fa fa-user"></i> User Login
  </a>
<?php } else { ?>
  <a id="userLoginSubmit" href="{local var="roomReservationHome"}/calendar/user/" class="roomMobile bSubmit">
    <i class="fa fa-check"></i> My Reservations
  </a>
  <a id="userLoginSubmit" href="{engine var="logoutPage"}" class="roomMobile bSubmit">
    <i class="fa fa-user"></i> User Logout
  </a>
<?php } ?>

<?php
templates::display('footer');
?>
