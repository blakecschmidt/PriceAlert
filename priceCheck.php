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

$stmt_usernames = mysqli_prepare($connect, "SELECT username FROM USER");
mysqli_stmt_execute($stmt_usernames);
mysqli_stmt_close($stmt_usernames);

$result = mysqli_query($connect, $stmt_usernames);
$usernames = array();

while ($row = $result->fetch_row()) {
    $usernames[] = $row[0];
}
$result->free();

foreach ($usernames as $user) {
    $stmt_items = mysqli_prepare($connect, "SELECT itemID, itemName FROM itemToUser JOIN Item ON itemToUser.itemID = Item.itemID WHERE username = (?)");
    mysqli_stmt_bind_param($stmt, 's', $user);
    mysqli_stmt_execute($stmt_items);
    mysqli_stmt_close($stmt_items);

    $result = mysqli_query($connect, $stmt_items);
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

    foreach ($itemNameAndIDs as $itemName => $nameIDs) {
        $itemIDsToNotify = array();
        foreach ($nameIDs as $id) {
            $stmt_items = mysqli_prepare($connect, "SELECT itemName, alertPrice, retailer, url, currentPrice FROM Item JOIN itemToRetailer ON Item.itemID = itemToRetailer.itemID WHERE itemID = $id");
            mysqli_stmt_bind_param($stmt, 's', $user);
            mysqli_stmt_execute($stmt_items);
            mysqli_stmt_close($stmt_items);

            $row = $result->fetch_row();
            $itemName = $row[0];
            $alertPrice = (float) $row[1];
            $retailer = $row[2];
            $url = $row[3];
            $currentPrice = (float) $row[4];

            $getPriceArgs = $url . " " . $retailer;
            $price = (float) system("python3 getPrice.py " . $getPriceArgs , $retval);

            if ($currentPrice == NULL || $price != $currentPrice) {
                $stmt_price = mysqli_prepare($connect, "UPDATE itemToRetailer SET currentPrice = (?) WHERE itemID = (?)");
                mysqli_stmt_bind_param($stmt_price, 'ds', $price, $id);
                mysqli_stmt_execute($stmt_price);
                mysqli_stmt_close($stmt_price);
            }

            if ($price < $alertPrice) {
                
            }
        }
    }
}



mysqli_close($connect);

?>