<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "siteKnab";


try {

    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // begin the transaction
    $conn->beginTransaction();
    // our SQL statements
    $conn->exec("CREATE TABLE Users (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
   	firstname VARCHAR(50) NOT NULL,
    lastname VARCHAR(50) NOT NULL,
	password VARCHAR(255) NOT NULL,
	email VARCHAR(50) NOT NULL UNIQUE,
    phone VARCHAR(13) NOT NULL,
	dateofbirth DATE,
	gender VARCHAR(10) NOT NULL,
    userimage VARCHAR(255) NOT NULL,
	address VARCHAR(50) NOT NULL,
	country VARCHAR(25) NOT NULL,
	state VARCHAR(50) NOT NULL,
	zip VARCHAR(10) NOT NULL,
	account_type VARCHAR(15) NOT NULL,
	account_pin INT NOT NULL,
	date_created DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
    
    $conn->exec("CREATE TABLE Account (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT, 
    user_id VARCHAR(30) NOT NULL,
    account_number BIGINT NOT NULL UNIQUE,
    bank_name VARCHAR(30) NOT NULL,
    date_created DATETIME DEFAULT CURRENT_TIMESTAMP
    -- FOREIGN KEY (user_id) REFERENCES Users(id)
    )");

    $conn->exec("CREATE TABLE Balance (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT, 
    account_id VARCHAR(30) NOT NULL,
    amount INT NOT NULL,
    date_created DATETIME DEFAULT CURRENT_TIMESTAMP
    -- FOREIGN KEY (account_id) REFERENCES Account(id)
    )");

	$conn->exec("CREATE TABLE Transaction (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT, 
    details VARCHAR(30) NOT NULL,
    amount VARCHAR(30) NOT NULL,
	account_id INT NOT NULL,
    type TINYINT NOT NULL,
    txid VARCHAR(100) NOT NULL,
	date_updated DATETIME DEFAULT CURRENT_TIMESTAMP
    -- FOREIGN KEY (account_id) REFERENCES Account(id)
    )");

	$conn->exec("CREATE TABLE Log (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT, 
    user_id INT NOT NULL,
    ip VARCHAR(55) NOT NULL,
    browser VARCHAR(255) NOT NULL,
	date_updated DATETIME DEFAULT CURRENT_TIMESTAMP
    -- FOREIGN KEY (user_id) REFERENCES Users(id)
    )");
    
    // commit the transaction
    $conn->commit();
    echo "New records created successfully";

} catch(PDOException $e) {
    // roll back the transaction if something failed
    $conn->rollback();
    echo "Error: " . $e->getMessage();
}
	
$conn = null;

?>