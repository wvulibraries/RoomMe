<?php
require_once("../engineHeader.php");

$listObj = new listManagement("building");

$options = array();
$options['field'] = "name";
$options['label'] = "Building Name";
$listObj->addField($options);
unset($options);

$options = array();
$options['field']    = "email";
$options['label']    = "Building Email";
$options['blank']    = TRUE;
$options['dupes']    = TRUE;
$options['validate'] = "email";
$listObj->addField($options);
unset($options);

$options = array();
$options['field']    = "phone";
$options['label']    = "Building Phone";
$options['blank']    = TRUE;
$options['dupes']    = TRUE;
$options['validate'] = "phone";
$listObj->addField($options);
unset($options);

$options = array();
$options['field']    = "fromEmail";
$options['label']    = "From Email";
$options['dupes']    = TRUE;
$options['validate'] = "email";
$listObj->addField($options);
unset($options);

$options = array();
$options['field']    = "roomListDisplay";
$options['label']    = "Room List Display";
$options['dupes']    = TRUE;
$options['value']    = "{name} -- {number}";
$listObj->addField($options);
unset($options);

$options = array();
$options['field'] = "roomSortOrder";
$options['label'] = "Room Sort Order";
$options['dupes'] = TRUE;
$options['type']  = "select";
$options['options'][0]['label'] = "Room Name";
$options['options'][0]['value'] = "name";
$options['options'][1]['label'] = "Room Number";
$options['options'][1]['value'] = "number";
$listObj->addField($options);
unset($options);

$options = array();
$options['field']    = "period";
$options['label']    = "Period";
$options['blank']    = TRUE;
$options['dupes']    = TRUE;
$options['validate'] = "integer";
$listObj->addField($options);
unset($options);

$options = array();
$options['field']    = "maxHoursAllowed";
$options['label']    = "Max Hours Per Period";
$options['blank']    = TRUE;
$options['dupes']    = TRUE;
$options['validate'] = "integer";
$listObj->addField($options);
unset($options);

$options = array();
$options['field']    = "bookingsAllowedInPeriod";
$options['label']    = "Max Bookings Per Period";
$options['blank']    = TRUE;
$options['dupes']    = TRUE;
$options['validate'] = "integer";
$listObj->addField($options);
unset($options);

$options = array();
$options['field']    = "fineAmount";
$options['label']    = "Fine Amount";
$options['blank']    = TRUE;
$options['dupes']    = TRUE;
// $options['validate'] = "integer";
$listObj->addField($options);
unset($options);

$options = array();
$options['field']    = "url";
$options['label']    = "Building URL";
$options['blank']    = TRUE;
$options['dupes']    = TRUE;
$options['validate'] = "url";
$listObj->addField($options);
unset($options);

$options = array();
$options['field']    = "externalURL";
$options['label']    = "External URL";
$options['blank']    = TRUE;
$options['dupes']    = TRUE;
$options['validate'] = "url";
$listObj->addField($options);
unset($options);

$options = array();
$options['field']    = "hoursRSS";
$options['label']    = "Hours RSS URL";
$options['blank']    = TRUE;
$options['dupes']    = TRUE;
$options['validate'] = "url";
$listObj->addField($options);
unset($options);

$options = array();
$options['field']    = "hoursURL";
$options['label']    = "Hours URL";
$options['blank']    = TRUE;
$options['dupes']    = TRUE;
$options['validate'] = "url";
$listObj->addField($options);
unset($options);

$options = array();
$options['field']    = "imageURL";
$options['label']    = "Image URL";
$options['blank']    = TRUE;
$options['dupes']    = TRUE;
$options['validate'] = "url";
$listObj->addField($options);
unset($options);

$options = array();
$options['field']    = "policyURL";
$options['label']    = "Policy URL";
$options['blank']    = TRUE;
$options['dupes']    = TRUE;
$options['validate'] = "url";
$listObj->addField($options);
unset($options);

$options = array();
$options['field']    = "fineLookupURL";
$options['label']    = "Fine Lookup URL";
$options['blank']    = TRUE;
$options['dupes']    = TRUE;
$options['validate'] = "url";
$listObj->addField($options);
unset($options);

$errorMsg = NULL;
if(isset($_POST['MYSQL']['building_submit'])) {
	$errorMsg = $listObj->insert();
	$errorMsg = errorHandle::prettyPrint();
}
else if (isset($_POST['MYSQL']['building_update'])) {
	$errorMsg = $listObj->update();
	$errorMsg = errorHandle::prettyPrint();
}

$engine->eTemplate("include","header");
?>

<header>
<h1>Building Management</h1>
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
<header><h1>New Building</h1></header>
{listObject display="insertForm" addGet="true"}
</section>

<section>
<header><h1>Edit Buildings</h1></header>
{listObject display="editTable" addGet="true"}
</section>


<?php
$engine->eTemplate("include","footer");
?>