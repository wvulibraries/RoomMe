<?php
require_once("../engineHeader.php");

$updateTable = FALSE;
$errorMsg    = NULL;

recurseInsert("includes/emailFields.php");

if(isset($engine->cleanPost['MYSQL']['emailMessages_submit'])) {
	$errorMsg = $listObj->insert();
	$errorMsg = errorHandle::prettyPrint();
}
else if (isset($engine->cleanPost['MYSQL']['emailMessages_update'])) {
	$errorMsg = $listObj->update();
	$errorMsg = errorHandle::prettyPrint();
}

$engine->eTemplate("include","header");
?>

<header>
<h1>Email Management</h1>
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
<header><h1>New Email</h1></header>
{listObject display="insertForm" addGet="true"}
</section>

<?php
$updateTable = TRUE;
require("includes/emailFields.php");
?>

<section>
<header><h1>Edit Email</h1></header>
{listObject display="editTable" addGet="true"}
</section>


<?php
$engine->eTemplate("include","footer");
?>