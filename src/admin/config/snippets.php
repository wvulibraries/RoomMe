<?php
require_once("../engineHeader.php");

$localvars = localvars::getInstance();

$snippet = new Snippet("pageContent","content",db::get("appDB"));
$snippet->snippetPublicURL = $localvars->get("roomResBaseDir").$snippet->snippetPublicURL;

require("../includes/snippetsFunctions.php");

if (isset($_POST['MYSQL']['pageContent_submit'])) {
	$listObj->insert();

	if (!isset($engine->errorStack['error'])) {
		unset($listObj);
		$_GET['MYSQL']['snippetID'] = $localvars->get("listObjInsertID");
	}
}
else if (isset($_GET['MYSQL']['deleteID'])) {
	$snippet->delete($_GET['MYSQL']['deleteID'],"ID");
}
else {
	if (isset($_GET['MYSQL']['snippetID'])) {
		$db  = db::get($localvars->get('dbConnectionName'));
		$sql = sprintf("SELECT * FROM `%s` WHERE ID=? LIMIT 1",
        	"pageContent"
        	);
        $sqlResult = $db->query($sql, array($_GET['MYSQL']['snippetID']));

        if ($sqlResult->error()) {
        	errorHandle::errorMsg("Error fetching content.");
        }
        else {
        	$content = $sqlResult->fetch();
        	$localvars->set("content",$content['content']);
        }
	}

	// Redeclare with HTML sanitizing
	if (isset($_POST['HTML']['snippetID'])) {
		$localvars->set("snippetID",$_POST['HTML']['snippetID']);
	}
	else if (isset($_GET['HTML']['snippetID'])) {
		$localvars->set("snippetID",$_GET['HTML']['snippetID']);
	}
	else {
		$localvars->set("snippetID","");
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

$localvars->set("snippetList",$snippet->insertSnippetList("we_snippetList","ul",TRUE,TRUE));

templates::display('header');
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
templates::display('footer');
?>
