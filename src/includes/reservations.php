<?php

// $localVars = localVars::getInstance();
$building = new building;
$localvars->set("buildingSelectOptions",$building->selectBuildingListOptions(FALSE,(isset($_POST['MYSQL']['library']))?$reservation->building['ID']:NULL));

?>

<!-- Reservations Section -->
<a id="nowSubmit" class="roomMobile bSubmit">
    <i class="fa fa-arrow-circle-o-down"></i> Available Now
</a>
<h3 class="roomH3 roomTabletDesktop">Reservations for <span class="currentDay">Saturday, February 28</span></h3>
<h3 class="roomH3 roomMobile">Make A Reservation</h3>
<a class="policyLink roomTabletDesktop" href="#">Reservation Policies 
    <i class="fa fa-exclamation-circle"></i>
</a>
<hr class="roomHR" />
<div class="styled-select">
    <select id="library">
        {local var="buildingSelectOptions"}             
    </select>
</div>
<div class="styled-select">
    <select id="start_month_modal">
		<option selected value='1'>Janaury</option>
        <option value='2'>February</option>
    </select>
</div>                                          
<div class="styled-select">
    <select id="start_day_modal">
        <option>1</option>
        <option>2</option>                          
    </select>
</div>
<div class="styled-select">
    <select id="start_year_modal">
        <option>2015</option>
        <option>2016</option>
    </select>    
</div>
<a id="calUpdateFormSubmit" class="bSubmit">
    <i class="fa fa-calendar"></i> Find A Room
</a>