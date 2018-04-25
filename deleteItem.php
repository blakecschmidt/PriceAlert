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

if (isset($_POST) && ($_POST["all"] == "true" || sizeof($_POST["retailer"]) > 0)) {
    delete();
} else {
    deleteForm();
}

function delete()
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

    $allIDs = $_POST["allIDs"];
    $retailers = $_POST["retailer"];
    $itemIDsToDelete = array();

    if ($_POST["all"] == "true") {
        $itemIDsToDelete = $allIDs;
    } else {
        foreach ($retailers as $retailer) {
            if ($retailer == "Amazon") {
                $itemIDsToDelete[] = $_POST["amazonID"];
            } elseif ($retailer == "Best Buy") {
                $itemIDsToDelete[] = $_POST["bestbuyID"];
            } elseif ($retailer == "Dell") {
                $itemIDsToDelete[] = $_POST["dellID"];
            } elseif ($retailer == "Walmart") {
                $itemIDsToDelete[] = $_POST["walmartID"];
            } elseif ($retailer == "Target") {
                $itemIDsToDelete[] = $_POST["targetID"];
            }
        }
    }

    foreach ($itemIDsToDelete as $id) {
        $stmt1 = mysqli_prepare($connect, "DELETE FROM $table_item WHERE itemID = (?)");
        mysqli_stmt_bind_param($stmt1, 's', $id);
        mysqli_stmt_execute($stmt1);
        mysqli_stmt_close($stmt1);

        $stmt2 = mysqli_prepare($connect, "DELETE FROM $table_itu WHERE itemID = (?)");
        mysqli_stmt_bind_param($stmt2, 's', $id);
        mysqli_stmt_execute($stmt2);
        mysqli_stmt_close($stmt2);

        $stmt3 = mysqli_prepare($connect, "DELETE FROM $table_itr WHERE itemID = (?)");
        mysqli_stmt_bind_param($stmt3, 's', $id);
        mysqli_stmt_execute($stmt3);
        mysqli_stmt_close($stmt3);
    }

    print "<p>The items you selected have now been deleted.</p>";

    mysqli_close($connect);

    print "<a href='myItems.html'>Back to My Items</a>";

}

function deleteForm()
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
    $username = $_COOKIE["username"];

    $stmt = mysqli_prepare($connect, "SELECT retailer, url, itemID FROM Item JOIN itemToUser ON Item.itemID = itemToUser.itemID JOIN itemToRetailer ON Item.itemID = itemToRetailer.itemID WHERE username = ? AND itemName = ?");
    mysqli_stmt_bind_param($stmt, 'ss', $username, $itemName);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $result = mysqli_query($connect, $stmt);

    $retailers = array();
    $urls = array();
    $itemIDs = array();

    while ($row = $result->fetch_row()) {
        $retailers[] = $row[0];
        $urls[] = $row[1];
        $itemIDs[] = $row[2];
    }
    $result->free();

    mysqli_close($connect);

    print <<<FORM
<form id = "deleteForm" method = "post" action = "">

<table>

    <tr>
        <td><label for="itemName">Item Name: </label></td><td><input type="text" name="itemName" id="itemName" placeholder=$itemName readonly></td>
    </tr>
    
    <tr>
        <td><input type="checkbox" name="all" id="all" value="true"></td><td><label for="all"> Check this box to delete this entire item. You can also individually select sites to delete from the item.</label></td>
    </tr>
  
FORM;

    for ($i = 0; $i < sizeof($retailers); $i++) {
        if ($retailers[$i] == "Amazon") {
            $amazonURL = $urls[$i];
            $amazonID = $itemIDs[$i];
            $amazonTag ="<tr><td><input type='checkbox' name='retailer[]' value='Amazon' id='amazon'></td><td><label for='amazon'> Amazon</label></td><td><input type='text' name='amazonURL' id='amazonURL' placeholder=$amazonURL readonly></td></tr>";
            print $amazonTag;
            print "<input type='hidden' name='amazonID' id='amazonID' value=$amazonID>";
        } elseif ($retailers[$i] == "Best Buy") {
            $bestbuyURL = $urls[$i];
            $bestbuyID = $itemIDs[$i];
            $bestbuyTag ="<tr><td><input type='checkbox' name='retailer[]' value='Best Buy' id='bestbuy'></td><td><label for='bestbuy'> Best Buy</label></td><td><input type='text' name='bestbuyURL' id='bestbuyURL' placeholder=$bestbuyURL readonly></td></tr>";
            print $bestbuyTag;
            print "<input type='hidden' name='bestbuyID' id='bestbuyID' value=$bestbuyID>";
        } elseif ($retailers[$i] == "Dell") {
            $dellURL = $urls[$i];
            $dellID = $itemIDs[$i];
            $dellTag ="<tr><td><input type='checkbox' name='retailer[]' value='Dell' id='dell'></td><td><label for='dell'> Dell</label></td><td><input type='text' name='dellURL' id='dellURL' placeholder=$dellURL readonly></td></tr>";
            print $dellTag;
            print "<input type='hidden' name='dellID' id='dellID' value=$dellID>";
        } elseif ($retailers[$i] == "Walmart") {
            $walmartURL = $urls[$i];
            $walmartID = $itemIDs[$i];
            $walmartTag ="<tr><td><input type='checkbox' name='retailer[]' value='Walmart' id='walmart'></td><td><label for='walmart'> Walmart</label></td><td><input type='text' name='walmartURL' id='walmartURL' placeholder=$walmartURL readonly></td></tr>";
            print $walmartTag;
            print "<input type='hidden' name='walmartID' id='walmartID' value=$walmartID>";
        } elseif ($retailers[$i] == "Target") {
            $targetURL = $urls[$i];
            $targetID = $itemIDs[$i];
            $targetTag ="<tr><td><input type='checkbox' name='retailer[]' value='Target' id='target'></td><td><label for='target'> Target</label></td><td><input type='text' name='targetURL' id='targetURL' placeholder=$targetURL readonly></td></tr>";
            print $targetTag;
            print "<input type='hidden' name='targetID' id='targetID' value=$targetID>";
        }
    }

    print <<<FORM
    <input type="hidden" name="allIDs" id="allIDs" value=$itemIDs>
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