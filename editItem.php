<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit An Item</title>
    <link rel = "stylesheet" type = "text/css" href = "./style.css" media = "all">
</head>
<body>

<?php
session_start();

function redirect($url) {
    ob_start();
    header("Location: " . $url);
    ob_end_flush();
    die();
}

if (!isset($_SESSION["username"]) && !isset($_COOKIE["username"])) {
    print <<<HEADER
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
            <li><a href="contact.php">Contact Us</a></li>
            <li><a href="login.php">Log In</a></li>
        </ul>
    </div>
</header>
HEADER;
} else {
    print <<<HEADER
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
            <li><a href="contact.php">Contact Us</a></li>
            <li><a href="logout.php">Log Out</a></li>
        </ul>
    </div>
</header>
HEADER;
}

print <<<H1
<div class="editItems">
<h1>Edit An Item</h1>
H1;


if (!isset($_COOKIE["username"]) && !isset($_SESSION["username"])) {
    redirect("./login.php");
    return;
}

if (isset($_POST["alertPriceCurrent"])) {
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
    if (isset($_COOKIE["username"])) {
        $username = $_COOKIE["username"];
    } else {
        $username = $_SESSION["username"];
    }
    $itemName = $_POST["itemName"];

    $allIDs = $_POST["allIDs"];
    $retailers = $_POST["retailer"];

    $amazonURLOld = $_POST["amazonURLOld"];
    $bestbuyURLOld = $_POST["bestbuyURLOld"];
    $dellURLOld = $_POST["dellURLOld"];
    $walmartURLOld = $_POST["walmartURLOld"];
    $targetURLOld = $_POST["targetURLOld"];

    $amazonURL = $_POST["amazonURL"];
    $bestbuyURL = $_POST["bestbuyURL"];
    $dellURL = $_POST["dellURL"];
    $walmartURL = $_POST["walmartURL"];
    $targetURL = $_POST["targetURL"];

    $itemIDsAndURLsToEdit = array();
    $itemRetailersAndURLsToInsert = array();

    if (is_numeric($_POST["alertPrice"]) && $_POST["alertPrice"] != 0) {
        $alertPrice = (float) $_POST["alertPrice"];
    } else {
        $alertPrice = (float) $_POST["alertPriceCurrent"];
    }

    if ($alertPrice != (float) $_POST["alertPriceCurrent"]) {
        foreach ($allIDs as $id) {
            $stmt = mysqli_prepare($connect, "UPDATE $table_item SET alertPrice = ? WHERE itemID = ?");
            mysqli_stmt_bind_param($stmt, 'ds', $alertPrice, $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }

    for ($i = 0; $i < sizeof($retailers); $i++) {
        if ($retailers[$i] == "Amazon" && $amazonURLOld != "") {
            $itemIDsAndURLsToEdit[] = array($_POST["amazonID"], $amazonURL);
        } elseif ($retailers[$i] == "Amazon") {
            $itemRetailersAndURLsToInsert[] = array($retailers[$i], $amazonURL);
        } elseif ($retailers[$i] == "Best Buy" && $bestbuyURLOld != "") {
            $itemIDsAndURLsToEdit[] = array($_POST["bestbuyID"], $bestbuyURL);
        } elseif ($retailers[$i] == "Best Buy") {
            $itemRetailersAndURLsToInsert[] = array($retailers[$i], $bestbuyURL);
        } elseif ($retailers[$i] == "Dell" && $dellURLOld != "") {
            $itemIDsAndURLsToEdit[] = array($_POST["dellID"], $dellURL);
        } elseif ($retailers[$i] == "Dell") {
            $itemRetailersAndURLsToInsert[] = array($retailers[$i], $dellURL);
        } elseif ($retailers[$i] == "Walmart" && $walmartURLOld != "") {
            $itemIDsAndURLsToEdit[] = array($_POST["walmartID"], $walmartURL);
        } elseif ($retailers[$i] == "Walmart") {
            $itemRetailersAndURLsToInsert[] = array($retailers[$i], $walmartURL);
        } elseif ($retailers[$i] == "Target" && $targetURLOld != "") {
            $itemIDsAndURLsToEdit[] = array($_POST["targetID"], $targetURL);
        } elseif ($retailers[$i] == "Target") {
            $itemRetailersAndURLsToInsert[] = array($retailers[$i], $targetURL);
        }
    }

    foreach ($itemIDsAndURLsToEdit as $edit) {
        $stmt1 = mysqli_prepare($connect, "UPDATE $table_itr SET url = ? WHERE itemID = ?");
        mysqli_stmt_bind_param($stmt1, 'ss', $edit[1],$edit[0]);
        mysqli_stmt_execute($stmt1);
        mysqli_stmt_close($stmt1);
    }

    foreach ($itemRetailersAndURLsToInsert as $insert) {
        $stmt2 = mysqli_prepare($connect, "INSERT INTO $table_item (itemName, alertPrice) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt2, 'sd', $itemName, $alertPrice);
        mysqli_stmt_execute($stmt2);
        mysqli_stmt_close($stmt2);

        $result_itemID = mysqli_query($connect, "SELECT itemID FROM $table_item ORDER BY itemID DESC");
        $row = $result_itemID->fetch_row();
        $itemID = $row[0];

        $stmt3 = mysqli_prepare($connect, "INSERT INTO $table_itu (itemID, username) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt3, 'ss', $itemID, $username);
        mysqli_stmt_execute($stmt3);
        mysqli_stmt_close($stmt3);

        $stmt4 = mysqli_prepare($connect, "INSERT INTO $table_itr (itemID, retailer, url) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt4, 'sss', $itemID, $insert[0], $insert[1]);
        mysqli_stmt_execute($stmt4);
        mysqli_stmt_close($stmt4);
    }

    print "<p>The properties you changed have now been edited.</p>";

    mysqli_close($connect);

    print "<a href='myItems.php'>Back to My Items</a>";

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

    $itemName = $_POST["itemName"];

    if (isset($_COOKIE["username"])) {
        $username = $_COOKIE["username"];
    } else {
        $username = $_SESSION["username"];
    }

    $result = mysqli_query($connect, "SELECT retailer, url, Item.itemID, alertPrice FROM Item JOIN itemToUser ON Item.itemID = itemToUser.itemID JOIN itemToRetailer ON Item.itemID = itemToRetailer.itemID WHERE username = '" . $username . "' AND itemName = '" . $itemName . "'");

    $retailers = array();
    $urls = array();
    $itemIDs = array();
    $alertPrices = array();

    while ($row = $result->fetch_row()) {
        $retailers[] = $row[0];
        $urls[] = $row[1];
        $itemIDs[] = $row[2];
        $alertPrices[] = (float) $row[3];
    }
    $result->free();

    mysqli_close($connect);

    $alertPrice = $alertPrices[0];
    $amazonURLOld = "";
    $bestbuyURLOld = "";
    $dellURLOld = "";
    $walmartURLOld = "";
    $targetURLOld = "";
    $amazonID = "";
    $bestbuyID = "";
    $dellID = "";
    $walmartID = "";
    $targetID = "";

    for ($i = 0; $i < sizeof($retailers); $i++) {
        if ($retailers[$i] == "Amazon") {
            $amazonURLOld = $urls[$i];
            $amazonID = $itemIDs[$i];
        } elseif ($retailers[$i] == "Best Buy") {
            $bestbuyURLOld = $urls[$i];
            $bestbuyID = $itemIDs[$i];
        } elseif ($retailers[$i] == "Dell") {
            $dellURLOld = $urls[$i];
            $dellID = $itemIDs[$i];
        } elseif ($retailers[$i] == "Walmart") {
            $walmartURLOld = $urls[$i];
            $walmartID = $itemIDs[$i];
        } elseif ($retailers[$i] == "Target") {
            $targetURLOld = $urls[$i];
            $targetID = $itemIDs[$i];
        }
    }

    print <<<FORM
<p>Please check a retailer box to indicate that you would like to edit the URL or add that listing. Changing the alert price will affect all listings. Checked boxes with blank URLs, blank alert price boxes and alert prices of zero will not be accepted.</p>

<form class="editItemsTable" id = "editForm" method = "post" action = "">

<table>

    <tr>
        <td><label for="itemName">Item Name: </label></td><td><input type="text" name="itemName" id="itemName" placeholder="$itemName" readonly></td>
    </tr>
    
    <tr>
        <td><label for="alertPrice">Alert Price: </label></td><td><input type="text" name="alertPrice" id="alertPrice" placeholder=$alertPrice></td>
    </tr>
    
    <tr>
        <td><input type="checkbox" name="retailer[]" value="Amazon" id="amazon"></td><td><label for="amazon"> Amazon</label></td>
        <td><input type="text" name="amazonURL" id="amazonURL" placeholder=$amazonURLOld></td>
    </tr>
    
    <tr>
        <td><input type="checkbox" name="retailer[]" value="Best Buy" id="bestbuy"></td><td><label for="bestbuy"> Best Buy</label></td>
        <td><input type="text" name="bestbuyURL" id="bestbuyURL" placeholder=$bestbuyURLOld></td>
    </tr>
    
    <tr>
        <td><input type="checkbox" name="retailer[]" value="Dell" id="dell"></td><td><label for="dell"> Dell</label></td>
        <td><input type="text" name="dellURL" id="dellURL" placeholder=$dellURLOld></td>
    </tr>
    
    <tr>
        <td><input type="checkbox" name="retailer[]" value="Walmart" id="walmart"></td><td><label for="walmart"> Walmart</label></td>
        <td><input type="text" name="walmartURL" id="walmartURL" placeholder=$walmartURLOld></td>
    </tr>
    
    <tr>
        <td><input type="checkbox" name="retailer[]" value="Target" id="target"></td><td><label for="target"> Target</label></td>
        <td><input type="text" name="targetURL" id="targetURL" placeholder=$targetURLOld></td>
    </tr>
FORM;

    foreach ($itemIDs as $id) {
        print <<<FORM
<input type="hidden" name="allIDs[]" value="$id">
FORM;

    }
    print <<<FORM
    <input type="hidden" name="itemName" id="itemName" value="$itemName">
    
    <input type="hidden" name="alertPriceCurrent" id="alertPriceCurrent" value=$alertPrice>
    <input type='hidden' name='amazonID' id='amazonID' value=$amazonID>
    <input type='hidden' name='bestbuyID' id='bestbuyID' value=$bestbuyID>
    <input type='hidden' name='dellID' id='dellID' value=$dellID>
    <input type='hidden' name='walmartID' id='walmartID' value=$walmartID>
    <input type='hidden' name='targetID' id='targetID' value=$targetID>
    
    <input type='hidden' name='amazonURLOld' id='amazonURLOld' value=$amazonURLOld>
    <input type='hidden' name='bestbuyURLOld' id='bestbuyURLOld' value=$bestbuyURLOld>
    <input type='hidden' name='dellURLOld' id='dellURLOld' value=$dellURLOld>
    <input type='hidden' name='walmartURLOld' id='walmartURLOld' value=$walmartURLOld>
    <input type='hidden' name='targetURLOld' id='targetURLOld' value=$targetURLOld>
    
    <tr><td><input type="submit" value="Submit"></td><td><input type="reset" value="Clear"></td></tr>
</table>
</form>
FORM;
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