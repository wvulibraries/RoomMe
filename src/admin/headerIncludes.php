<?php

	$patterns = templates::getTemplatePatterns();
	if (isset($patterns['formBuilder'])) {

		print '{form display="assets"}';
	}


?>
  <script type="text/javascript">
  	var roomReservationHome = '{local var="roomReservationHome"}';
  </script>
  
<script type="text/javascript" src="{local var="roomResBaseDir"}/javascript/roomReservations.js"></script>
<script type="text/javascript" src="{local var="roomResBaseDir"}/javascript/doubleMultiSelect.js"></script>