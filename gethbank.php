<?php

// Include config file
require_once "conn.php";

$q = intval($_GET['q']);
$bank_name = $firstname = $lastname = "";

//get users fullname using account number
$sql="SELECT bank_name FROM account WHERE account_number = '".$q."'";
if($stmt = $pdo->prepare($sql)){
    // Attempt to execute the prepared statement
    if($stmt->execute()){

        if($stmt->rowCount() == 1){
          if($row = $stmt->fetch()){
            $bank_name = $row["bank_name"]; 
         
        } 
      }
   }
}


echo "$bank_name";

//mysqli_close($con);
?>
