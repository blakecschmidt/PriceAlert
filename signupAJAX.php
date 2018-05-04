<?php

$username = $_POST["userName"];

$stmt = "SELECT username from User WHERE username = '" . $username . "'";
$userDB = "none";
$result = mysqli_query($connect, $stmt);

$row = $result->fetch_row();
$userDB = $row[0];

if ($userDB != "none") {
    $match = true;
}

if ($match == true) {
    echo "Username Taken";
}

?>