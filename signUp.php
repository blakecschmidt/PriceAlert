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
            <li><a href="login.php">Log In</a></li>
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
    print("function called");
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
    $username = $_POST['userName'];
    $email = $_POST['email'];
    $password = crypt($_POST['passWord']);
    $repeatPassword = crypt($_POST['repeatPassWord']);
    $userValid = false;
    $passwordValid = false;
    $emailValid = filter_var($email, FILTER_VALIDATE_EMAIL);

    $stmt = "SELECT username from $table_u WHERE username = '" . $username . "'";

    $result = mysqli_query($connect, $stmt);
    while ($row = $result->fetch_row()) {
        $userDB = $row[0];
    }
    print($userDB);
    if ($userDB == $username){
        $userValid = false;
        print("user name striaght false");
    }
    elseif (sizeof($username) >= 10 && sizeof($username) <= 20){
        if (preg_match("/^[a-zA-Z]+[a-zA-Z0-9]*$/", $username)){
            $userValid = true;
            print("user name checked true");
        }
        print("user name checked false");
    }
    $result ->free();
    if (sizeof($_POST['passWord']) >= 6){
        if (preg_match('/((?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*])(?=.{6,}))/', $_POST['passWord'])){
            if($password == $repeatPassword){
                $passwordValid = true;
                print("password checked true");
            }
            print("password did not pass regex");
        }
        print("password did not pass size");

    }
    if ($userValid == false || $passwordValid == false || $emailValid == false){
        print("<p>Username or password invalid<p>");
        print("<a href='signUp.php'>Return to sign up form</a>");
    }
    else{
        $stmt2 = mysqli_prepare($connect, "INSERT into $table_u (username, email, password) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt2, 'sss', $username, $email, $password);
        mysqli_stmt_execute($stmt2);
        mysqli_stmt_close($stmt2);

        print "<p>Successfully registered for Price Alert<p>";
        print "<a href='login.php'>Login</a>";
    }
    mysqli_close($connect);
}

function signUpForm(){
    print <<<signUp
        <div class="wrapper">
            <div class="signUp">
                <form action = "" method = "post">
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
                            <td><input type = "password" name = "passWord" id = "passWord"></td>
                        </tr>
                        <tr>
                            <td><label for = "repeatPassWord">Repeat Password:</label></td>
                        </tr>
                        <tr>
                            <td><input type = "password" name = "repeatPassWord" id = "repeatPassWord"></td>
                        </tr>
                        <tr>
                            <td><input type = "submit" value = "Sign Up"><input type = "reset" value = "Reset"></td>
                        </tr>
                    </table>
                </form>
                <p><a href="login.php">Already have an account? Log in here.</a></p>
            </div>
            <div class="signUpInstructions2">
                User name requirements:
                    <ul>
                        <li>10 through 20 characters in length</li>
                        <li>Only letters and digits</li>
                        <li>Cannot begin with a digit</li>
                    </ul>
            </div>
            <div class="signUpInstructions1">
                    Password requirements:
                    <ul>
                        <li>Must be at least 6 characters in length</li>
                        <li>Only letters, digits, and special characters</li>
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
