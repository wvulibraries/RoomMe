<?php
require_once("../engineHeader.php");

try {
	$calendar = new calendar();
	$calendar->setLocalvars();
	$localvars->set("dateSelect",$calendar->buildDateSelects());
	$localvars->set("calendar",drawRoomCalendar($calendar->getRoomID(),array($calendar->dates['display']['month'],$calendar->dates['display']['day'],$calendar->dates['display']['year'])));
}
catch (Exception $e) {
	errorHandle::errorMsg("Invalid or missing Building or Room ID");
	$error = TRUE;
}

?>

<header>
	<h1>{local var="name"} Calendar</h1>
	<h2>{local var="month"} / {local var="day"} / {local var="year"}</h2>
</header>

<br />

<section>

	{local var="dateSelect"}
	<br /><br />
	{local var="calendar"}

</section>