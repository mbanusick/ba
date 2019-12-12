<?php

// Include config file
require_once "conn.php";

// Check if the user is already logged in, if yes then redirect him to welcome page
 if(isset($_SESSION["account_number"]) && $_SESSION["account_number"] === !NULL){
    header("location: summary.php");
    exit;
} 
 
 
// Define variables and initialize with empty values
$firstname = $lastname = $password = $email = "";
$email_err = $password_err = $account_pin_err = "";
 
$id = $_SESSION["id"];
$ip = $_SERVER['REMOTE_ADDR'];
$browser = $_SERVER['HTTP_USER_AGENT'];

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    
    // Check if pin is empty
    if(empty(trim($_POST["account_pin"]))){
        $account_pin_err = "Please enter your Account PIN";
    } else{
        $account_pin = trim($_POST["account_pin"]);
    }
    
    
    // Validate credentials
    if(empty($account_pin_err) && empty($account_pin_err)){
        // Prepare a select statement
        $sql = "SELECT users.account_pin, account.account_number FROM users 
        INNER JOIN account ON Users.id=Account.user_id WHERE users.id = $id";   
        
        if($stmt = $pdo->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":account_pin", $param_account_pin, PDO::PARAM_INT);
            
			
            // Set parameters
            $param_account_pin = $account_pin;
        
			
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Check if account_pin exists, if yes then verify pin
                if($stmt->rowCount() == 1){
                    if($row = $stmt->fetch()){
                       // $id = $row["id"];
                        $account_pin1 = $row["account_pin"];
                        $account_number = $row["account_number"];

                        if($account_pin1 == $account_pin){
                            // Store data in session variables
                            $_SESSION["account_number"] = $account_number;  
                            
                            //IP and browser log
                            $pdo-> prepare ("INSERT INTO log (user_id, ip, browser) VALUES($id, '$ip', '$browser')") -> execute();
                            
                            
                            // Redirect user to dashboard page
                            header("location: summary.php");
                        }
                    }
                } else{
                    // Display an error message if username doesn't exist
                    $account_pin_err = "The PIN is Incorrect, try again";
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
        
        // Close statement
        unset($stmt);
    }
    
    // Close connection
    unset($pdo);
}
?>


<!DOCTYPE html>
<html lang="en" class="nojs">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Online account</title>

<link href="./css/admin.css" rel="stylesheet" type="text/css">
<link href="./css/styles.css" rel="stylesheet" type="text/css">

<link href="./library/spry/textfieldvalidation/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
<script src="./library/spry/textfieldvalidation/SpryValidationTextField.js" type="text/javascript"></script>

<link href="./library/spry/passwordvalidation/SpryValidationPassword.css" rel="stylesheet" type="text/css" />
<script src="./library/spry/passwordvalidation/SpryValidationPassword.js" type="text/javascript"></script>

</head>

<body style="background-color:#ECECEC;margin-top:50px;">
<table width="750" border="0" align="center" cellpadding="0" cellspacing="1" class="graybox">
 <tr style="background-color:#FFFFFF"> 
  <td><img src="./images/OnlineBanking-logo.png" /></td>
 </tr>
 <tr> 
  <td valign="top"> 
  <table width="100%" border="0" cellspacing="0" cellpadding="20">
    <tr> 
     <td class="contentArea">
      <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
      <h2 align="center"><strong>Login Step 2:</strong> Enter PIN to Access your Account</h2>
      <p align="center">Enter Your Account PIN to proceed</p>
	  <div class="errorMessage" align="center">&nbsp;</div>
       <table width="450" border="0" align="center" cellpadding="5" cellspacing="1" bgcolor="#336699" class="entryTable">
        <tr id="entryTableHeader"> 
         <td><div align="center">:: Customer PIN ::</div></td>
        </tr>
        <tr> 
         <td class="contentArea"> 
		 
		  <table width="100%" border="0" cellpadding="2" cellspacing="1" class="text">
           <tr> 
            <td colspan="3">&nbsp;</td>
           </tr>
           <tr class="text"> 
            <td width="100" align="right">Account PIN#</td>
            <td width="10" >:</td>
            <td>
			<span id="sprytextfield1" style="text-align:left;">
            <input name="account_pin" type="password" id="accno" tabindex="10" size="30" maxlength="30" />
            <br/>
            <span class="passwordRequiredMsg"><?php echo $account_pin_err; ?></span>
			</span>
			</td>
           </tr>
           
           <tr> 
            <td colspan="2">&nbsp;</td>
            <td><input name="submitButton" type="submit" id="submitButton" value="Continue ! " /></td>
           </tr>
		  
          </table></td>
        </tr>
       </table>
       <p>&nbsp;</p>
      </form></td>
    </tr>
	          <tr>
            <td class="contentArea"
 style="border-top: thin dashed rgb(153, 153, 153);">
            <img style="width: 940px; height: 183px;" alt=""
 src="images/SI_MB-Footer_940x183dpi.png"></td>
          </tr>
	</tr>
   </table>
   
   </td>
 </tr>
</table>
<p>&nbsp;</p>
</body>

</html>
