<?php

function getUserInfo($username) {

	$engine = EngineAPI::singleton();

	require '/home/www.libraries.wvu.edu/phpincludes/databaseConnectors/database.lib.wvu.edu.remote.php';
	$databaseOptions['dbName'] = "authentication";
	$authDB                   = db::create('mysql', $databaseOptions, 'authDB');;

	$sql = sprintf("SELECT master.* FROM accountUsernames LEFT JOIN master on master.uid=accountUsernames.uid WHERE accountUsernames.username=?",
		$username
		);

	$sqlResult = $authDB->query($sql);

	if ($sqlResult->error()) {
		errorHandle::newError(__FUNCTION__."() - ".$sqlResult->errorMsg(), errorHandle::DEBUG);
		return(FALSE);
	}

	if ($sqlResult->rowCount() < 1) {
		return(FALSE);
	}
	else if ($sqlResult->rowCount() > 1) {
		errorHandle::newError(__FUNCTION__."() - More than one user returned.", errorHandle::DEBUG);
		return(FALSE);
	}

	$row = $sqlResult->fetch();

	$userInfo             = array();
	$userInfo['initials'] = substr($row['firstname'],0,1).substr($row['lastname'],0,1);
	$userInfo['idNumber'] = $row['uid'];

	return($userInfo);

}

?>
