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
<script type="text/javascript" src="{local var="roomResBaseDir"}/javascript/dayCheck.js"></script>


<style>

  .listTable {overflow:auto;}

  .editTable {overflow:auto;}

	::-webkit-scrollbar {
    -webkit-appearance: none;
    width: 4px;
	}

	::-webkit-scrollbar-thumb {
    border-radius: 4px;
    background-color: rgba(0,0,0,.5);
    -webkit-box-shadow: 0 0 1px rgba(255,255,255,.5);
  }
</style>
