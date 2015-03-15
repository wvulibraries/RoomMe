<?php
require_once("../../engineHeader.php");

$db        = db::get($localvars->get('dbConnectionName'));
$sql       = sprintf("SELECT * FROM `building` ORDER BY `name`");
$sqlResult = $db->query($sql);

$options = "";
while ($row = $sqlResult->fetch()) {
	$options .= sprintf('<option value="%s">%s</option>',
		htmlSanitize($row['ID']),
		htmlSanitize($row['name']));
}

$localvars->set("librarySelectOptions",$options);

$type = "reservation";
if (isset($_GET['HTML']['type']) && $_GET['HTML']['type'] == "series") {
	$type = "series";
	$localvars->set("series","Series");
}
$localvars->set("type",$type);

templates::display('header');
?>

<header>
<h1>Reservation {local var="series"} Creation -- Select Library</h1>
</header>

<form action="reservationRoomSelect.php?type={local var="type"}" method="post">
	{csrf}

	<label for="library">Library:</label>
	<select name="library" id="library">
		{local var="librarySelectOptions"}
	</select>

	<br /><br />
	<input type="submit" name="librarySubmit" value="Select Library" />
</form>


<?php
templates::display('footer');
?>