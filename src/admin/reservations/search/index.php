<?php
require_once("../../engineHeader.php");

$currentMonth = date("n");
$currentDay   = date("j");
$currentYear  = date("Y");

if (isset($_POST['MYSQL']['search'])) {

	try {

		if (!isset($_POST['HTML']['username']) || is_empty($_POST['HTML']['username'])) {
			throw new Exception("Username cannot be blank.");
		}

		$user = new userInfo;
		if (!$user->get($_POST['MYSQL']['username'])) {
			throw new Exception("Unable to retrieve user");
		}	
		if (!$user->getReservations()) {
			throw new Exception("Unable to retrieve users reservations");
		}

		//@TODO this needs to be refactored into a listClass to handle this and the 
		// reservationsList.php page
		$table           = new tableObject("array");
		$table->sortable = TRUE;
		$table->summary  = "Room reservation listings";
		$table->class    = "styledTable";

		$hoursOnTable = getConfig("hoursOnReservationTable");

		$headers = array();
		$headers[] = "Username";
		$headers[] = "Building";
		$headers[] = "Room";
		$headers[] = "Start Time";
		$headers[] = "End Time";
		if ($hoursOnTable == "1") {
			$headers[] = "Hours";
		}
		$headers[] = "Edit";
		$table->headers($headers);

		$timeFormat = getTimeFormat();

		$time     = mktime(0,0,0,$_POST['MYSQL']['start_month'],$_POST['MYSQL']['start_day'],$_POST['MYSQL']['start_year']);
		$time_end = mktime(23,59,0,$_POST['MYSQL']['end_month'],$_POST['MYSQL']['end_day'],$_POST['MYSQL']['end_year']);

		$reservations = array();
		$totalHours   = 0;
		foreach ($user->reservations as $ID=>$reservation) {
			// Continue if the reservation is outside of the date range
			if ($reservation->reservation['startTime'] < $time || $reservation->reservation['endTime'] > $time_end) {
				continue;
			}

			$displayName = $user->user['username'];
			if (isset($reservation->reservation['groupname']) && !is_empty($reservation->reservation['groupname'])) {
				$displayName .= " (".$reservation->reservation['groupname'].")";
			}

			$roomDisplayName = str_replace("{name}", $reservation->room['name'], $reservation->building['roomListDisplay']);
			$roomDisplayName = str_replace("{number}", $reservation->room['number'], $roomDisplayName);

			$temp = array();
			$temp['username']  = $displayName; 
			$temp['building']  = $reservation->building['name'];
			$temp['room']      = $roomDisplayName; 
			$temp['startTime'] = date($timeFormat,$reservation->reservation['startTime']);
			$temp['endTime']   = date($timeFormat,$reservation->reservation['endTime']);
			if ($hoursOnTable == "1") {
				$reserveTime = ($reservation->reservation['endTime'] - $reservation->reservation['startTime'])/60/60;
				$temp['hoursOnReservationTable'] = ($reserveTime > 23.6)?"24":$reserveTime;
			}
			$temp['edit']      = sprintf('<a href="../create/?id=%s">Edit</a>',
				htmlSanitize($reservation->reservation['ID'])
				);

			$reservations[] = $temp;
			$totalHours     += $temp['hoursOnReservationTable'];

		}

		$localvars->set("reservationTable",$table->display($reservations));
		$localvars->set("totalHours",sprintf('<p><strong>Total Hours for patron during period:</strong> %s</p>',$totalHours));

	}
	catch (Exception $e) {
		errorHandle::errorMsg($e->getMessage());		
	}

	$localvars->set("username",$_POST['HTML']['username']);

}

templates::display('header');
?>

<header>
<h1>Patron Search</h1>
</header>

<?php if (count($engine->errorStack) > 0) {	?>
<section id="actionResults">
	<header>
		<h1>Results</h1>
	</header>
	<?php print errorHandle::prettyPrint(); ?>
</section>
<?php } ?>

<form action="{phpself query="true"}" method="post">
	{csrf}

		<fieldset>
			<legend>User Information</legend>
			<label for="username" class="requiredField">Username:</label> &nbsp; <input type="text" id="username" name="username" value="{local var="username"}" required/>
		</fieldset>
		<br />

		<table>
		<tr>
			<td>
				<label for="start_month">Month:</label><br />
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
				<label for="start_day">Day:</label><br />
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
				<label for="start_year">Year:</label><br />
				<select name="start_year" id="start_year" >
					<?php

					for($I=$currentYear-1;$I<=$currentYear+10;$I++) {
						printf('<option value="%s" %s>%s</option>',
							$I,
							($I==$currentYear)?"selected":"",
							$I);
					}
					?>
				</select>
			</td>
		</tr>
				<tr>
			<td>
				<label for="start_month">Month:</label><br />
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
				<label for="start_day">Day:</label><br />
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
				<label for="start_year">Year:</label><br />
				<select name="end_year" id="end_year" >
					<?php

					for($I=$currentYear-1;$I<=$currentYear+10;$I++) {
						printf('<option value="%s" %s>%s</option>',
							$I,
							($I==$currentYear)?"selected":"",
							$I);
					}
					?>
				</select>
			</td>
		</tr>
	</table>

	<input type="submit" name="search" value="Search" />

</form>

{local var="totalHours"}
{local var="reservationTable"}

<?php
templates::display('footer');
?>