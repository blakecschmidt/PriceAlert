<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add An Item</title>
    <link rel = "stylesheet" type = "text/css" href = "./style.css" media = "all">
</head>
<body>

<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_POST["email"]) && $_POST["email"] == "Yes") {
    email();
} elseif (isset($_POST["price"]) && $_POST["price"] == "Yes") {
    price();
} elseif (isset($_POST["check"]) && $_POST["check"] == "Yes") {
    priceCheck();
}

function price() {
    $url = "https://www.amazon.com/Xbox-One-X-1TB-Console/dp/B074WPGYRF/ref=sr_1_3?s=videogames&ie=UTF8&qid=1525038636&sr=1-3&keywords=xbox%2Bone%2Bx&th=1";
    $retailer = "Amazon";
    //$getPriceArgs = "'" . $url . "' '" . $retailer . "'";
    //$price = system("/usr/local/bin/python3 /projects/coursework/2018-spring/cs329e-mitra/bcs2363/PriceAlert/getPrice.py '" . $url . "' '" . $retailer . "'", $retval);
    //exec("/usr/local/bin/python3 /projects/coursework/2018-spring/cs329e-mitra/bcs2363/PriceAlert/getPrice.py", $output, $ret_code);
    $price = system("/usr/local/bin/python3 /projects/coursework/2018-spring/cs329e-mitra/bcs2363/PriceAlert/getPrice.py", $retval);
    print "Price: " . $price;
    print "Retval: " . $retval;
}

function email() {
    $emailInfo = array();
    $emailInfo[] = "Xbox";
    $emailInfo[] = array("Amazon", "Dell", "Best Buy");
    $emailInfo[] = array("400.00", "425.23", "500");
    $emailInfo[] = array("https://www.amazon.com/Xbox-One-X-1TB-Console/dp/B074WPGYRF/ref=sr_1_3?s=videogames&ie=UTF8&qid=1524680613&sr=1-3&keywords=xbox+one+x", "url", "url");
    $emailInfo[] = array("pricealertnotify@gmail.com", '3qKL^yoc*,Aq6ZmH$rDn', "smtp.gmail.com:587");
    $emailInfo[] = "blakecschmidt@gmail.com";

    $sendEmailArgs = $emailInfo[0] . " " . $emailInfo[1] . " " . $emailInfo[2] . " " . $emailInfo[3] . " " . $emailInfo[4] . " " . $emailInfo[5];
    $sendEmail = system("python3 sendEmail.py " . $sendEmailArgs, $retval);
}

function priceCheck()
{
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
                $result = mysqli_query($connect, "SELECT itemName, alertPrice, retailer, url, currentPrice FROM Item JOIN itemToRetailer ON Item.itemID = itemToRetailer.itemID WHERE Item.itemID = '" . $id . "'");

                $row = $result->fetch_row();
                $itemName = $row[0];
                $alertPrice = (float)$row[1];
                $retailer = $row[2];
                $url = $row[3];
                $currentPrice = (float)$row[4];
                $result->free();

                print "itemName: " . $itemName . "<br>";
                print "alertPrice: " . $alertPrice . "<br>";
                print "retailer: " . $retailer . "<br>";
                print "url: " . $url . "<br>";
                print "currentPrice: " . $currentPrice . "<br>";

                //Checked to here

                $json_array = array(
                        "0" => array(
                                "username"
                        )
                );

                /*$getPriceArgs = $url . " " . $retailer;
                $price = (float)system
                ("python3 getPrice.py " . $getPriceArgs, $retval);

                print "Price: " . $price . "<br>";

                if ($currentPrice == NULL || $currentPrice == 0 || $price != $currentPrice) {
                    $stmt_price = mysqli_prepare($connect, "UPDATE itemToRetailer SET currentPrice = (?) WHERE itemID = (?)");
                    mysqli_stmt_bind_param($stmt_price, 'ds', $price, $id);
                    mysqli_stmt_execute($stmt_price);
                    mysqli_stmt_close($stmt_price);
                }

                if ($price < $alertPrice) {
                    $emailInfo[1][] = $retailer;
                    $emailInfo[2][] = $price;
                    $emailInfo[3][] = $url;
                }*/
            }

            //$sendEmailArgs = $emailInfo[0] . " " . $emailInfo[1] . " " . $emailInfo[2] . " " . $emailInfo[3] . " " . $emailInfo[4] . " " . $emailInfo[5];
            //$sendEmail = system("python3 sendEmail.py " . $sendEmailArgs, $retval);
        }
    }

    mysqli_close($connect);
}

print <<<FORM
<form method = "post" action = "">
<input type="hidden" name="check" id="check" value="Yes">
<input type = "submit" value = "Check">
</form>

<form method = "post" action = "">
<input type="hidden" name="price" id="price" value="Yes">
<input type = "submit" value = "Price">
</form>

<form method = "post" action = "">
<input type="hidden" name="email" id="email" value="Yes">
<input type = "submit" value = "Email">
</form>
FORM;


?>

</body>
</html>
