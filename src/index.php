<?php
require_once("engineHeader.php");
recurseInsert("includes/functions.php","php");

// $localVars = localVars::getInstance();
$db        = db::get($localvars->get('dbConnectionName'));

$sql       = sprintf("SELECT * FROM building ORDER BY name");
$sqlResult = $db->query($sql);

$localvars->set("policyLabel",$messages->get("policyLabel"));

$building = new building;
$localvars->set("buildingSelectOptions",$building->selectBuildingListOptions(FALSE,(isset($_POST['MYSQL']['library']))?$reservation->building['ID']:NULL));

$date = new date;
$localvars->set("monthSelect",$date->dropdownMonthSelect(1,TRUE,array("id"=>"start_month_modal")));
$localvars->set("daySelect",$date->dropdownDaySelect(TRUE,array("id"=>"start_day_modal")));
$localvars->set("yearSelect",$date->dropdownYearSelect(0,1,TRUE,array("id"=>"start_year_modal")));

templates::display('header'); 
?>

	<!-- Reservations Section -->
	<a id="nowSubmit" class="roomMobile bSubmit">
		<i class="fa fa-arrow-circle-o-down"></i> Available Now
	</a>
	<h3 class="roomH3 roomTabletDesktop">Reservations for <span class="currentDay">{date format="l, F j"}</span></h3>
	<h3 class="roomH3 roomMobile">Make A Reservation</h3>
	<a class="policyLink roomTabletDesktop" href="policies/">Reservation Policies 
		<i class="fa fa-exclamation-circle"></i>
	</a>
	<hr class="roomHR" />
	<div class="styled-select">
		<select id="building_modal">
			{local var="buildingSelectOptions"}             
		</select>
	</div>
	<div class="styled-select">
		{local var="monthSelect"}
	</div>                                          
	<div class="styled-select">
		{local var="daySelect"}
	</div>
	<div class="styled-select">
		{local var="yearSelect"} 
	</div>
	<a id="calUpdateFormSubmit" class="bSubmit">
		<i class="fa fa-calendar"></i> Find A Room
	</a>

	<!-- Table Pager -->
	<div class="tablePager roomTabletDesktop">
		<a class="pagerButtons"><i id="pagerFirst" data-startCols="0" data-endCols="7" class="fa fa-step-backward pagerLink"></i></a>
		<a class="pagerButtons"><i id="pagerPrev"  data-startCols="" data-endCols="" class="fa fa-backward pagerLink"></i></a>
		<a class="pagerButtons"><i id="pagerNext"  data-startCols="7" data-endCols="14"class="fa fa-forward pagerLink"></i></a>
		<a class="pagerButtons"><i id="pagerLast"  data-startCols="" data-endCols="" class="fa fa-step-forward pagerLink" ></i></a>
	</div>

	<!-- Calendar Call -->
	<ul id="mobileList">
	</ul>
	
	<table id="reservationsRoomTable" cellspacing="0" cellpadding="0">
		<thead>
			<tr id="reservationsRoomTableHeaderRow">			
			</tr>
		</thead>
		<tbody id="reservationsRoomTableBody">

		</tbody>
	</table>

	<div class="clear:both;"></div>
	<br>
	<h4>Rooms by Building:</h4>
	<hr class="roomHR"></hr>
	<nobr><a class="policyLink1" href="/services/rooms/building/?building=2"><i class="fa fa-building"></i>Downtown Campus Library</a></nobr>
	<nobr><a class="policyLink1" href="/services/rooms/building/?building=1"><i class="fa fa-building"></i>Evansdale Library</a></nobr>
	<nobr><a class="policyLink1" href="http://home.hsc.wvu.edu/its/forms/library-study-room-reservation/" target="_blank"><i class="fa fa-building"></i>Health Sciences Library</a></nobr>
	<hr class="roomHR"></hr>
	<br>

	<!-- Mobile UI -->			
	<a class="policyLink roomMobile" href="#">Reservation Policies <i class="fa fa-exclamation-circle"></i></a>

	<?php if (is_empty(session::get("username"))) { ?>
		<a id="userLoginSubmit" href="{local var="loginURL"}" class="roomMobile bSubmit">
			<i class="fa fa-user"></i> User Login
		</a>
	<?php } else { ?>
		<a id="userLoginSubmit" href="{local var="roomReservationHome"}/calendar/user/" class="roomMobile bSubmit">
			<i class="fa fa-check"></i> My Reservations
		</a>
		<a id="userLoginSubmit" href="{engine var="logoutPage"}?csrf={engine name="csrfGet"}" class="roomMobile bSubmit">
			<i class="fa fa-user"></i> User Logout
		</a>
	<?php } ?>

<?php templates::display('footer'); ?>