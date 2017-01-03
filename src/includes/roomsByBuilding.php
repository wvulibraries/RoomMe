<?php

$buildingObj = new building;
$buildings   = $buildingObj->getall();
$localvars   = localvars::getInstance();

?>

<h4 style="float:left;">Rooms by Building:</h4>
<hr class="roomHR"></hr>
<?php
     foreach ($buildings as $building) {
      $buildingURL = (is_empty($building['externalURL']))?sprintf("%s/building/?building=%s",$localvars->get("roomReservationHome"),$building['ID']):$building['externalURL'];

?>

<nobr><a class="policyLink1" href="<?php print $buildingURL; ?>"><i class="fa fa-building"></i><?php print $building['name'] ?></a></nobr>

<?php } ?>

<hr class="roomHR"></hr>
<br>
