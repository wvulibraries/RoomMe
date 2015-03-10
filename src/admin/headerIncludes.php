<?php

	$patterns = templates::getTemplatePatterns();
	if (isset($patterns['formBuilder'])) {

		print '{form display="assets"}';
	}


?>

<script type="text/javascript" src="{local var="roomResBaseDir"}/javascript/doubleMultiSelect.js"></script>