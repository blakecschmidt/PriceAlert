<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delete An Item</title>
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
            <li><a href="myItems.html">My Items</a></li>
            <li><a href="myProfile.html">My Profile</a></li>
            <li><a href="contact.html">Contact Us</a></li>
            <li><a href="logIn.html">Log In</a></li>
        </ul>
    </div>
</header>
<h1>Delete An Item</h1>

<?php

if (isset($_POST)) {
    edit();
} else {
    editForm();
}

function edit()
{

    $host = "spring-2018.cs.utexas.edu";
    $user = "bcs2363";
    $pwd = "4fPUF78Nu~";
    $dbs = "cs329e_bcs2363";
    $port = "3306";

    $connect = mysqli_connect($host, $user, $pwd, $dbs, $port);

    if (empty($connect)) {
        die("mysqli_connect failed: " . mysqli_connect_error());
    }

    $table_item = "Item";
    $table_itu = "itemToUser";
    $table_itr = "itemToRetailer";
    $username = $_COOKIE["username"];
    $itemName = $_POST["itemName"];

    $allIDs = $_POST["allIDs"];
    $retailers = $_POST["retailer"];

    $amazonURL = $_POST["amazonURL"];
    $bestbuyURL = $_POST["bestbuyURL"];
    $dellURL = $_POST["dellURL"];
    $walmartURL = $_POST["walmartURL"];
    $targetURL = $_POST["targetURL"];

    $itemIDsAndURLsToEdit = array();
    $itemRetailersAndURLsToInsert = array();

    if ($_POST["alertPrice"] != "" && $_POST["alertPrice"] != 0) {
        $alertPrice = $_POST["alertPrice"];
    } else {
        $alertPrice = $_POST["alertPriceCurrent"];
    }

    for ($i = 0; $i < sizeof($retailers); $i++) {
        if ($retailers[$i] == "Amazon" && $amazonURL != "") {
            $itemIDsAndURLsToEdit[] = array($_POST["amazonID"], $amazonURL);
        } elseif ($retailers[$i] == "Amazon" && $amazonURL == "") {
            $itemRetailersAndURLsToInsert[] = array($retailers[$i], $amazonURL);
        } elseif ($retailers[$i] == "Best Buy" && $bestbuyURL != "") {
            $itemIDsAndURLsToEdit[] = array($_POST["bestbuyID"], $bestbuyURL);
        } elseif ($retailers[$i] == "Best Buy" && $bestbuyURL == "") {
            $itemRetailersAndURLsToInsert[] = array($retailers[$i], $bestbuyURL);
        } elseif ($retailers[$i] == "Dell" && $dellURL != "") {
            $itemIDsAndURLsToEdit[] = array($_POST["dellID"], $dellURL);
        } elseif ($retailers[$i] == "Dell" && $dellURL == "") {
            $itemRetailersAndURLsToInsert[] = array($retailers[$i], $dellURL);
        } elseif ($retailers[$i] == "Walmart" && $walmartURL != "") {
            $itemIDsAndURLsToEdit[] = array($_POST["walmartID"], $walmartURL);
        } elseif ($retailers[$i] == "Walmart" && $walmartURL == "") {
            $itemRetailersAndURLsToInsert[] = array($retailers[$i], $walmartURL);
        } elseif ($retailers[$i] == "Target" && $targetURL != "") {
            $itemIDsAndURLsToEdit[] = array($_POST["targetID"], $targetURL);
        } elseif ($retailers[$i] == "Target" && $targetURL == "") {
            $itemRetailersAndURLsToInsert[] = array($retailers[$i], $targetURL);
        }
    }

    foreach ($itemIDsAndURLsToEdit as $edit) {
        $stmt1 = mysqli_prepare($connect, "UPDATE $table_item SET alertPrice = (?) WHERE itemID = (?)");
        mysqli_stmt_bind_param($stmt1, 'ss', $alertPrice, $edit[0]);
        mysqli_stmt_execute($stmt1);
        mysqli_stmt_close($stmt1);

        $stmt2 = mysqli_prepare($connect, "UPDATE $table_itr SET url = (?) WHERE itemID = (?)");
        mysqli_stmt_bind_param($stmt2, 'ss', $edit[1],$edit[0]);
        mysqli_stmt_execute($stmt2);
        mysqli_stmt_close($stmt2);
    }

    foreach ($itemRetailersAndURLsToInsert as $insert) {
        $stmt1 = mysqli_prepare($connect, "INSERT INTO $table_item (itemName, alertPrice) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt1, 'ss', $itemName, $alertPrice);
        mysqli_stmt_execute($stmt1);
        mysqli_stmt_close($stmt1);

        $stmt2 = mysqli_prepare($connect, "INSERT INTO $table_itu (username) VALUES (?)");
        mysqli_stmt_bind_param($stmt2, 's', $username);
        mysqli_stmt_execute($stmt2);
        mysqli_stmt_close($stmt2);

        $stmt3 = mysqli_prepare($connect, "INSERT INTO $table_itr (retailer, url, currentPrice) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt3, 'sss', $insert[0], $insert[1], NULL);
        mysqli_stmt_execute($stmt3);
        mysqli_stmt_close($stmt3);
    }

    print "<p>The properties you changed have now been edited.</p>";

    mysqli_close($connect);

    print "<a href='myItems.html'>Back to My Items</a>";

}

function editForm()
{
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
    $username = $_COOKIE["username"];

    $stmt = mysqli_prepare($connect, "SELECT retailer, url, itemID, alertPrice FROM Item JOIN itemToUser ON Item.itemID = itemToUser.itemID JOIN itemToRetailer ON Item.itemID = itemToRetailer.itemID WHERE username = ? AND itemName = ?");
    mysqli_stmt_bind_param($stmt, 'ss', $username, $itemName);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $result = mysqli_query($connect, $stmt);

    $retailers = array();
    $urls = array();
    $itemIDs = array();
    $alertPrices = array();

    while ($row = $result->fetch_row()) {
        $retailers[] = $row[0];
        $urls[] = $row[1];
        $itemIDs[] = $row[2];
        $alertPrices[] = $row[3];
    }
    $result->free();

    mysqli_close($connect);

    $alertPrice = $alertPrices[0];
    $amazonURL = "";
    $bestbuyURL = "";
    $dellURL = "";
    $walmartURL = "";
    $targetURL = "";
    $amazonID = "";
    $bestbuyID = "";
    $dellID = "";
    $walmartID = "";
    $targetID = "";

    for ($i = 0; $i < sizeof($retailers); $i++) {
        if ($retailers[$i] == "Amazon") {
            $amazonURL = $urls[$i];
            $amazonID = $itemIDs[$i];
        } elseif ($retailers[$i] == "Best Buy") {
            $bestbuyURL = $urls[$i];
            $bestbuyID = $itemIDs[$i];
        } elseif ($retailers[$i] == "Dell") {
            $dellURL = $urls[$i];
            $dellID = $itemIDs[$i];
        } elseif ($retailers[$i] == "Walmart") {
            $walmartURL = $urls[$i];
            $walmartID = $itemIDs[$i];
        } elseif ($retailers[$i] == "Target") {
            $targetURL = $urls[$i];
            $targetID = $itemIDs[$i];
        }
    }

    print <<<FORM
<form id = "editForm" method = "post" action = "">

<table>

    <tr>
        <td>Please check a retailer box to indicate that you would like to edit the URL or add that listing. Changing the alert price will affect all listings. Checked boxes with blank URLs, blank alert price boxes and alert prices of zero will not be accepted.</td>
    </tr>

    <tr>
        <td><label for="itemName">Item Name: </label></td><td><input type="text" name="itemName" id="itemName" placeholder=$itemName readonly></td>
    </tr>
    
    <tr>
        <td><label for="alertPrice">Alert Price: </label></td><td><input type="text" name="alertPrice" id="alertPrice" placeholder=$alertPrice></td>
    </tr>
    
    <tr>
        <td><input type="checkbox" name="retailer[]" value="Amazon" id="amazon"></td><td><label for="amazon"> Amazon</label></td>
        <td><input type="text" name="amazonURL" id="amazonURL" placeholder=$amazonURL></td>
    </tr>
    
    <tr>
        <td><input type="checkbox" name="retailer[]" value="Best Buy" id="bestbuy"></td><td><label for="bestbuy"> Best Buy</label></td>
        <td><input type="text" name="bestbuyURL" id="bestbuyURL" placeholder=$bestbuyURL></td>
    </tr>
    
    <tr>
        <td><input type="checkbox" name="retailer[]" value="Dell" id="dell"></td><td><label for="dell"> Dell</label></td>
        <td><input type="text" name="dellURL" id="dellURL" placeholder=$dellURL></td>
    </tr>
    
    <tr>
        <td><input type="checkbox" name="retailer[]" value="Walmart" id="walmart"></td><td><label for="walmart"> Walmart</label></td>
        <td><input type="text" name="walmartURL" id="walmartURL" placeholder=$walmartURL></td>
    </tr>
    
    <tr>
        <td><input type="checkbox" name="retailer[]" value="Target" id="target"></td><td><label for="target"> Target</label></td>
        <td><input type="text" name="targetURL" id="targetURL" placeholder=$targetURL></td>
    </tr>
    
    <input type="hidden" name="allIDs" id="allIDs" value=$itemIDs>
    <input type="hidden" name="itemName" id="itemName" value=$itemName>
    <input type="hidden" name="alertPriceCurrent" id="alertPriceCurrent" value=$alertPrice>
    <input type='hidden' name='amazonID' id='amazonID' value=$amazonID>
    <input type='hidden' name='bestbuyID' id='bestbuyID' value=$bestbuyID>
    <input type='hidden' name='dellID' id='dellID' value=$dellID>
    <input type='hidden' name='walmartID' id='walmartID' value=$walmartID>
    <input type='hidden' name='targetID' id='targetID' value=$targetID>
    
    <tr><td><input type="submit" value="Submit"></td></tr>
    <tr><td><input type="reset" value="Clear"></td></tr>
</table>
</form>
FORM;

}


?>

<footer>
    <div class="footer">
        <p>Friday, March 23rd, 2018.<br>Website created by Blake Schmidt, Ben Luzarraga, and Kyle Gruber.</p>
    </div>
</footer>
</body>
</html>