<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add An Item</title>
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
<div class="addItems">
<h1>Add An Item</h1>
H1;

if (!isset($_COOKIE["username"]) && !isset($_SESSION["username"])) {
    redirect("./login.php");
    return;
}

if (isset($_POST) && $_POST["itemName"] != "" && $_POST["alertPrice"] != "" && sizeof($_POST["retailer"]) > 0) {
    insert();
} else {
    insertForm();
}

function checkURL ($str)
{
    return preg_match("/(((http|ftp|https):\/{2})+(([0-9a-z_-]+\.)+(aero|asia|biz|cat|com|coop|edu|gov|info|int|jobs|mil|mobi|museum|name|net|org|pro|tel|travel|ac|ad|ae|af|ag|ai|al|am|an|ao|aq|ar|as|at|au|aw|ax|az|ba|bb|bd|be|bf|bg|bh|bi|bj|bm|bn|bo|br|bs|bt|bv|bw|by|bz|ca|cc|cd|cf|cg|ch|ci|ck|cl|cm|cn|co|cr|cu|cv|cx|cy|cz|cz|de|dj|dk|dm|do|dz|ec|ee|eg|er|es|et|eu|fi|fj|fk|fm|fo|fr|ga|gb|gd|ge|gf|gg|gh|gi|gl|gm|gn|gp|gq|gr|gs|gt|gu|gw|gy|hk|hm|hn|hr|ht|hu|id|ie|il|im|in|io|iq|ir|is|it|je|jm|jo|jp|ke|kg|kh|ki|km|kn|kp|kr|kw|ky|kz|la|lb|lc|li|lk|lr|ls|lt|lu|lv|ly|ma|mc|md|me|mg|mh|mk|ml|mn|mn|mo|mp|mr|ms|mt|mu|mv|mw|mx|my|mz|na|nc|ne|nf|ng|ni|nl|no|np|nr|nu|nz|nom|pa|pe|pf|pg|ph|pk|pl|pm|pn|pr|ps|pt|pw|py|qa|re|ra|rs|ru|rw|sa|sb|sc|sd|se|sg|sh|si|sj|sj|sk|sl|sm|sn|so|sr|st|su|sv|sy|sz|tc|td|tf|tg|th|tj|tk|tl|tm|tn|to|tp|tr|tt|tv|tw|tz|ua|ug|uk|us|uy|uz|va|vc|ve|vg|vi|vn|vu|wf|ws|ye|yt|yu|za|zm|zw|arpa)(:[0-9]+)?((\/([~0-9a-zA-Z\#\+\%@\.\/_-]+))?(\?[0-9a-zA-Z\+\%@\/&\[\];=_-]+)?)?))\b/imuS", $str);
}

function urlError() {
    print <<<ERROR
<p>One or more of the URLs has been entered incorrectly. Be sure to include the full URL exactly as it is shown in the address bar. Please return to My Items and try again.</p>
<p><a href='myItems.php'>Back to My Items</a></p>
ERROR;

}

function priceError() {
    print <<<ERROR
<p>The alert price you have set is invalid. Please return to My Items and try again.</p>
<p><a href='myItems.php'>Back to My Items</a></p>
ERROR;

}

function insert()
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

    $amazonURL = mysqli_real_escape_string($connect, strip_tags($_POST["amazonURL"]));
    $bestbuyURL = mysqli_real_escape_string($connect, strip_tags($_POST["bestbuyURL"]));
    $dellURL = mysqli_real_escape_string($connect, strip_tags($_POST["dellURL"]));
    $walmartURL = mysqli_real_escape_string($connect, strip_tags($_POST["walmartURL"]));
    $targetURL = mysqli_real_escape_string($connect, strip_tags($_POST["targetURL"]));

    if (($amazonURL != "" && !checkURL($amazonURL)) || ($bestbuyURL != "" && !checkURL($bestbuyURL)) || ($dellURL != "" && !checkURL($dellURL)) || ($walmartURL != "" && !checkURL($walmartURL)) || ($targetURL != "" && !checkURL($targetURL))) {
        urlError();
        mysqli_close($connect);
        return;
    }

    if (is_numeric($_POST["alertPrice"]) && $_POST["alertPrice"] != 0) {
        $alertPrice = (float) mysqli_real_escape_string($connect, strip_tags($_POST["alertPrice"]));;
    } else {
        priceError();
        mysqli_close($connect);
        return;
    }

    $table_item = "Item";
    $table_itu = "itemToUser";
    $table_itr = "itemToRetailer";

    if (isset($_COOKIE["username"])) {
        $username = $_COOKIE["username"];
    } else {
        $username = $_SESSION["username"];
    }
    $itemName = mysqli_real_escape_string($connect, strip_tags($_POST["itemName"]));
    $retailers = $_POST["retailer"];

    foreach ($retailers as $retailer) {
        if ($retailer == "Amazon") {
            $url = $amazonURL;
        } elseif ($retailer == "Best Buy") {
            $url = $bestbuyURL;
        } elseif ($retailer == "Dell") {
            $url = $dellURL;
        } elseif ($retailer == "Walmart") {
            $url = $walmartURL;
        } elseif ($retailer == "Target") {
            $url = $targetURL;
        } else {
            $url = "none";
        }

        $stmt1 = mysqli_prepare($connect, "INSERT INTO $table_item (itemName, alertPrice) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt1, 'sd', $itemName, $alertPrice);
        mysqli_stmt_execute($stmt1);
        mysqli_stmt_close($stmt1);

        $result_itemID = mysqli_query($connect, "SELECT itemID FROM $table_item ORDER BY itemID DESC");
        $row = $result_itemID->fetch_row();
        $itemID = $row[0];

        $stmt2 = mysqli_prepare($connect, "INSERT INTO $table_itu (itemID, username) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt2, 'ss', $itemID, $username);
        mysqli_stmt_execute($stmt2);
        mysqli_stmt_close($stmt2);

        $stmt3 = mysqli_prepare($connect, "INSERT INTO $table_itr (itemID, retailer, url) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt3, 'sss', $itemID, $retailer, $url);
        mysqli_stmt_execute($stmt3);
        mysqli_stmt_close($stmt3);
    }

    mysqli_close($connect);

    print <<<STUFF
    <p>Your item has now been added.</p>
    <a href='myItems.php'>Back to My Items</a>
STUFF;


}

function insertForm()
{
    print <<<FORM
<div class="addItemsTable">
<form id = "insertForm" method = "post" action = "">

    <table>
    
        <tr>
            <td><label for="itemName">Item Name: </label></td><td><input type="text" name="itemName" id="itemName"></td>
        </tr>
        
        <tr>
            <td><label for="alertPrice">Alert Price: </label></td><td><input type="text" name="alertPrice" id="alertPrice"></td>
        </tr>
        
        <tr>
            <td><input type="checkbox" name="retailer[]" value="Amazon" id="amazon"></td><td><label for="amazon"> Amazon</label></td>
            <td><input type="text" name="amazonURL" id="amazonURL" placeholder="Product Page URL"></td>
        </tr>
        
        <tr>
            <td><input type="checkbox" name="retailer[]" value="Best Buy" id="bestbuy"></td><td><label for="bestbuy"> Best Buy</label></td>
            <td><input type="text" name="bestbuyURL" id="bestbuyURL" placeholder="Product Page URL"></td>
        </tr>
        
        <tr>
            <td><input type="checkbox" name="retailer[]" value="Dell" id="dell"></td><td><label for="dell"> Dell</label></td>
            <td><input type="text" name="dellURL" id="dellURL" placeholder="Product Page URL"></td>
        </tr>
        
        <tr>
            <td><input type="checkbox" name="retailer[]" value="Walmart" id="walmart"></td><td><label for="walmart"> Walmart</label></td>
            <td><input type="text" name="walmartURL" id="walmartURL" placeholder="Product Page URL"></td>
        </tr>
        
        <tr>
            <td><input type="checkbox" name="retailer[]" value="Target" id="target"></td><td><label for="target"> Target</label></td>
            <td><input type="text" name="targetURL" id="targetURL" placeholder="Product Page URL"></td>
        </tr>
        
        <tr><td><input type="submit" value="Submit"></td><td><input type="reset" value="Clear"></td></tr>
    </table>
</form>
</div>
FORM;
}

print "</div>";

$date = date('l\, F jS\, Y');

print <<<FOOTER
<footer>
    <div class="footer">
        <p>$date<br>Price Alert created by Blake Schmidt, Ben Luzarraga, and Kyle Gruber.</p>
    </div>
</footer>
FOOTER;

?>

</body>
</html>
