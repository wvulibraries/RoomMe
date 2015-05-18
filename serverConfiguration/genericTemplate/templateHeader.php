<!DOCTYPE html>
<html lang="en">
    <head>
        <title>RoomMe | WVU Libraries</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
        <meta name="HandheldFriendly" content="True">
        <meta name="MobileOptimized" content="320">
		<meta name="keywords" content="{local var="currentDisplayObjectKeywords"}">
		<meta name="description" content="{local var="currentDisplayObjectDescription"}">
    	<meta name="author" content="WVU Libraries">
		<meta http-equiv="cleartype" content="on">
    	
        <!-- Favicon Goes Here -->
        <link rel="shortcut icon" href="favicon.ico">

        <!-- External CSS -->
        <link href="https://netdna.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.css" type="text/css" rel="stylesheet">
        <link href='https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,200,200italic,300,400italic,300italic,600,600italic,700,900,700italic,900italic|Bitter:400,400italic,700' rel='stylesheet' type='text/css'>        

        <!-- Local CSS -->
        <link href="{local var="roomResBaseDir"}/css/rooms.css" type="text/css" rel="stylesheet">

        <!-- JavaScripts -->
        <script type="text/javascript" src="https://lib.wvu.edu/javascript/2014/jquery-2.1.1.min.js"></script>
        
        <?php recurseInsert("headerIncludes.php","php") ?>
    </head>
    <body>

        <!-- Room Reservation Header -->
        <div class="roomReservation">
            <div class="wrap">
                <h2><a href="/services/rooms/">Room Reservations</a></h2>
                    <?php if (is_empty(session::get("username"))) { ?>
                        <a class="userLogin roomTabletDesktop" href="{local var="loginURL"}">
                            <i class="fa fa-user"></i>
                            User Login
                        </a>
                    <?php } else { ?>
                        <a class="userLogin roomTabletDesktop" href="{engine var="logoutPage"}?csrf={engine name="csrfGet"}">
                            <i class="fa fa-user"></i>User Logout
                        </a>                
                        <a class="userLogin roomTabletDesktop" href="{local var="roomReservationHome"}/calendar/user/" class="roomMobile bSubmit">
                            <i class="fa fa-check"></i>My Reservations
                        </a>
                    <?php } ?>
            </div>            
        </div>

        <!-- Page Wrapper -->
        <div class="wrap hpcard">
            <section class="bp-body-1c">