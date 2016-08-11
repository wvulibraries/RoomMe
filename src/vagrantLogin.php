<?php

if (!preg_match("/^localhost/",$_SERVER['HTTP_HOST'])) {
	header("HTTP/1.0 404 Not Found");
	die("404. Not Found.");
}

require 'engineHeader.php';

print "<pre>";
var_dump($_SERVER);
print "</pre>";

$session = 'a:7:{s:4:"page";s:25:"/services/rooms/index.php";s:2:"qs";s:0:"";s:6:"groups";a:4:{i:0;s:32:"libraryWeb_roomReservation_rooms";i:1;s:32:"libraryWeb_roomReservation_admin";i:2;s:26:"libraryWeb_roomReservation";i:3;s:12:"Domain Users";}s:2:"ou";s:4:"Main";s:8:"username";s:7:"vagrant";s:8:"authtype";s:4:"ldap";s:9:"auth_ldap";a:3:{s:6:"groups";a:4:{i:0;s:95:"CN=libraryWeb_roomReservation_rooms,OU=Web,OU=Groups,OU=Library,OU=Main,DC=wvu-ad,DC=wvu,DC=edu";i:1;s:95:"CN=libraryWeb_roomReservation_admin,OU=Web,OU=Groups,OU=Library,OU=Main,DC=wvu-ad,DC=wvu,DC=edu";i:2;s:89:"CN=libraryWeb_roomReservation,OU=Web,OU=Groups,OU=Library,OU=Main,DC=wvu-ad,DC=wvu,DC=edu";i:3;s:48:"CN=Domain Users,CN=Users,DC=wvu-ad,DC=wvu,DC=edu";}s:6:"userDN";s:93:"CN=vagrant box,OU=systemsGeneratedCourtesyAccounts,OU=Library,OU=Main,DC=wvu-ad,DC=wvu,DC=edu";s:8:"username";s:7:"vagrant";}}';

session::import(unserialize($session));

?>

<p>You are now logged in. Use the links below to navigate:</p>

<ul>
	<li><a href="/services/rooms/">Public Pages</a></li>
	<li><a href="/services/rooms/admin/">Admin Pages</a></li>
</ul>
