<?php

// Include config file
require_once "conn.php";

$q = intval($_GET['q']);
$fullname = $firstname = $lastname = "";

$sql="SELECT users.firstname, users.lastname FROM users INNER JOIN account ON account.user_id = users.id WHERE account.account_number = '".$q."'";
if($stmt = $pdo->prepare($sql)){
    // Attempt to execute the prepared statement
    if($stmt->execute()){
        // Check if username exists, if yes then verify password
        if($stmt->rowCount() == 1){
          if($row = $stmt->fetch()){
            $firstname = $row["firstname"]; 
            $lastname = $row["lastname"];
        } 
      }
   }
}

$fullname = $firstname . ' ' . $lastname;

echo "$fullname";

//mysqli_close($con);
?>
