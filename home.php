﻿<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Price Alert</title>
    <link rel = "stylesheet" type = "text/css" href = "./style.css" media = "all">
</head>
<body>
    <header>
        <div class="header">
            <a href="home.html"><img id="logo" src="logo.png" alt="Price Alert Logo"></a>
            <h1>Price Alert</h1>
        </div>

        <div class="navBar">
            <ul>
                <li><a id="home" href="home.php">Home</a></li>
                <li><a href="myItems.php">My Items</a></li>
                <li><a href="myProfile.php">My Profile</a></li>
                <li><a href="contact.html">Contact Us</a></li>
                <li><a href="login.php">Log In</a></li>
            </ul>
        </div>
    </header>

    <div class="pageStrt">
        <!--<div class="row">-->
            <div class="about"><!--col-md-8 col-centered">-->
                <h3>About Price Alert</h3>
                <p>
                    Price Alert is a site that tracks items offered by multiple retailers and notifies the user of a price drop.
                    Watch the video below to create an account and get started.
                </p>
            </div>
        <!--</div>-->
        <br />
        <!--<div class="row">-->
            <div class="dashboard"> <!--col-md-8 col-centered">-->
                <h3>Your Dashboard</h3>
<?php
    function pullCount($userName){
        $host = "spring-2018.cs.utexas.edu";
        $user = "bcs2363";
        $pwd = "4fPUF78Nu~";
        $dbs = "cs329e_bcs2363";
        $port = "3306";

        $connect = mysqli_connect($host, $user, $pwd, $dbs, $port);

        if (empty($connect)) {
            die("mysqli_connect failed: " . mysqli_connect_error());
        }

        //TODO double check table name is correct
        $table_u = "itemToUser";

        $result = mysqli_query($connect, "SELECT COUNT(username) from $table_u WHERE username = '". $userName ."' ");
        $row = $result->fetch_row();
        return $row[0];
    }

    if (isset($_SESSION['username']) || isset($_COOKIE['username'])) {
        if (isset($_SESSION['username'])) {
            $userName = $_SESSION['username'];
        }
        else {
            $userName = $_COOKIE['username'];
            }
        $count = pullCount($userName);
        if ($count == 0) {
            print("<p>Hello, $userName. You are currently tracking $count items. Start tracking items on the <a href='myItems.php'>My Items</a> page.</p>");
        }
        else {
        print("<p>Hello, $userName. You are currently tracking $count items. View their status on the <a href='myItems.php'>My Items</a> page.</p>");
        }
    }
    else {
        print("<p><a href='login.php'>Log in</a> or <a href='signUp.php'>sign up</a> to begin tracking item sales.</p>");
    }
?>
            </div>
        <!--</div>-->
        <br />
        <div class="vidCon col-centered"><!--row>-->
            <!--<div class="col-md-8 col-centered">
                <div class="row">-->
                    <div class="video col-md-6">
                        <p>*The training video for how to get started with Price Alert would go here*</p>
                    </div>
                    <div class="vidText col-md-4">
                        <p>
                            Watch the video to the left for help getting started with Price Alert.
                            In this video, one of the members of the Price Alert team will walk through account creation as well as tracking your first item.
                        </p>
                    </div>
                <!--</div>
            </div>-->
        </div>
    </div>

    <footer>
        <div class="footer">
            <p>Friday, March 23rd, 2018.<br>Website created by Blake Schmidt, Ben Luzarraga, and Kyle Gruber.</p>
        </div>
    </footer>

</body>
</html>