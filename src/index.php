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

$localvars->set("headerDate",date("l, F j"));

$localvars->set("future_date_list",future_date_list(time()));
$localvars->set("available_now",available_now((isset($_GET['MYSQL']['time']))?$_GET['MYSQL']['time']:time(),"2"));
$localvars->set("now_text",(isset($_GET['MYSQL']['time']))?date("l, M j",$_GET['MYSQL']['time']):"Now");

templates::display('header');
?>

	<!-- Reservations Section -->
	<h3 class="roomH3 roomMobile" style="margin-top: 20px;">Available {local var="now_text"}</h3>
	<hr class="roomHR roomMobile" />
	<ul id="mobileList" class="roomMobile">{local var="available_now"}</ul>

	<h3 class="roomH3 roomTabletDesktop">Reservations for <span class="currentDay" id="headerDate">{local var="headerDate}</span></h3>

	<!-- Extra Links -->
	<a class="policyLink roomTabletDesktop" href="{local var="advancedSearch"}">Advanced Search <span class="fa fa-cog"></span></a>
	<a class="policyLink3 roomTabletDesktop" href="{local var="policiesPage"}">Reservation Policies
		<span class="fa fa-exclamation-circle"></span>
	</a>
	<a class="policyLink roomTabletDesktop" href="{local var="helpPage"}">Help
		<span class="fa fa-question-circle"></span>
	</a>

	<hr class="roomHR roomTabletDesktop" />
	<div class="styled-select roomTabletDesktop">
		<select id="building_modal">
			{local var="buildingSelectOptions"}
		</select>
	</div>
	<div class="styled-select roomTabletDesktop">
		{local var="monthSelect"}
	</div>
	<div class="styled-select roomTabletDesktop">
		{local var="daySelect"}
	</div>
	<div class="styled-select roomTabletDesktop">
		{local var="yearSelect"}
	</div>
	<a id="calUpdateFormSubmit" class="bSubmit roomTabletDesktop button_inactive">
		<span class="fa fa-calendar"></span> Find A Room
	</a>
	<div style="clear:both;"></div>
	<h3 class="roomH3 roomMobile" style="margin-top: 40px;">Future Dates</h3>
	<hr class="roomHR roomMobile" />

	{local var="future_date_list"}

	<a href="{local var="advancedSearch"}" id="asbutton" class="bSubmit roomMobile"><span class="fa fa-cog"></span> Advanced Search</a>

	<!-- Table Pager -->
	<div class="tablePager roomTabletDesktop">
		<a class="pagerButtons"><span id="pagerFirst" data-startCols="0" data-endCols="7" class="fa fa-step-backward pagerLink"></span></a>
		<a class="pagerButtons"><span id="pagerPrev"  data-startCols="" data-endCols="" class="fa fa-backward pagerLink"></span></a>
		<a class="pagerButtons"><span id="pagerNext"  data-startCols="7" data-endCols="14"class="fa fa-forward pagerLink"></span></a>
		<a class="pagerButtons"><span id="pagerLast"  data-startCols="" data-endCols="" class="fa fa-step-forward pagerLink" ></span></a>
	</div>

	<!-- Calendar Call -->
	<div style="clear: both;"></div>
	<img id="imageLoader" src="{local var="roomResBaseDir"}/images/loading.gif" alt="decorative loading gif" role="spinner"/>
	<div style="clear: both;"></div>
	<table id="reservationsRoomTable" cellspacing="0" cellpadding="0">
		<caption class="hidden-text"> Table used for Reserving Rooms by Time </caption> 
		<thead>
			<tr id="reservationsRoomTableHeaderRow">
			</tr>
		</thead>
		<tbody id="reservationsRoomTableBody">

		</tbody>
	</table>

	<div class="clear:both;"></div>
	<br>

	<!-- Rooms NAvigation -->
	<?php recurseInsert("includes/roomsByBuilding.php","php") ?>



<?php templates::display('footer'); ?>
