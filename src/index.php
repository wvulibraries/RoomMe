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

	<!-- Room Reservation Header -->
	<div class="roomReservation">
	    <div class="wrap">
	        <h2>Room Reservations</h2>
        		<?php if (is_empty(session::get("username"))) { ?>
        			<a class="userLogin roomTabletDesktop" href="{local var="loginURL"}">
        				<i class="fa fa-user"></i>
        				User Login
        			</a>
				<?php } else { ?>
					<a class="userLogin roomTabletDesktop" href="{engine var="logoutPage"}?csrf={engine name="csrfGet"}">
						<i class="fa fa-user"></i>User Logout
					</a>				
					<a class="userLogin roomTabletDesktop" href="{local var="roomReservationHome"}/calendar/user/" class="roomMobile bSubmit">
						<i class="fa fa-check"></i>My Reservations
					</a>
				<?php } ?>
	    </div>            
	</div>

	<!-- CMS Backpage Wrapper -->
	<div class="wrap hpcard">
		<section class="bp-body-1c">

			<!-- Reservations Section -->
			<a id="nowSubmit" class="roomMobile bSubmit">
				<i class="fa fa-arrow-circle-o-down"></i> Available Now
			</a>
			<h3 class="roomH3 roomTabletDesktop">Reservations for <span class="currentDay">{date format="l, F j"}</span></h3>
			<h3 class="roomH3 roomMobile">Make A Reservation</h3>
			<a class="policyLink roomTabletDesktop" href="#">Reservation Policies 
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

		<!-- Calendar Call -->
		<table id="reservationsRoomTable" cellspacing="0" cellpadding="0">
			<thead>
				<tr id="reservationsRoomTableHeaderRow">
					<td class="tdHours tdEmpty">&nbsp;</td>				
				</tr>
			</thead>
			<tbody id="reservationsRoomTableBody">

			</tbody>
		</table>

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