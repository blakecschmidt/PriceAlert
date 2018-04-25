CREATE TABLE User (
username varchar(20) PRIMARY KEY,
email varchar(40),
password varchar(50)
);

CREATE TABLE Item (
itemID INTEGER PRIMARY KEY AUTO_INCREMENT,
itemName varchar(40),
alertPrice FLOAT
);

CREATE TABLE itemToUser (
itemID INTEGER PRIMARY KEY AUTO_INCREMENT,
username varchar(20),
FOREIGN KEY(itemID) REFERENCES Item(itemID),
FOREIGN KEY(username) REFERENCES User(username)
);

CREATE TABLE itemToRetailer (
itemID INTEGER PRIMARY KEY AUTO_INCREMENT,
retailerID varchar(20),
currentPrice FLOAT,
FOREIGN KEY(itemID) REFERENCES Item(itemID)
);

select itemName, retailer, url from Item join itemToUser on Item.itemID = itemToUser.itemID 
join itemToRetailer on Item.itemID = itemToRetailer.itemID 
where username = $username and itemName = $itemName;