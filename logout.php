<?php

function redirect($url) {
    ob_start();
    header("Location: " . $url);
    ob_end_flush();
    die();
    }

setcookie("userName", "", time()-3600);
session_unset();
session_destroy();
redirect("login.php");
?>