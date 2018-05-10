<?php
$host = "spring-2018.cs.utexas.edu";
$user = "bcs2363";
$pwd = "4fPUF78Nu~";
$dbs = "cs329e_bcs2363";
$port = "3306";

$connect = mysqli_connect($host, $user, $pwd, $dbs, $port);

$username = $_POST["username"];

$stmt = "SELECT username from User WHERE username = '" . $username . "'";
$userDB = "none";
$result = mysqli_query($connect, $stmt);

while ($row = $result->fetch_row()) {
    $userDB = $row[0];
}
mysqli_close($connect);

if ($userDB != "none") {
    echo "Username taken";
}

?>