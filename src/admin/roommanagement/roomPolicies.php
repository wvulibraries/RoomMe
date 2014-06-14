<?php
require_once("../engineHeader.php");


// Get site configuration options used on this page
$db        = db::get($localvars->get('dbConnectionName'));
$sql       = sprintf("SELECT value FROM siteConfig WHERE name='defaultReservationIncrements'");
$sqlResult = $db->query($sql);
$row       = $sqlResult->fetch();

$reservationIncrements = $row['value'];

$listObj = new listManagement("policies");

$options = array();
$options['field'] = "name";
$options['label'] = "Policy Name";
$listObj->addField($options);
unset($options);

$options = array();
$options['field'] = "description";
$options['label'] = "Description";
$options['dupes'] = TRUE;
$listObj->addField($options);
unset($options);

$options = array();
$options['field'] = "url";
$options['label'] = "URL";
$options['dupes'] = TRUE;
$options['blank'] = TRUE;
$listObj->addField($options);
unset($options);

$options = array();
$options['field']    = "period";
$options['label']    = "Period";
$options['dupes']    = TRUE;
$options['validate'] = "integer";
$listObj->addField($options);
unset($options);

$options = array();
$options['field']    = "hoursAllowed";
$options['label']    = "Hours per Period";
$options['dupes']    = TRUE;
$options['validate'] = "integer";
$listObj->addField($options);
unset($options);

$options = array();
$options['field']    = "bookingsAllowedInPeriod";
$options['label']    = "Bookings per Period";
$options['dupes']    = TRUE;
$options['validate'] = "integer";
$listObj->addField($options);
unset($options);

$options = array();
$options['field']    = "maxLoanLength";
$options['label']    = "Max Loan Length";
$options['dupes']    = TRUE;
$options['validate'] = "integer";
$listObj->addField($options);
unset($options);

$options = array();
$options['field']    = "fineAmount";
$options['label']    = "Fine Amount";
$options['dupes']    = TRUE;
// $options['validate'] = "float";
$listObj->addField($options);
unset($options);

$options = array();
$options['field']    = "publicScheduling";
$options['label']    = "Public Scheduling";
$options['dupes']    = TRUE;
$options['type']     = "yesNo";
$listObj->addField($options);
unset($options);

$options = array();
$options['field']    = "publicViewing";
$options['label']    = "Public Viewing";
$options['dupes']    = TRUE;
$options['type']     = "yesNo";
$listObj->addField($options);
unset($options);

$options = array();
$options['field']    = "sameDayReservations";
$options['label']    = "Create Same Day Reservations";
$options['dupes']    = TRUE;
$options['type']     = "yesNo";
$listObj->addField($options);
unset($options);

$options = array();
$options['field'] = "reservationIncrements";
$options['label'] = "Reservation Increments";
$options['dupes'] = TRUE;
$options['value'] = $reservationIncrements;
$listObj->addField($options);
unset($options);

$options = array();
$options['field']    = "futureScheduleLength";
$options['label']    = "Future Schedule Length";
$options['dupes']    = TRUE;
$options['validate'] = "integer";
$listObj->addField($options);
unset($options);

$errorMsg = NULL;
if(isset($_POST['MYSQL']['policies_submit'])) {
	$errorMsg = $listObj->insert();
	$errorMsg = errorHandle::prettyPrint();
}
else if (isset($_POST['MYSQL']['policies_update'])) {
	$errorMsg = $listObj->update();
	$errorMsg = errorHandle::prettyPrint();
}

templates::display('header');
?>

<header>
<h1>Policies Management</h1>
</header>

<?php
if (!isnull($errorMsg)) {
?>
<section id="actionResults">
	<header>
		<h1>Results</h1>
	</header>
	<?php print $errorMsg; ?>
</section>
<?php } ?>


<section>
<header><h1>Policies Type</h1></header>
{listObject display="insertForm" addGet="true"}
</section>

<section>
<header><h1>Policies Types</h1></header>
{listObject display="editTable" addGet="true"}
</section>


<?php
templates::display('footer');
?>