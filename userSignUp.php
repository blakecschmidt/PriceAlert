<?php
  # get the incoming information
  extract ($_POST);
  $userName = $_POST["userName"];
  $passWord = $_POST["passWord"];

  $userNameAlreadyExists = false;

  # open file 'users.txt' and check if userName already exists
  if ($file = fopen ("users.txt", "r"))
  {
    while(!feof($file))
    {
      $line = fgets($file);

      $commaIdx = strpos($line, ",") - 1;
      $fileUserName = subStr($line, 0, $commaIdx);

      if ($userName == $fileUserName) {
        $userNameAlreadyExists = true;
        break;
      }
    }
    fclose($file);
  }

  # open file 'users.txt' and append the name and e-mail address
  if (($file = fopen ("users.txt", "a")) && !$userNameAlreadyExists)
  {
    fwrite ($file, "$userName , $passWord \n");
    fclose ($file);
  }

  function redirect($url) {
    ob_start();
    header("Location: " . $url);
    ob_end_flush();
    die();
  }

  redirect("logIn.html");
?>