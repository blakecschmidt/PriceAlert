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
        <a href="home.html"><img id="logo" src="logo.png" alt="Price Alert Logo"></a>
        <h1>Price Alert</h1>
    </div>

    <div class="navBar">
        <ul>
            <li><a id="home" href="home.html">Home</a></li>
            <li><a href="myItems.php">My Items</a></li>
            <li><a href="myProfile.php">My Profile</a></li>
            <li><a href="contact.html">Contact Us</a></li>
            <li><a href="login.php">Log In</a></li>
        </ul>
    </div>
</header>

<div class="myItemStrt">

<?php

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

$stmt1 = mysqli_prepare($connect, "SELECT itemName, alertPrice, retailer, url, currentPrice FROM Item JOIN itemToUser ON Item.itemID = itemToUser.itemID JOIN itemToRetailer ON Item.itemID = itemToRetailer.itemID WHERE username = ? ORDER BY itemName");
mysqli_stmt_bind_param($stmt1, 's', $username);
mysqli_stmt_execute($stmt1);
mysqli_stmt_close($stmt1);

$stmt2 = mysqli_prepare($connect, "SELECT itemName, COUNT(*) FROM Item JOIN itemToUser ON Item.itemID = itemToUser.itemID JOIN itemToRetailer ON Item.itemID = itemToRetailer.itemID WHERE username = ? GROUP BY itemName ORDER BY itemName");
mysqli_stmt_bind_param($stmt2, 's', $username);
mysqli_stmt_execute($stmt2);
mysqli_stmt_close($stmt2);

$result1 = mysqli_query($connect, $stmt1);
$itemInfo = array();

while ($row = $result1->fetch_row()) {
    $itemInfo[] = array(row[0], row[1], row[2], row[3], row[4]);
}
$result1->free();

$result2 = mysqli_query($connect, $stmt2);
$itemCount = array();

while ($row = $result2->fetch_row()) {
    $itemCount[] = array(row[0], row[1]);
}
$result2->free();

mysqli_close($connect);
$count = 0;

print <<<TABLE
<form method = "post" action = "addItem.php">
    <td><input type="submit" value="Add Item"></td>
</form>
TABLE;

for ($i = 0; $i < sizeof($itemCount); $i++) {
    print <<<TABLE
<table class="myItem">
    <tr>
        <th>$itemCount[$i][0]</th>
        <th>Current Price</th>
        <th>Alert Price</th>
        <th>Link</th>
    </tr>
TABLE;

    for ($j = 0; $j < $itemCount[$i][1]; $j++) {
        print <<<TABLE
    <tr>
        <td>$itemInfo[$count][2]</td>
        <td>$itemInfo[$count][4]</td>
        <td>$itemInfo[$count][1]</td>
        <td>$itemInfo[$count][3]</td>
    </tr>
TABLE;
        $count++;
    }
    print <<<TABLE
    <tr>
        <form method = "post" action = "editItem.php">
            <input type="hidden" name="itemName" id="itemName" value=$itemCount[$i][0]>
            <td><input type="submit" value="Edit Item"></td>
        </form>
        
        <form method = "post" action = "deleteItem.php">
            <input type="hidden" name="itemName" id="itemName" value=$itemCount[$i][0]>
            <td><input type="submit" value="Delete Item"></td>
        </form>
    </tr>
</table>
TABLE;

}

?>
</div>

<footer>
    <div class="footer">
        <p>Friday, March 23rd, 2018.<br>Website created by Blake Schmidt, Ben Luzarraga, and Kyle Gruber.</p>
    </div>
</footer>
</body>
</html>

