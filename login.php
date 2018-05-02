<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
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

if ($_POST['userName'] == "" || $_POST['passWord'] == ""){
    loginForm();
}
elseif ($_POST['userName'] != "" && $_POST['passWord'] != ""){
    verifyLogin();
}

function verifyLogin(){
    $host = "spring-2018.cs.utexas.edu";
    $user = "bcs2363";
    $pwd = "4fPUF78Nu~";
    $dbs = "cs329e_bcs2363";
    $port = "3306";

    $connect = mysqli_connect($host, $user, $pwd, $dbs, $port);

    if (empty($connect)) {
        die("mysqli_connect failed: " . mysqli_connect_error());
    }

    $table_u = "User";

    $userName = $_POST['userName'];
    $password = $_POST['passWord'];
    $stay = $_POST['stay'];
    $userDB = "none";
    $passDB = "none";

    $stmt = "SELECT * from $table_u WHERE username = '" . $userName . "'";

    $result = mysqli_query($connect, $stmt);
    while ($row = $result->fetch_row()) {
        $userDB = $row[0];
        $passDB = $row[2];
    }

    if($userDB != "none"){
        if (crypt($password, $passDB) == $passDB) {
            session_start();
            $_SESSION['username'] = $userName;
            if(sizeof($stay) == 1){
                setcookie("username", $userName, time()+3600*24*365);
            }
            redirect("home.php");
        }
        else{
            print("<p><b class='pageStrt'>Username or password incorrect.</b><a href='login.php'>Return to login form.</a></p>");
        }
    }
    else{
        print("<p><b class='pageStrt'>Username or password incorrect.</b><a href='login.php'>Return to login form.</a></p>");
    }
    mysqli_close($connect);
}

function loginForm()
{
    print <<<loginForm
    <div class="wrapper">
    	<div class="logIn">
			<form action = "" method = "post">
				<table>
					<tr>
						<td>User Name:</td>
					</tr>
					<tr>
						<td><input type = "text" name = "userName"></td>
					</tr>
					<tr>
						<td>Password:</td>
					</tr>
					<tr>
						<td><input type = "password" name = "passWord"></td>
					</tr>
					<tr>
						<td><input type = "submit" value = "Log In"><input type = "reset" value = "Reset"></td>
					</tr>
					<tr>
					    <td><input type="checkbox" name="stay[]">Keep me logged in</td>
                    </tr>
				</table>
			</form>
			<p><a href="signUp.php">Don't have an account? Sign up here.</a></p>
		</div>
	</div>
loginForm;
}

$date = date('l\, F jS\, Y');

print <<<FOOTER
<footer>
    <div class="footer">
        <p>$date<br>Price Alert created by Blake Schmidt, Ben Luzarraga, and Kyle Gruber.</p>
    </div>
</footer>
FOOTER;

?>

</body>
</html>
