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

    function redirect($url) {
    ob_start();
    header("Location: " . $url);
    ob_end_flush();
    die();
    }

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

print <<<H1
<div class="myProfile">
        <div class="myProfileSideBar">
            <ul>
                <li><button id="personalInfo">Personal Info</button></li>
                <li><button id="security">Security</button></li>
                <li><button id="settings">Settings</button></li>
            </ul>
        </div>
        <div class="htmlGrab">
H1;

    function pullEmail($userName){
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
        $table_u = "User";

        $result = mysqli_query($connect, "SELECT email from $table_u WHERE username = '". $userName ."' ");
        $row = $result->fetch_row();
        return $row[0];
    }

    function pullPassword($userName){
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
        $table_u = "User";

        $result = mysqli_query($connect, "SELECT password from $table_u WHERE username = '". $userName ."' ");
        $row = $result->fetch_row();
        return $row[0];
    }

    function setEmail($userName, $userEmail){
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
        $table_u = "User";

        $qry = "UPDATE $table_u SET email = '".$userEmail."' WHERE username = '". $userName ."'";
        $stmt = mysqli_prepare($connect, $qry);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($connect);
    }

    function setPassword($userName, $password){
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
        $table_u = "User";

        $qry = "UPDATE $table_u SET password = '".$password."' WHERE username = '". $userName ."'";
        $stmt = mysqli_prepare($connect, $qry);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($connect);
    }


    if (isset($_SESSION['username']) || isset($_COOKIE['username'])) {
        if (isset($_SESSION['username'])) {
        $userName = $_SESSION['username'];
        }
        else {
        $userName = $_COOKIE['username'];
        }

        $userEmail = pullEmail($userName);
        $userPassword = pullPassword($userName);

        if (isset($_POST['newEmail']) && $_POST['newEmail'] != '') {
            if ($_POST['newEmail'] != $userEmail) {
                setEmail($userName, $_POST['newEmail']);
            }
            redirect("myProfile.php");
        }
        elseif ((isset($_POST['oldPassword']) && $_POST['oldPassword'] != '') && (isset($_POST['newPassword']) && $_POST['newPassword'] != '')){
            //!!!!!!
            if (crypt($_POST['oldPassword'], $userPassword) == $userPassword) {
                setPassword($userName, crypt($_POST['newPassword']));
            }
            redirect("myProfile.php");
        }
        else {
        print <<<INFO
        <div class = "myPersonalInfo">
        <h1>Personal Info</h1>
                <form action = "" method = "post">
                    <table>
                        <tr>
                            <td><label for = "oldEmail">Old Email:</label></td>
                        </tr>
                        <tr>
                            <td><input type = "text" name = "oldEmail" id = "oldEmail" value = "$userEmail"></td>
                        </tr>
                        <tr>
                            <td><label for = "newEmail">New Email:</label></td>
                        </tr>
                        <tr>
                            <td><input type = "text" name = "newEmail" id = "newEmail" maxlength = "40"></td>
                        </tr>
                        <tr>
                            <td><input type = "submit" value = "Save"><input type = "reset" value = "Reset"></td>
                        </tr>
                    </table>
                </form>
        </div>
        <div class = "mySecurity" style="display:none">
        <h1>Security Info</h1>
            <form action = "" method = "post">
                    <table>
                        <tr>
                            <td><label for = "oldPassword">Old Password:</label></td>
                        </tr>
                        <tr>
                            <td><input type = "password" name = "oldPassword" id = "oldPassword""></td>
                        </tr>
                        <tr>
                            <td><label for = "newPassword">New Password:</label></td>
                        </tr>
                        <tr>
                            <td><input type = "password" name = "newPassword" id = "newPassword""></td>
                        </tr>
                        <tr>
                            <td><input type = "submit" value = "Save"><input type = "reset" value = "Reset"></td>
                        </tr>
                    </table>
                </form>
        </div>
        <div class = "mySettings" style="display:none">
            <h1>Settings Info</h1>
            <p>This is where settings information such as frequency of email updates will be displayed</p>
        </div>

INFO;
    }

    }
    else {
    redirect("login.php");
    return;
}

print "</div>";
print "</div>";

$date = date('l\, F jS\, Y');

print <<<FOOTER
<footer>
    <div class="footer">
        <p>$date<br>Price Alert created by Blake Schmidt, Ben Luzarraga, and Kyle Gruber.</p>
    </div>
</footer>
FOOTER;

?>

<script>
    var myPersonalInfoDiv = document.getElementsByClassName("myPersonalInfo")[0];
    var mySecurityDiv = document.getElementsByClassName("mySecurity")[0];
    var mySettingsDiv = document.getElementsByClassName("mySettings")[0];
    var personalInfoButton = document.getElementById("personalInfo");
    var securityButton = document.getElementById("security");
    var settingsButton = document.getElementById("settings");

    personalInfoButton.onclick = function() {
        myPersonalInfoDiv.style.display = "block";
        mySecurityDiv.style.display = "none";
        mySettingsDiv.style.display = "none";
    };

    securityButton.onclick = function() {
        myPersonalInfoDiv.style.display = "none";
        mySecurityDiv.style.display = "block";
        mySettingsDiv.style.display = "none";
    };

    settingsButton.onclick = function() {
        myPersonalInfoDiv.style.display = "none";
        mySecurityDiv.style.display = "none";
        mySettingsDiv.style.display = "block";
    };
</script>

</body>
</html>