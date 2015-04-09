<?php

require_once "../../engineHeader.php";
print date("l, F j", mktime(0,0,0,$_GET['MYSQL']['month'],$_GET['MYSQL']['day'],$_GET['MYSQL']['year']));

?>