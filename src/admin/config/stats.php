<?php
require_once("../engineHeader.php");
recurseInsert("includes/functions.php","php");
$errorMsg = "";
$error    = FALSE;

$currentMonth = date("n");
$currentDay   = date("j");
$currentYear  = date("Y");

$output = "";

if (isset($_POST['HTML']['genStats'])) {

	$stime     = mktime(0,0,0,$_POST['MYSQL']['start_month'],$_POST['MYSQL']['start_day'],$_POST['MYSQL']['start_year']);
	$etime     = mktime(0,0,0,$_POST['MYSQL']['end_month'],$_POST['MYSQL']['end_day'],$_POST['MYSQL']['end_year']);

	$db        = db::get($localvars->get('dbConnectionName'));

	$sql       = sprintf("SELECT reservations.*, building.name as buildingName, building.roomListDisplay as roomListDisplay, rooms.name as roomName, rooms.number as roomNumber, via.name as via FROM `reservations` LEFT JOIN `rooms` on reservations.roomID=rooms.ID LEFT JOIN `building` ON building.ID=rooms.building LEFT JOIN `via` on via.ID=reservations.createdVia WHERE reservations.endTime<=? AND reservations.startTime>=? ORDER BY building.name, rooms.name, rooms.number, via.name");

	$sqlResult = $db->query($sql,array($etime,$stime));

	if ($sqlResult['result']) {
		$stats = array();
		while ($row = $sqlResult->fetch()) {

			$roomDisplayName = str_replace("{name}", $row['roomName'], $row['roomListDisplay']);
			$roomDisplayName = str_replace("{number}", $row['roomNumber'], $roomDisplayName);

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

templates::display('header');
?>

<header>
<h1>Reservation Listing</h1>
</header>

<form action="{phpself query="true"}" method="post">
	{csrf}
	<table>
		<tr>
			<td>
				<label for="start_month">Month:</label>
				<select name="start_month" id="start_month" >
					<?php

					for($I=1;$I<=12;$I++) {
						printf('<option value="%s" %s>%s</option>',
							($I < 10)?"0".$I:$I,
							($I == $currentMonth)?"selected":"",
							$I);
					}
					?>
				</select>
			</td>
			<td>
				<label for="start_day">Day:</label>
				<select name="start_day" id="start_day" >
					<?php

					for($I=1;$I<=31;$I++) {
						printf('<option value="%s" %s>%s</option>',
							($I < 10)?"0".$I:$I,
							($I == $currentDay)?"selected":"",
							$I);
					}
					?>
				</select>
			</td>
			<td>
				<label for="start_year">Year:</label>
				<select name="start_year" id="start_year" >
					<?php

					for($I=$currentYear-10;$I<=$currentYear+10;$I++) {
						printf('<option value="%s"%s>%s</option>',
							$I,
							($I == $currentYear)?"selected":"",
							$I);
					}
					?>
				</select>
			</td>

		</tr>
		<tr>
			<td>
				<label for="end_month">Month:</label>
				<select name="end_month" id="end_month" >
					<?php

					for($I=1;$I<=12;$I++) {
						printf('<option value="%s" %s>%s</option>',
							($I < 10)?"0".$I:$I,
							($I == $currentMonth)?"selected":"",
							$I);
					}
					?>
				</select>
			</td>
			<td>
				<label for="end_day">Day:</label>
				<select name="end_day" id="end_day" >
					<?php

					for($I=1;$I<=31;$I++) {
						printf('<option value="%s" %s>%s</option>',
							($I < 10)?"0".$I:$I,
							($I == $currentDay)?"selected":"",
							$I);
					}
					?>
				</select>
			</td>
			<td>
				<label for="end_year">Year:</label>
				<select name="end_year" id="end_year" >
					<?php

					for($I=$currentYear-10;$I<=$currentYear+10;$I++) {
						printf('<option value="%s" %s>%s</option>',
							$I,
							($I == $currentYear)?"selected":"",
							$I);
					}
					?>
				</select>
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