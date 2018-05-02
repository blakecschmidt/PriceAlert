<?php

session_start();

function redirect($url) {
    ob_start();
    header("Location: " . $url);
    ob_end_flush();
    die();
}

if (isset($_COOKIE["username"])) {
    unset($_COOKIE["username"]);
    setcookie('username', '', time() - 3600);
}

session_unset();
session_destroy();
redirect("./home.php");
?>
