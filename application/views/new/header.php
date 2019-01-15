<!DOCTYPE html>
<html>
<head>
    <?php $this->lang->load('systema', 'ukrainian'); ?>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Панель керування</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="robots" content="all,follow">


    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>

    <!-- Bootstrap CSS-->
    <link rel="stylesheet" href="/assets2/vendor/bootstrap/css/bootstrap.min.css">
    <!-- Font Awesome CSS-->
    <link rel="stylesheet" href="/assets2/vendor/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css" integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous">

    <!-- Fontastic Custom icon font-->
    <link rel="stylesheet" href="/assets2/css/fontastic.css">
    <!-- Google fonts - Poppins -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,700">
    <!-- theme stylesheet-->
    <link rel="stylesheet" href="/assets2/css/style.default.css" id="theme-stylesheet">
    <!-- Custom stylesheet - for your changes-->
    <link rel="stylesheet" href="/assets2/css/custom.css">
    <!-- Favicon-->
    <link rel="shortcut icon" href="/assets2/img/favicon.ico">

    <!-- Tweaks for older IEs--><!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script src="/assets/js/global.js"></script>

    <link rel="apple-touch-icon" href="//mindmup.s3.amazonaws.com/lib/img/apple-touch-icon.png" />
    <link href="/assets2/bootstrap-wysiwyg-master/external/google-code-prettify/prettify.css" rel="stylesheet">
</head>
<body>
<div class="page">
    <!-- Main Navbar-->
    <header class="header">
        <nav class="navbar">
            <?php echo $searchBox;?>
            <div class="container-fluid">
                <div class="navbar-holder d-flex align-items-center justify-content-between">
                    <!-- Navbar Header-->
                    <div class="navbar-header">
                        <!-- Navbar Brand --><a href="/" class="navbar-brand">
                            <div class="brand-text brand-big"><span>Добрий </span><strong>Друк</strong></div>
                            <div class="brand-text brand-small"><strong>ДД</strong></div></a>
                        <!-- Toggle Button--><a id="toggle-btn" href="#" class="menu-btn active"><span></span><span></span><span></span></a>
                    </div>
                    <!-- Navbar Menu -->
                    <ul class="nav-menu list-unstyled d-flex flex-md-row align-items-md-center">
                        <!-- Search-->
                        <li class="nav-item d-flex align-items-center"><a id="search" href="#"><i class="icon-search"></i></a></li>
                        <!-- Notifications
                        <li class="nav-item dropdown"> <a id="notifications" rel="nofollow" data-target="#" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link">
                                <i class="fa fa-comments"></i><span class="badge bg-red">8</span></a>
                            <ul aria-labelledby="notifications" class="dropdown-menu">
                                <li><a rel="nofollow" href="#" class="dropdown-item">
                                        <div class="notification">
                                            <div class="notification-content"><i class="fa fa-envelope bg-green"></i>You have 6 new messages </div>
                                            <div class="notification-time"><small>4 minutes ago</small></div>
                                        </div></a></li>
                                <li><a rel="nofollow" href="#" class="dropdown-item">
                                        <div class="notification">
                                            <div class="notification-content"><i class="fa fa-twitter bg-blue"></i>You have 2 followers</div>
                                            <div class="notification-time"><small>4 minutes ago</small></div>
                                        </div></a></li>
                                <li><a rel="nofollow" href="#" class="dropdown-item">
                                        <div class="notification">
                                            <div class="notification-content"><i class="fa fa-upload bg-orange"></i>Server Rebooted</div>
                                            <div class="notification-time"><small>4 minutes ago</small></div>
                                        </div></a></li>
                                <li><a rel="nofollow" href="#" class="dropdown-item">
                                        <div class="notification">
                                            <div class="notification-content"><i class="fa fa-twitter bg-blue"></i>You have 2 followers</div>
                                            <div class="notification-time"><small>10 minutes ago</small></div>
                                        </div></a></li>
                                <li><a rel="nofollow" href="#" class="dropdown-item all-notifications text-center"> <strong>view all notifications                                            </strong></a></li>
                            </ul>
                        </li>-->
                        <!-- Messages
                        <li class="nav-item dropdown"> <a id="messages" rel="nofollow" data-target="#" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link"><i class="fa fa-envelope-o"></i><span class="badge bg-orange">10</span></a>
                            <ul aria-labelledby="notifications" class="dropdown-menu">
                                <li><a rel="nofollow" href="#" class="dropdown-item d-flex">
                                        <div class="msg-profile"> <img src="/assets2/img/avatar-1.jpg" alt="..." class="img-fluid rounded-circle"></div>
                                        <div class="msg-body">
                                            <h3 class="h5">Jason Doe</h3><span>Sent You Message</span>
                                        </div></a></li>
                                <li><a rel="nofollow" href="#" class="dropdown-item d-flex">
                                        <div class="msg-profile"> <img src="/assets2/img/avatar-2.jpg" alt="..." class="img-fluid rounded-circle"></div>
                                        <div class="msg-body">
                                            <h3 class="h5">Frank Williams</h3><span>Sent You Message</span>
                                        </div></a></li>
                                <li><a rel="nofollow" href="#" class="dropdown-item d-flex">
                                        <div class="msg-profile"> <img src="/assets2/img/avatar-3.jpg" alt="..." class="img-fluid rounded-circle"></div>
                                        <div class="msg-body">
                                            <h3 class="h5">Ashley Wood</h3><span>Sent You Message</span>
                                        </div></a></li>
                                <li><a rel="nofollow" href="#" class="dropdown-item all-notifications text-center"> <strong>Read all messages    </strong></a></li>
                            </ul>
                        </li>-->
                        <!-- Logout    -->
                        <li class="nav-item"><a href="/main/logout" class="nav-link logout">Logout<i class="fa fa-sign-out"></i></a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    <div class="page-content d-flex align-items-stretch">
        <!-- Side Navbar -->
        <nav class="side-navbar">
            <!-- Sidebar Header-->
            <div class="sidebar-header d-flex align-items-center">
                <!--<div class="avatar"><img src="/assets2/img/avatar-1.jpg" alt="..." class="img-fluid rounded-circle"></div>-->
                <div class="title">
                    <h1 class="h4"><?php echo $this->ion_auth->user()->row()->first_name.' '.$this->ion_auth->user()->row()->last_name;?></h1>
                    <p></p>
                </div>
            </div>
            <!-- Sidebar Navidation Menus--><span class="heading">Основне</span>
            <ul class="list-unstyled">
                <?php foreach ($userGroups as $group):?>
                    <?php $active="";?>
                    <?php if($group->name==$this->uri->segment(1)) $active='class="active"'; ?>
                    <li <?php echo $active;?>><a href="/<?php echo $group->name;?>"><?php echo $group->icon.lang($group->description);?></a></li>
                <?php endforeach;?>

            </ul>
        </nav>