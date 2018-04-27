<!DOCTYPE html>
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
                <li><a id="home" href="home.html">Home</a></li>
                <li><a href="myItems.php">My Items</a></li>
                <li><a href="myProfile.php">My Profile</a></li>
                <li><a href="contact.html">Contact Us</a></li>
                <li><a href="logIn.html">Log In</a></li>
            </ul>
        </div>
    </header>
    <div class="myProfile">
        <div class="myProfileSideBar">
            <ul>
                <li><button id="personalInfo">Personal Info</button></li>
                <li><button id="security">Security</button></li>
                <li><button id="settings">Settings</button></li>
            </ul>
        </div>
        <div class="htmlGrab">
<?php
    function redirect($url) {
    ob_start();
    header("Location: " . $url);
    ob_end_flush();
    die();
    }

    function pullPersonalInfo($userName){
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
    $table_u = "Users";

    $result = mysqli_query($connect, "SELECT password from $table_u WHERE UserName = '". $userName ."' ");
    $row = $result->fetch_row();
    return $row;
    }


    if (isset($_SESSION['user']) || isset($_COOKIE['username'])) {
        if (isset($_SESSION['user'])) {
        $userName = $_SESSION['user'];
        }
        else {
        $userName = $_COOKIE['username'];
        }

        $personalInfo = pullPersonalInfo($userName);
        print <<<PERSINFO
         <h1>Personal Info</h1>
                <form action = "" method = "post">
                    <table>
                        <tr>
                            <td><label for = "email">Email:</label></td>
                        </tr>
                        <tr>
                            <td><input type = "text" name = "email" id = "email" value = "$row[0]"></td>
                        </tr>
                        <tr>
                            <td><label for = "name">Name:</label></td>
                        </tr>
                        <tr>
                            <td><input type = "name" name = "name" id = "name"></td>
                        </tr>
                        <tr>
                            <td><input type = "submit" value = "Save"><input type = "reset" value = "Reset"></td>
                        </tr>
                    </table>
                </form>
PERSINFO;

    }
    else {
    redirect("logIn.php");
}
?>
        </div>
        </div>

    <footer>
        <div class="footer">
            <p>Friday, March 23rd, 2018.<br>Website created by Blake Schmidt, Ben Luzarraga, and Kyle Gruber.</p>
        </div>
    </footer>
    <script>
        var div = document.getElementsByClassName("htmlGrab")[0];
        var personalInfoButton = document.getElementById("personalInfo");
        var securityButton = document.getElementById("security");
        var settingsButton = document.getElementById("settings");

        personalInfoButton.onclick = function() {
            div.innerHTML = "<h1>Personal Info</h1><p>This is where personal information such as username and email will be displayed</p>"
        }

        securityButton.onclick = function() {
            div.innerHTML = "<h1>Security Info</h1><p>This is where security information such as changing the password will be displayed</p>"
        }

        settingsButton.onclick = function() {
            div.innerHTML = "<h1>Settings Info</h1><p>This is where settings information such as changing emails and frequency of email updates will be displayed</p>"
        }
    </script>
</body>
</html>