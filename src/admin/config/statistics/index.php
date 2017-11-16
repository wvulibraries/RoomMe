<?php
require_once("../../engineHeader.php");
recurseInsert("includes/functions.php","php");
$errorMsg = "";
$error    = FALSE;

$currentMonth = date("n");
$currentDay   = date("j");
$currentYear  = date("Y");

$output = "";

if (isset($_POST['HTML']['genStats'])) {

	$stime     = mktime(0,0,0,$_POST['MYSQL']['start_month'],$_POST['MYSQL']['start_day'],$_POST['MYSQL']['start_year']);
	$etime     = mktime(23,0,0,$_POST['MYSQL']['end_month'],$_POST['MYSQL']['end_day'],$_POST['MYSQL']['end_year']);

	$db        = db::get($localvars->get('dbConnectionName'));

	$sql       = sprintf("SELECT reservations.*, building.name as buildingName, building.roomListDisplay as roomListDisplay, rooms.name as roomName, rooms.number as roomNumber, via.name as via FROM `reservations` LEFT JOIN `rooms` on reservations.roomID=rooms.ID LEFT JOIN `building` ON building.ID=rooms.building LEFT JOIN `via` on via.ID=reservations.createdVia WHERE reservations.endTime<=? AND reservations.startTime>=? ORDER BY building.name, rooms.name, rooms.number, via.name");

	$sqlResult = $db->query($sql,array($etime,$stime));

	if (!$sqlResult->error()) {
		$stats                               = array();
		$stats['buildins']                   = array();
		$stats['totals']['total']            = 0;
		$stats['totals']['via']              = array();

		while ($row = $sqlResult->fetch()) {

			$roomDisplayName = str_replace("{name}", $row['roomName'], $row['roomListDisplay']);
			$roomDisplayName = str_replace("{number}", $row['roomNumber'], $roomDisplayName);

			// Build the array
			if (!isset($stats['buildings'][$row['buildingName']])) {
				$stats['buildings'][$row['buildingName']] = array();
				$stats['buildings'][$row['buildingName']]['total'] = 0;
			}
			if (!isset($stats['buildings'][$row['buildingName']]['rooms'])) {
				$stats['buildings'][$row['buildingName']]['rooms'] = array();
			}
			if (!isset($stats['buildings'][$row['buildingName']]['rooms'][$roomDisplayName])) {
				$stats['buildings'][$row['buildingName']]['rooms'][$roomDisplayName] = 0;
			}
			if (!isset($stats['buildings'][$row['buildingName']]['via'])) {
				$stats['buildings'][$row['buildingName']]['via'] = array();
			}
			if (!isset($stats['buildings'][$row['buildingName']]['via'][$row['via']])) {
				$stats['buildings'][$row['buildingName']]['via'][$row['via']] = 0;
			}
			if (!isset($stats['totals']['via'][$row['via']])) {
				$stats['totals']['via'][$row['via']] = 0;
			}

			$stats['buildings'][$row['buildingName']]['total']++;
			$stats['buildings'][$row['buildingName']]['rooms'][$roomDisplayName]++;
			$stats['buildings'][$row['buildingName']]['via'][$row['via']]++;

			$stats['totals']['total']++;
			$stats['totals']['via'][$row['via']]++;
		}

		$output  = "<ul>";
		$output .= "<li><strong>Total Reservations:</strong> ".$stats['totals']['total']."</li>";
		$output .= "<li><strong>Created Via:</strong><ul>";
		foreach ($stats['totals']['via'] as $via=>$stat) {
			$output .= "<li><strong>".((is_empty($via))?"Public Interface":$via)."</strong>: ".$stat."</li>";
		}
		$output .= "</ul></li>"; // created via

		foreach ($stats['buildings'] as $buildingName=>$building) {
			$output .= "<li><h1>".$buildingName."</h1><ul>";

			$output .= "<li><strong>Total Reservations:</strong> ".$building['total']."</li>";

			$output .= "<li><h2>Created Via:</h2><ul>";
			foreach ($building['via'] as $via=>$stat) {
				$percent = sprintf("%02.02f",
					(((float)((float)$stat/(float)$building['total']))* 100)
					);
				$output .= "<li><strong>".((is_empty($via))?"Public Interface":$via)."</strong>: ".$stat." (".$percent."%)</li>";
			}
			$output .= "</ul></li>"; // created via

			$output .= "<li><h2>Rooms:</h2><ul>";
			foreach ($building['rooms'] as $roomName=>$stat) {
				$output .= "<li><strong>".$roomName."</strong>: ".$stat."</li>";
			}
			$output .= "</ul></li>"; // Rooms

			$output .= "</ul></li>";
		}

	}



}

$localvars->set("statsOutput",$output);

$date = new date;

// @TODO display on month dropdown should be configurable via interface
$localvars->set("monthSelect", $date->dropdownMonthSelect(1,$currentMonth,array("name"=>"start_month", "id"=>"start_month", "class" => "start_date")));
$localvars->set("daySelect",   $date->dropdownDaySelect($currentDay,array("name"=>"start_day", "id"=>"start_day", "class" => "start_date")));
$localvars->set("yearSelect",  $date->dropdownYearSelect(-2,10,$currentYear,array("name"=>"start_year", "id"=>"start_year", "class" => "start_date")));

$localvars->set("endmonthSelect", $date->dropdownMonthSelect(1,$currentMonth,array("name"=>"end_month", "id"=>"end_month", "class" => "end_date")));
$localvars->set("enddaySelect",   $date->dropdownDaySelect($currentDay,array("name"=>"end_day", "id"=>"end_day", "class" => "end_date")));
$localvars->set("endyearSelect",  $date->dropdownYearSelect(-2,10,$currentYear,array("name"=>"end_year", "id"=>"end_year", "class" => "end_date")));

templates::display('header');
?>

<header>
<h1>Statistics</h1>
</header>

<form action="{phpself query="true"}" method="post">
	{csrf}
	<table>
		<tr>
			<td>
				<label for="start_month">Month:</label>
				{local var="monthSelect"}
			</td>
			<td>
				<label for="start_day">Day:</label>
				{local var="daySelect"}
			</td>
			<td>
				<label for="start_year">Year:</label>
				{local var="yearSelect"}
			</td>

		</tr>
		<tr>
			<td>
				<label for="end_month">Month:</label>
				{local var="endmonthSelect"}
			</td>
			<td>
				<label for="end_day">Day:</label>
				{local var="enddaySelect"}
			</td>
			<td>
				<label for="end_year">Year:</label>
				{local var="endyearSelect"}
			</td>
		</tr>
		<tr>
						<td style="vertical-align:bottom" colspan="3">
				<input type="submit" name="genStats" value="Generate Stats" />
			</td>
		</tr>
	</table>

</form>

{local var="statsOutput"}

<?php
templates::display('footer');
?>
