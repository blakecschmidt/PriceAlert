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
            <li><a href="myItems.html">My Items</a></li>
            <li><a href="myProfile.html">My Profile</a></li>
            <li><a href="contact.html">Contact Us</a></li>
            <li><a href="logIn.html">Log In</a></li>
        </ul>
    </div>
</header>
<?php

if((isset($_POST['userName'])&& $_POST['userName'] != '')
    &&(isset($_POST['passWord']) && $_POST['passWord'] != '')
    &&(isset($_POST['email']) && $_POST['email'] != '')){
    verifySignUp();
}
else{
    signUpForm();
}

function verifySignUp(){
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
    $username = $_POST['userName'];
    $email = $_POST['email'];
    $password = crypt($_POST['passWord']);
    $repeatPassword = crypt($_POST['repeatPassWord']);
    $userValid = false;
    $passwordValid = false;
    $emailValid = filter_var($email, FILTER_VALIDATE_EMAIL);

    if (sizeof($username) >= 10 && sizeof($username) <= 20){
        if (preg_match("/^[a-zA-Z]+[a-zA-Z0-9]*$/", $username)){
            $userValid = true;
        }
    }
    if (sizeof($_POST['passWord']) >=6){
        if (preg_match("/((?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])(?=.{6,}))/", $_POST['passWord'])){
            if($password == $repeatPassword){
                $passwordValid = true;
            }
        }
    }
    if ($userValid == false || $passwordValid == false || $emailValid == false){
        print("username or password invalid.");
        print("<a href='signUp.php'>Return to sign up form</a>");
    }
    else{
        $result = mysqli_query($connect,
            "INSERT into $table_u (username, email, password) VALUES('" . $username . "', '" . $email . "', '" . $password . "')");
        print("Successfully registered for Price Alert");
        print("<a href='login.php'>Login</a>");
    }
}

function signUpForm(){
    print <<<signUp
        <div class="pageStrt">
            <div class="signUp">
                <form action = "userSignUp.php" method = "post">
                    <table>
                        <tr>
                            <td><label for = "userName">User Name:</label></td>
                        </tr>
                        <tr>
                            <td><input type = "text" name = "userName" id = "userName"></td>
                        </tr>
                        <tr>
                            <td><label for = "email">Email:</label></td>
                        </tr>
                        <tr>
                            <td><input type = "email" name = "email" id = "email"></td>
                        </tr>
                        <tr>
                            <td><label for = "passWord">Password:</label></td>
                        </tr>
                        <tr>
                            <td><input type = "text" name = "passWord" id = "passWord"></td>
                        </tr>
                        <tr>
                            <td><label for = "repeatPassWord">Repeat Password:</label></td>
                        </tr>
                        <tr>
                            <td><input type = "text" name = "repeatPassWord" id = "repeatPassWord"></td>
                        </tr>
                        <tr>
                            <td><input type = "submit" value = "Sign Up"><input type = "reset" value = "Reset"></td>
                        </tr>
                    </table>
                </form>
                <p><a href="logIn.html">Already have an account? Log in here.</a></p>
            </div>
            <div class="signUpInstructions">
                User name requirements:
                    <ul>
                        <li>10 through 20 characters in length</li>
                        <li>Only letters and digits</li>
                        <li>Cannot begin with a digit</li>
                    </ul>
                    Password requirements:
                    <ul>
                        <li>Must be at least 6 characters in length</li>
                        <li>Only letters and digits</li>
                        <li>Must have one lower case letter</li>
                        <li>Must have one upper case letter</li>
                        <li>Must have one digit</li>
                    </ul>
                
            </div>
        </div>
signUp;

}
?>
    <footer>
        <div class="footer">
            <p>Friday, March 23rd, 2018.<br>Website created by Blake Schmidt, Ben Luzarraga, and Kyle Gruber.</p>
        </div>
    </footer>
</body>
</html>
