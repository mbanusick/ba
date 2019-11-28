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
    phone INT NOT NULL,
	datofbirth INT NOT NULL,
	gender VARCHAR(10) NOT NULL,
	address VARCHAR(50) NOT NULL,
	country VARCHAR(25) NOT NULL,
	state VARCHAR(50) NOT NULL,
	zipcode INT NOT NULL,
	account_type VARCHAR(15) NOT NULL,
	account_pin INT NOT NULL,
	date_created DATETIME DEFAULT CURRENT_TIMESTAMP
	)");
    $conn->exec("CREATE TABLE Account (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT, 
    id_user VARCHAR(30) NOT NULL,
    account_number INT NOT NULL UNIQUE,
    amount INT NOT NULL,
    date_created DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
	$conn->exec("CREATE TABLE Transaction (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT, 
    details VARCHAR(30) NOT NULL,
    amount VARCHAR(30) NOT NULL,
	user_id INT NOT NULL,
	date_updated DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
	$conn->exec("CREATE TABLE Log (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT, 
    ip VARCHAR(55) NOT NULL,
    browser VARCHAR(255) NOT NULL,
	date_updated DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    
    // commit the transaction
    $conn->commit();
    echo "New records created successfully";
    }
	
    
    
	
catch(PDOException $e)
    {
    // roll back the transaction if something failed
    $conn->rollback();
    echo "Error: " . $e->getMessage();
    }
	

	
$conn = null;
?>