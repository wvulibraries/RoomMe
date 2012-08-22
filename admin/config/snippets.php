<?php
require_once("../engineHeader.php");
$snippet = new Snippet("pageContent","content");
$snippet->snippetPublicURL = localvars::get("roomResBaseDir").$snippet->snippetPublicURL;

require("../includes/snippetsFunctions.php");

if (isset($engine->cleanPost['MYSQL']['pageContent_submit'])) {
	$listObj->insert();

	if (!isset($engine->errorStack['error'])) {
		unset($listObj);
		$engine->cleanGet['MYSQL']['snippetID'] = localvars::get("listObjInsertID");
	}
}
else if (isset($engine->cleanGet['MYSQL']['deleteID'])) {
	$snippet->delete($engine->cleanGet['MYSQL']['deleteID'],"ID");
}
else {
	if (isset($engine->cleanGet['MYSQL']['snippetID'])) {
        $sql = sprintf("SELECT * FROM `%s` WHERE ID='%s' LIMIT 1",
        	"pageContent",
        	$engine->cleanGet['MYSQL']['snippetID']
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
	if (isset($engine->cleanPost['HTML']['snippetID'])) {
		localvars::add("snippetID",$engine->cleanPost['HTML']['snippetID']);
	}
	else if (isset($engine->cleanGet['HTML']['snippetID'])) {
		localvars::add("snippetID",$engine->cleanGet['HTML']['snippetID']);
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