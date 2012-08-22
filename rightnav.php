<?php
	localvars::add("loginURL",    $engineVars['loginPage'].'?page='.$_SERVER['PHP_SELF']."&qs=".(urlencode($_SERVER['QUERY_STRING'])));
?>

<ul>
	<li>
		<a href="index.php">Room Reservation Home</a>
	</li>
	<li>
		<a href="find.php">Check Room Availability</a>
	</li>
	<li>
		<a href="http://www.hsc.wvu.edu/its/Forms/SchedulingForms/LibraryStudyRoomRequest.aspx">Health Sciences Libraries</a>
	</li>
	<li>
		<?php if (isempty(sessionGet("username"))) { ?>
		<a href="{local var="loginURL"}">Login</a>
		<?php } else { ?>
		<a href="view.php">View your reservations</a>
		<a href="{engine var="logoutPage"}?csrf={engine name="csrfGet"}">Logout</a>
		<?php } ?>
	</li>
</ul>