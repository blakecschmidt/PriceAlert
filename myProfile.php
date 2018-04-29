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
                <li><a href="login.html">Log In</a></li>
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


    if (isset($_SESSION['username']) || isset($_COOKIE['username'])) {
        if (isset($_SESSION['username'])) {
        $userName = $_SESSION['username'];
        }
        else {
        $userName = $_COOKIE['username'];
        }

        $userEmail = pullEmail($userName);

        if (isset($_POST['email']) && $_POST['email'] != ''){
            if ($_POST['email'] != $userEmail) {
                setEmail($userName, $_POST['email']);
            }
            redirect("myProfile.php");
        }
        else {
        print <<<PERSINFO
         <h1>Personal Info</h1>
                <form action = "" method = "post">
                    <table>
                        <tr>
                            <td><label for = "email">Email:</label></td>
                        </tr>
                        <tr>
                            <td><input type = "text" name = "email" id = "email" value = "$userEmail"></td>
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

    }
    else {
    redirect("login.php");
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
            div.innerHTML = "test"
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