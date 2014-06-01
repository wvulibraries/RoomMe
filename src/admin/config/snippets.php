<?php
require_once("../engineHeader.php");
$snippet = new Snippet("pageContent","content");
$snippet->snippetPublicURL = localvars::get("roomResBaseDir").$snippet->snippetPublicURL;

require("../includes/snippetsFunctions.php");

if (isset($_POST['MYSQL']['pageContent_submit'])) {
	$listObj->insert();

	if (!isset($engine->errorStack['error'])) {
		unset($listObj);
		$_GET['MYSQL']['snippetID'] = localvars::get("listObjInsertID");
	}
}
else if (isset($_GET['MYSQL']['deleteID'])) {
	$snippet->delete($_GET['MYSQL']['deleteID'],"ID");
}
else {
	if (isset($_GET['MYSQL']['snippetID'])) {
        $sql = sprintf("SELECT * FROM `%s` WHERE ID='%s' LIMIT 1",
        	"pageContent",
        	$_GET['MYSQL']['snippetID']
        	);
        $sqlResult = $engine->openDB->query($sql);

        if (!$sqlResult['result']) {
        	errorHandle::errorMsg("Error fetching content.");
        }
        else {
        	$content = mysql_fetch_array($sqlResult['result'], MYSQL_ASSOC);
        	localvars::add("content",$content['content']);
        }
	}

	// Redeclare with HTML sanitizing
	if (isset($_POST['HTML']['snippetID'])) {
		localvars::add("snippetID",$_POST['HTML']['snippetID']);
	}
	else if (isset($_GET['HTML']['snippetID'])) {
		localvars::add("snippetID",$_GET['HTML']['snippetID']);
	}
	else {
		localvars::add("snippetID","");
	}

}

require("../includes/snippetsFunctions.php");


if (!is_empty($engine->errorStack)) {
	$engine->localVars("errorMsg", '<section id="actionResults">
		<header>
			<h1>Results</h1>
		</header>
		'.errorHandle::prettyPrint().'
	</section>
	');
}

localvars::add("snippetList",$snippet->insertSnippetList("we_snippetList","ul",TRUE,TRUE));

$engine->eTemplate("include","header");
?>

<header>
	<h1>Content Management</h1>
</header>

{local var="errorMsg"}

<section>
	<header>
		<h1>Select Snippet</h1>
	</header>

	<div>
		<a href="{phpself query="false"}?action=snippets">Add New Snippet</a>
		<hr />
		{local var="snippetList"}
	</div>
</section>

<section>
	<header>
		<h1>Edit Snippet</h1>
	</header>
	{listObject display="insertForm"}
</section>


<?php
$engine->eTemplate("include","footer");
?>