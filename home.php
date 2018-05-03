<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Price Alert</title>
    <link rel = "stylesheet" type = "text/css" href = "./style.css" media = "all">
</head>
<body>
<?php

session_start();

if (!isset($_SESSION["username"]) && !isset($_COOKIE["username"])) {
    print <<<HEADER
<header>
    <div class="header">
        <a href="home.php"><img id="logo" src="logo.png" alt="Price Alert Logo"></a>
        <h1>Price Alert</h1>
    </div>

    <div class="navBar">
        <ul>
            <li><a id="home" href="home.php">Home</a></li>
            <li><a href="myItems.php">My Items</a></li>
            <li><a href="myProfile.php">My Profile</a></li>
            <li><a href="contact.php">Contact Us</a></li>
            <li><a href="login.php">Log In</a></li>
        </ul>
    </div>
</header>
HEADER;
} else {
    print <<<HEADER
<header>
    <div class="header">
        <a href="home.php"><img id="logo" src="logo.png" alt="Price Alert Logo"></a>
        <h1>Price Alert</h1>
    </div>

    <div class="navBar">
        <ul>
            <li><a id="home" href="home.php">Home</a></li>
            <li><a href="myItems.php">My Items</a></li>
            <li><a href="myProfile.php">My Profile</a></li>
            <li><a href="contact.php">Contact Us</a></li>
            <li><a href="logout.php">Log Out</a></li>
        </ul>
    </div>
</header>
HEADER;
}

print <<<START
<div class="pageStrt">
        <div class="about">
            <h3>About Price Alert</h3>
            <p>
                Price Alert is a site that tracks items offered by multiple retailers and notifies the user of a price drop.
                Watch the video below to create an account and get started.
            </p>
        </div>
    <br>
        <div class="dashboard">
            <h3>Your Dashboard</h3>
START;

	function pullInfo($userName){
        $host = "spring-2018.cs.utexas.edu";
        $user = "bcs2363";
        $pwd = "4fPUF78Nu~";
        $dbs = "cs329e_bcs2363";
        $port = "3306";

        $connect = mysqli_connect($host, $user, $pwd, $dbs, $port);

        if (empty($connect)) {
            die("mysqli_connect failed: " . mysqli_connect_error());
        }

        $result = mysqli_query($connect, "SELECT itemName, COUNT(itemName), alertPrice FROM itemToUser JOIN Item ON itemToUser.itemID = Item.itemID JOIN itemToRetailer ON itemToUser.itemID = itemToRetailer.itemID WHERE username = '". $userName ."' GROUP BY itemName, alertPrice");
        return $result;
    }

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

        $result = mysqli_query($connect, "SELECT itemName FROM Item JOIN itemToUser ON Item.itemID = itemToUser.itemID WHERE username = '". $userName ."' GROUP BY itemName");
        $count = 0;
        while ($row = $result->fetch_row()){
        	$count = $count + 1;
        }
        return $count;
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
        	print("<p>Hello, ".$userName.". You are not currently tracking any items, click on <a href=\"myItems.php\">My Items</a> to start tracking an item.<p>");
        }
        else {
        	if ($count == 1) {
        		$str = "<p>Hello, ".$userName.". You are currently tracking ".$count." item:<br><br>";
        	}
        	else {
        		$str = "<p>Hello, ".$userName.". You are currently tracking ".$count." items:<br><br>";
        	}
        	$result = pullInfo($userName);
       		while($row = $result->fetch_row()) {
        		$str = $str . "<b>" . $row[0] . " - ".$row[1]." retailers tracked - $".$row[2]." alert price</b><br>";
			}
        	print($str."<br>Click on <a href=\"myItems.php\">My Items</a> to view more information.<p>");
        }

    }
    else {
        print("<p><a href='login.php'>Log in</a> or <a href='signUp.php'>sign up</a> to begin tracking item sales.</p>");
    }
    print "</div>";

	print <<<VIDEO
    <br>
    <div class="vidCon col-centered">
        <div class="video col-md-6">
            <p>*The training video for how to get started with Price Alert would go here*</p>
        </div>
        <div class="vidText col-md-4">
            <p>
                Watch the video to the left for help getting started with Price Alert.
                In this video, one of the members of the Price Alert team will walk through account creation as well as tracking your first item.
            </p>
        </div>
    </div>
VIDEO;

	$date = date('l\, F jS\, Y');

	print <<<FOOTER
</div>
<footer>
    <div class="footer">
        <p>$date<br>Price Alert created by Blake Schmidt, Ben Luzarraga, and Kyle Gruber.</p>
    </div>
</footer>
FOOTER;


?>

</body>
</html>