<?php
require_once("engineHeader.php");
recurseInsert("includes/functions.php","php");

// $localVars = localVars::getInstance();
$db        = db::get($localvars->get('dbConnectionName'));

$sql       = sprintf("SELECT * FROM building ORDER BY name");
$sqlResult = $db->query($sql);


$localvars->set("policyLabel",getResultMessage("policyLabel"));

templates::display('header');
?>

<header>
<h1>Room Reservations</h1>
</header>

<?php
while ($row = $sqlResult->fetch()) {
?>

<section class="reservationsLibrary">
	<header>
		<h1><?php print htmlSanitize($row['name']); ?></h1>
	</header>

	<ul>
		<?php if (is_empty($row['externalURL'])) { ?>
		<li>
			<a href="building.php?building=<?php print htmlSanitize($row['ID']) ?>">View &amp; Reserve Rooms</a>
		</li>

		<?php if (isset($row['policyURL']) && !is_empty($row['policyURL'])) { ?>
		<li>
			<a href="<?php print htmlSanitize($row['policyURL']) ?>">View Library {local var="policyLabel"}</a>
		</li>
		<?php } ?>

		<?php if (isset($row['hoursURL']) && !is_empty($row['hoursURL'])) { ?>
		<li>
			<a href="<?php print htmlSanitize($row['hoursURL']) ?>">View Library Hours</a>
		</li>
		<?php } ?>

		<?php if (isset($row['url']) && !is_empty($row['url'])) { ?>
		<li>
			<a href="<?php print htmlSanitize($row['url']) ?>">View Library Homepage</a>
		</li>
		<?php } ?>

		<li>
			<a href="#" class="calendarModal_link" data-type="building" data-id="<?php print htmlSanitize($row['ID']) ?>">View Reservation Calendar &ndash; All Rooms</a>
		</li>
		<?php } else { ?>

		<li>
			<a href="<?php print htmlSanitize($row['externalURL']) ?>">View &amp; Reserve Rooms</a>
		</li>

		<?php } ?>
	</ul>

	<?php if (isset($row['imageURL']) && !is_empty($row['imageURL'])) { ?>
	<img src="<?php print htmlSanitize($row['imageURL']); ?>" class="reservationsLibraryImage"/>
	<?php } ?>
</section>

<?php } ?>

<div id="calendarModal">
</div>

<?php
templates::display('footer');
?>