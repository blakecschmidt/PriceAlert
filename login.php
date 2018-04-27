<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add An Item</title>
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
            <li><a href="login.php">Log In</a></li>
        </ul>
    </div>
</header>
<?php

if ($_POST['userName'] == "" || $_POST['passWord'] == ""){
    loginForm();
}
elseif ($_POST['userName'] != "" && $_POST['passWord'] != ""){
    verifyLogin();
}
function redirect($url) {
    ob_start();
    header("Location: " . $url);
    ob_end_flush();
    die();
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

    //TODO double check table name is correct
    $table_u = "User";

    $userName = $_POST['userName'];
    $password = crypt($_POST['passWord']);
    $stay = $_POST['stay'];

    $stmt1 = mysqli_prepare($connect, "SELECT * from $table_u WHERE username = (?)");
    mysqli_stmt_bind_param($stmt1, 's', $username);
    mysqli_stmt_execute($stmt1);
    mysqli_stmt_close($stmt1);

    $result = mysqli_query($connect, $stmt1);
    $row = $result->fetch_row();
    if(sizeof($row) == 3){
        if ($password == $row[2]){
            session_start();
            $_SESSION['username'] = $userName;
            if(sizeof($_POST['stay']) == 1){
                setcookie("username", $userName);
            }
            print("Logged in.");
            redirect("home.html");
        }
        else{
            print("<b>Username or password incorrect.</b>");
            print("<a href='login.php'>Return to login form.</a>");
        }
    }
    else{
        print("Username or password incorrect.");
        print("<a href='login.php'>Return to login form.</a>");
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
						<td><input type = "text" name = "passWord"></td>
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


?>
    <footer>
        <div class="footer">
            <p>Friday, March 23rd, 2018.<br>Website created by Blake Schmidt, Ben Luzarraga, and Kyle Gruber.</p>
        </div>
    </footer>
</body>
</html>
