<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Items</title>
    <link rel = "stylesheet" type = "text/css" href = "./style.css" media = "all">
</head>
<body>
<header>
    <div class="header">
        <a href="home.php"><img id="logo" src="logo.png" alt="Price Alert Logo"></a>
        <h1>Price Alert</h1>
    </div>

    <div class="navBar">
        <ul>
            <li><a id="home" href="home.php">Home</a></li>
            <li><a href="myItems.php">My Items</a></li>
            <li><a href="myProfile.php">My Profile</a></li>
            <li><a href="contact.html">Contact Us</a></li>
            <li><a href="login.php">Log In</a></li>
        </ul>
    </div>
</header>

<div class="myItemsStrt">

<?php
session_start();

print <<<TABLE
<form class="addItemBtn" method = "post" action = "addItem.php">
    <td><input type="submit" value="Add Item"></td>
</form>
TABLE;

$host = "spring-2018.cs.utexas.edu";
$user = "bcs2363";
$pwd = "4fPUF78Nu~";
$dbs = "cs329e_bcs2363";
$port = "3306";

$connect = mysqli_connect($host, $user, $pwd, $dbs, $port);

if (empty($connect)) {
    die("mysqli_connect failed: " . mysqli_connect_error());
}

$itemName = "";
if (isset($_COOKIE["username"])) {
    $username = $_COOKIE["username"];
} else {
    $username = $_SESSION["username"];
}

$result1 = mysqli_query($connect, "SELECT itemName, alertPrice, retailer, url, currentPrice FROM Item JOIN itemToUser ON Item.itemID = itemToUser.itemID JOIN itemToRetailer ON Item.itemID = itemToRetailer.itemID WHERE username = '" . $username . "' ORDER BY itemName");
$itemInfo = array();

while ($row = $result1->fetch_row()) {
    $itemInfo[] = array($row[0], $row[1], $row[2], $row[3], $row[4]);
}
$result1->free();

$result2 = mysqli_query($connect, "SELECT itemName, COUNT(*) FROM Item JOIN itemToUser ON Item.itemID = itemToUser.itemID JOIN itemToRetailer ON Item.itemID = itemToRetailer.itemID WHERE username = '" . $username . "' GROUP BY itemName ORDER BY itemName");
$itemCount = array();

while ($row = $result2->fetch_row()) {
    $itemCount[] = array($row[0], $row[1]);
}
$result2->free();

mysqli_close($connect);
$count = 0;

for ($i = 0; $i < sizeof($itemCount); $i++) {
    $itemHeaderName = $itemCount[$i][0];

    print <<<TABLE
<table class="myItem">
    <tr>
        <th>$itemHeaderName</th>
        <th>Current Price</th>
        <th>Alert Price</th>
        <th>Link</th>
    </tr>
TABLE;

    for ($j = 0; $j < $itemCount[$i][1]; $j++) {
        $itemTableRetailer = $itemInfo[$count][2];
        $itemTableCurrentPrice = $itemInfo[$count][4];
        $itemTableAlertPrice = $itemInfo[$count][1];
        $itemTableURL = $itemInfo[$count][3];

        if ($itemTableCurrentPrice == null) {
            $itemTableCurrentPrice = "Not Checked Yet";
        }

        print <<<TABLE
    <tr>
        <td>$itemTableRetailer</td>
        <td>$itemTableCurrentPrice</td>
        <td>$itemTableAlertPrice</td>
        <td><a href=$itemTableURL>Link to Store</a></td>
    </tr>
TABLE;
        $count++;
    }
    $itemTableID = $itemCount[$i][0];

    print <<<TABLE
    <tr>
        <form method = "post" action = "editItem.php">
            <input type="hidden" name="itemName" id="itemName" value=$itemTableID>
            <td><input type="submit" value="Edit Item"></td>
        </form>
        
        <form method = "post" action = "deleteItem.php">
            <input type="hidden" name="itemName" id="itemName" value=$itemTableID>
            <td><input type="submit" value="Delete Item"></td>
        </form>
    </tr>
</table>
<br><br>
TABLE;

}

?>
</div>

<footer class="myItemsFooter">
    <div>
        <p>Friday, March 23rd, 2018.<br>Website created by Blake Schmidt, Ben Luzarraga, and Kyle Gruber.</p>
    </div>
</footer>
</body>
</html>

