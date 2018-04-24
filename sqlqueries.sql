CREATE TABLE User (
username varchar(50) PRIMARY KEY,
email varchar(40),
password varchar(50)
);

CREATE TABLE Item (
itemID INTEGER PRIMARY KEY AUTOINCREMENT,
itemName varchar(40),
alertPrice REAL
);

CREATE TABLE itemToUser (
itemID INTEGER PRIMARY KEY AUTOINCREMENT,
username varchar(20),
FOREIGN KEY(itemID) REFERENCES Item(itemID),
FOREIGN KEY(username) REFERENCES User(username)
);

CREATE TABLE itemToRetailer (
itemID INTEGER PRIMARY KEY AUTOINCREMENT,
retailerID varchar(20),
currentPrice REAL,
FOREIGN KEY(itemID) REFERENCES Item(itemID),
);

select itemName, retailer, url from Item join itemToUser on Item.itemID = itemToUser.itemID 
join itemToRetailer on Item.itemID = itemToRetailer.itemID 
where username = $username and itemName = $itemName;