<?php
require_once("../../engineHeader.php");
recurseInsert("includes/functions.php","php");

// $localVars = localVars::getInstance();
$db        = db::get($localvars->get('dbConnectionName'));

$sql       = sprintf("SELECT * FROM building ORDER BY name");
$sqlResult = $db->query($sql);

$localvars->set("policyLabel",$messages->get("policyLabel"));

$building = new building;

if (isset($_POST['MYSQL']['library'])) {
	$buildingID = $reservation->building['ID'];
}
else if (isset($_GET['MYSQL']['building'])) {
	$buildingID = $_GET['MYSQL']['building'];
}
else {
	$buildingID = NULL;
}

$localvars->set("buildingSelectOptions",$building->selectBuildingListOptions(FALSE,$buildingID));
$localvars->set("buildingName",getBuildingName($buildingID));

$date = new date;
$localvars->set("monthSelect",$date->dropdownMonthSelect(1,TRUE,array("id"=>"start_month_modal", "class" => "date_select_modal")));
$localvars->set("daySelect",$date->dropdownDaySelect(TRUE,array("id"=>"start_day_modal", "class" => "date_select_modal")));
$localvars->set("yearSelect",$date->dropdownYearSelect(0,1,TRUE,array("id"=>"start_year_modal", "class" => "date_select_modal")));

$localvars->set("headerDate",date("l, F j"));

templates::display('header');
?>

	<!-- Reservations Section -->
	<h3 class="roomH3 roomMobile" style="margin-top: 20px;">Available Now</h3>
	<hr class="roomHR roomMobile" />
	<ul id="mobileList" class="roomMobile"></ul>

	<h3 class="roomH3 roomTabletDesktop">Calendar for {local var="buildingName"} on <span class="currentDay" id="headerDate">{local var="headerDate}</span></h3>

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
	<a id="calUpdateFormSubmit" class="bSubmit roomTabletDesktop">
		<span class="fa fa-calendar"></span> Find A Room
	</a>
	<div style="clear:both;"></div>
	<h3 class="roomH3 roomMobile" style="margin-top: 40px;">Future Dates</h3>
	<hr class="roomHR roomMobile" />
	<a href="{local var="advancedSearch"}" id="asbutton" class="bSubmit roomMobile"><span class="fa fa-cog"></span> Advanced Search</a>

	<!-- Table Pager -->
	<div class="tablePager roomTabletDesktop">
		<a class="pagerButtons"><span id="pagerFirst" data-startCols="0" data-endCols="7" class="fa fa-step-backward pagerLink"></span></a>
		<a class="pagerButtons"><span id="pagerPrev"  data-startCols="" data-endCols="" class="fa fa-backward pagerLink"></span></a>
		<a class="pagerButtons"><span id="pagerNext"  data-startCols="7" data-endCols="14"class="fa fa-forward pagerLink"></span></a>
		<a class="pagerButtons"><span id="pagerLast"  data-startCols="" data-endCols="" class="fa fa-step-forward pagerLink" ></span></a>
	</div>

	<!-- Calendar Call -->
	<table id="reservationsRoomTable" cellspacing="0" cellpadding="0">
		<thead>
			<tr id="reservationsRoomTableHeaderRow">
			</tr>
		</thead>
		<tbody id="reservationsRoomTableBody">

		</tbody>
	</table>

	<div class="clear:both;"></div>

<?php templates::display('footer'); ?>
