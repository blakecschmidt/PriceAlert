<?php

$host = "spring-2018.cs.utexas.edu";
$user_db = "bcs2363";
$pwd = "4fPUF78Nu~";
$dbs = "cs329e_bcs2363";
$port = "3306";

$connect = mysqli_connect($host, $user_db, $pwd, $dbs, $port);

if (empty($connect)) {
    die("mysqli_connect failed: " . mysqli_connect_error());
}

$result = mysqli_query($connect, "SELECT username FROM User");
$usernames = array();

while ($row = $result->fetch_row()) {
    $usernames[] = $row[0];
}
$result->free();

//For each user
foreach ($usernames as $user) {
    $result = mysqli_query($connect, "SELECT Item.itemID, itemName FROM itemToUser JOIN Item ON itemToUser.itemID = Item.itemID WHERE username = '" . $user . "'");
    $itemNameAndIDs = array();

    while ($row = $result->fetch_row()) {
        if (array_key_exists($row[1], $itemNameAndIDs)) {
            $itemNameAndIDs[$row[1]][] = $row[0];
        } else {
            $itemNameAndIDs[$row[1]] = array();
            $itemNameAndIDs[$row[1]][] = $row[0];
        }
    }
    $result->free();

    $result = mysqli_query($connect, "SELECT email FROM User WHERE username = '" . $user . "'");
    $row = $result->fetch_row();
    $email = $row[0];
    $result->free();

    //For each set of itemIDs matched to each item of the user
    foreach ($itemNameAndIDs as $itemName => $nameIDs) {
        $itemIDsToNotify = array();
        //itemName, retailers, prices, urls, senderEmail, destEmail
        $emailInfo = array($itemName, array(), array(), array(), array("pricealertnotify@gmail.com", '3qKL^yoc*,Aq6ZmH$rDn', "smtp.gmail.com:587"), $email);
        //For each ID in set of IDs
        foreach ($nameIDs as $id) {
            $result = mysqli_query($connect, "SELECT itemName, alertPrice, retailer, url, currentPrice FROM Item JOIN itemToRetailer ON Item.itemID = itemToRetailer.itemID WHERE itemID = '" . $id . "'");

            $row = $result->fetch_row();
            $itemName = $row[0];
            $alertPrice = (float) $row[1];
            $retailer = $row[2];
            $url = $row[3];
            $currentPrice = (float) $row[4];
            $result->free();

            $getPriceArgs = $url . " " . $retailer;
            $price = (float) system("python3 getPrice.py " . $getPriceArgs , $retval);

            if ($currentPrice == NULL || $price != $currentPrice) {
                $stmt_price = mysqli_prepare($connect, "UPDATE itemToRetailer SET currentPrice = (?) WHERE itemID = (?)");
                mysqli_stmt_bind_param($stmt_price, 'ds', $price, $id);
                mysqli_stmt_execute($stmt_price);
                mysqli_stmt_close($stmt_price);
            }

            if ($price < $alertPrice) {
                $emailInfo[1][] = $retailer;
                $emailInfo[2][] = $price;
                $emailInfo[3][] = $url;
            }
        }

        $sendEmailArgs = $emailInfo[0] . " " . $emailInfo[1] . " " . $emailInfo[2] . " " . $emailInfo[3] . " " . $emailInfo[4] . " " . $emailInfo[5];
        $sendEmail = system("python3 sendEmail.py " . $sendEmailArgs , $retval);
    }
}

mysqli_close($connect);

?>