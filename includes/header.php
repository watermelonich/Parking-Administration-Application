<?php

//$stmt = $pdo->prepare("SELECT message, link FROM notifications WHERE user_id = ? AND read_status = ? ORDER BY id DESC");

//$stmt->execute([$_SESSION['user_id'], 'unread']);

//$notification = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
  	<meta charset="UTF-8">
  	<meta name="viewport" content="width=device-width, initial-scale=1.0">
  	<meta http-equiv="X-UA-Compatible" content="ie=edge">
  	<title>Admin Dashboard</title>
  	<link rel="stylesheet" href="styles.css">
  	<script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
</head>
<body>
	<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <!-- Navbar Brand-->
        <a class="navbar-brand ps-3" href="index.html">Parking System</a>
        <!-- Sidebar Toggle-->
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
        <!-- Navbar Search-->
        <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
            
        </form>
        <!-- Navbar-->
        
        <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <?php
                    if($_SESSION['user_type'] == 'Master')
                    {
                    ?>
                    <li><a class="dropdown-item" href="setting.php">Setting</a></li>
                    <?php
                    }
                    ?>
                    <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                    
                    <li><hr class="dropdown-divider" /></li>
                    <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                </ul>
            </li>
        </ul>
    </nav>
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <?php
                        if($_SESSION['user_type'] == 'Master')
                        {
                        ?>
                        <a class="nav-link" href="category.php">
                            Vehical Category
                        </a>
                        <a class="nav-link" href="duration.php">
                            Parking Duration
                        </a>
                        <a class="nav-link" href="prices.php">
                            Parking Rate
                        </a>
                        <a class="nav-link" href="slots.php">
                            Parking Slots
                        </a>
                        <a class="nav-link" href="customer.php">
                            Customer
                        </a>
                        <a class="nav-link" href="reports.php">
                            Report
                        </a>
                        <a class="nav-link" href="users.php">
                            Users
                        </a>
                        <a class="nav-link" href="profile.php">
                            Profile
                        </a>

                        <?php
                        }

                        if($_SESSION['user_type'] == 'User')
                        {
                        ?>
                        <a class="nav-link" href="customer.php">
                            Customer
                        </a>
                        <?php
                        }
                        ?>
                        <a class="nav-link" href="logout.php">
                            Logout
                        </a>
                    </div>
                </div>
                <div class="sb-sidenav-footer">
                    <div class="small">Logged in as:</div>
                    <?php //echo $_SESSION['user_name']; ?>
                </div>
            </nav>
        </div>
        <div id="layoutSidenav_content">
            <main>