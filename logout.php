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
}
session_unset();
session_destroy();
redirect("login.php");
?>