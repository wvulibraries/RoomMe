<?php

require_once "../../engineHeader.php";

print date("l, F j", mktime(0,0,0,@intval($_GET['MYSQL']['month']),@intval($_GET['MYSQL']['day']),@intval($_GET['MYSQL']['year'])));

?>
