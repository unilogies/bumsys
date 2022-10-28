<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo get_title(); ?></title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="shortcut icon" href="<?php echo full_website_address() . "/assets/images/logo.png" ?>" type="image/x-icon">

    <!-- Include all CSS -->
    <link rel="stylesheet" href="<?php echo full_website_address(); ?>/css/">

    <!-- Include all Header JS -->
    <script src="<?php echo full_website_address(); ?>/js/?q=head&v=2.1.3"></script>

    <script>
        /* store the website url in javascript variable */
        var full_website_address = "<?php echo full_website_address(); ?>";

        /* Store the CSRF Token */
        var xCsrfToken = '<?php echo isset($_SESSION["csrf_token"]) ? $_SESSION["csrf_token"] : ""; ?>';
    </script>

</head>

<?php

    $selectLoggedUser = easySelect(
        "employees",
        "emp_firstname, emp_lastname, emp_positions, emp_photo",
        array(),
        array(
            "emp_id" => $_COOKIE["eid"]
        )
    );

    
    $LoggedUser = $selectLoggedUser["data"][0];

    $empPhotoUrl = empty($LoggedUser['emp_photo']) ? full_website_address() . "/assets/images/defaultUserPic.png" : full_website_address() . "/images/?for=employees&id=" . $_COOKIE["eid"] . "&v=" . strlen($LoggedUser['emp_photo']);

    $empFullName = $LoggedUser["emp_firstname"] . " " . $LoggedUser["emp_lastname"];

?>

<body class="fixed hold-transition skin-blue sidebar-mini">
    <div class="wrapper">

        <!-- Main Header -->
        <header class="main-header">
            <!-- Logo -->
            <a href="<?php echo full_website_address(); ?>" class="logo">
                <!-- mini logo for sidebar mini 50x50 pixels -->
                <span class="logo-mini"><img src="<?php echo full_website_address() . "/assets/images/logo.png" ?>" alt="Royal Live"></span>
                <!-- logo for regular state and mobile devices -->
                <span class="logo-lg"><b>BuM</b>Sys</span>
            </a>

            <!-- Header Navbar -->
            <nav class="navbar navbar-static-top">
                <!-- Sidebar toggle button-->
                <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
                    <span class="sr-only">Toggle navigation</span>
                </a>
                <!-- Navbar Right Menu -->
                <div class="navbar-custom-menu">
                    <ul class="nav navbar-nav">

                        <!-- Notifications: style can be found in dropdown.less -->
                        <li class="dropdown notifications-menu">

                            <?php 
                                $selectUnsolvedCase = easySelectA(array(
                                    "table"     => "cases",
                                    "fields"    => "count(*) totalUnsolvedCase",
                                    "where"     => array(
                                        "is_trash = 0 and case_status not in('Solved', 'Closed')"
                                    )
                                ));
                                

                                $totalUnsolvedCase = 0;
                                if($selectUnsolvedCase !== false) {
                                    $totalUnsolvedCase = $selectUnsolvedCase["data"][0]["totalUnsolvedCase"];
                                }
                            ?>

                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-bell-o"></i>
                                <?php 
                                    if( $totalUnsolvedCase > 0 ) {
                                        echo '<span class="label label-warning">'. $totalUnsolvedCase .'</span>';
                                    }
                                ?>
                                
                            </a>
                            <ul class="dropdown-menu">
                                <li class="header">You have <?php echo $totalUnsolvedCase; ?> notifications</li>
                                <li>
                                    <!-- inner menu: contains the actual data -->
                                    <ul class="menu">
  
                                        <li>
                                            <a href="<?php echo full_website_address() ?>/customer-support/case-list/">
                                                <i class="fa fa-warning text-yellow"></i> 
                                                You have total <?php echo $totalUnsolvedCase; ?> unsolved cases.
                                            </a>
                                        </li>
                        
                                    </ul>
                                </li>
                                <li class="footer"><a href="#">View all</a></li>
                            </ul>
                        </li>

                        <li style="margin-left: 20px;">
                            <a style="background-color: #222d32;" href="<?php echo full_website_address() ?>/sales/pos/"><i class="fa fa-th-large"></i> <span style="display: inline-block; padding-left: 5px;"> POS</span> </a>
                        </li>

                        <!-- User Account Menu -->
                        <li class="dropdown user user-menu">
                            <!-- Menu Toggle Button -->
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <!-- The user image in the navbar-->
                                <img width='160px' height='160px' src='<?= $empPhotoUrl; ?>' class='user-image' />

                                <!-- hidden-xs hides the username on small devices so only the image appears. -->
                                <span class="hidden-xs"> <?php echo $empFullName; ?> </span>
                            </a>
                            <ul class="dropdown-menu">
                                <!-- The user image in the menu -->
                                <li class="user-header">
                                    <img width='160px' height='160px' src='<?= $empPhotoUrl; ?>' class='img-circle' />

                                    <p>
                                        <?php echo "{$empFullName} - {$LoggedUser['emp_positions']}"; ?>
                                        <small>Member since Nov. 2012</small>
                                    </p>
                                </li>
                                <!-- Menu Body -->
                                <li class="user-body">
                                    <div class="row">
                                        <div class="col-xs-4 text-center">
                                            <a href="#">Followers</a>
                                        </div>
                                        <div class="col-xs-4 text-center">
                                            <a href="#">Sales</a>
                                        </div>
                                        <div class="col-xs-4 text-center">
                                            <a href="#">Friends</a>
                                        </div>
                                    </div>
                                    <!-- /.row -->
                                </li>
                                <!-- Menu Footer-->
                                <li class="user-footer">
                                    <div class="pull-left">
                                        <a data-toggle="modal" data-target="#modalDefault" href="<?php echo full_website_address() . "/xhr/?icheck=false&module=peoples&page=editProfile&id=" . $_SESSION['uid']; ?>" class="btn btn-default btn-flat">Profile</a>
                                    </div>
                                    <div class="pull-right">
                                        <a href="<?php echo full_website_address() . "/logout/"; ?>" class="btn btn-default btn-flat">Sign out</a>
                                    </div>
                                </li>
                            </ul>
                        </li>
                        <!-- Control Sidebar Toggle Button -->
                        <li>
                            <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>
        <!-- Left side column. contains the logo and sidebar -->
        <aside class="main-sidebar">

            <!-- sidebar: style can be found in sidebar.less -->
            <section class="sidebar">

                <!-- Sidebar user panel (optional) -->
                <div class="user-panel">
                    <div class="pull-left image">
                        <img width='160px' height='160px' src='<?= $empPhotoUrl; ?>' class='img-circle' />

                    </div>
                    <div class="pull-left info">
                        <p><?php echo $empFullName; ?></p>
                        <!-- Status -->
                        <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
                    </div>
                </div>

                <!-- Sidebar Menu -->
                <?php


                echo "<ul class='sidebar-menu' data-widget='tree'>";
                echo $generatedMenu;
                echo '</ul>';


                ?>
                <!-- /.sidebar-menu -->
            </section>
            <!-- /.sidebar -->
        </aside>

        <!-- .dynamic-containter -->
        <div class='dynamic-container'>