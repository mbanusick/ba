<?php

// Include config file
require_once "conn.php";

// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: pinlogin.php");
    print_r($_SESSION);
    exit;
}
 

// Define variables and initialize with empty values
$firstname = $lastname = $password = $email = $account_number = "";
$email_err = $password_err = $account_number_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Check if account_number is empty
    if(empty(trim($_POST["account_number"]))){
        $account_number_err = "Please enter your Account Number";
    } else {
        $account_number = trim($_POST["account_number"]);
    }
    
    // Check if password is empty
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }
    
    // Validate credentials
    if(empty($account_number_err) && empty($password_err)){
        // Prepare a select statement
        $sql = "SELECT Users.id, Account.account_number, Users.password FROM 
        Users INNER JOIN Account ON Users.id=Account.user_id WHERE account_number = :account_number";
        
        if($stmt = $pdo->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":account_number", $param_account_number, PDO::PARAM_INT);
            //$stmt->bindParam(":password", $param_password, PDO::PARAM_STR);
			
            // Set parameters
            $param_account_number = $account_number;
           // $param_password = $password;
			
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Check if account_number exists, if yes then verify password
                if($stmt->rowCount() == 1){
                    if($row = $stmt->fetch()){
                        $id = $row["id"];
                        $account_number = $row["account_number"];
                        $password1 = $row["password"];
                        if($password == $password1){
						// Password is correct, so start a new session	
                            session_start();
                            
                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                             
							
                            // Redirect user to welcome page
                            header("location: pinlogin.php");
                        } else{
                            // Display an error message if password is not valid
                            $password_err = "The Password you entered was not valid";
                        }
                    }
                } else{
                    // Display an error message if username doesn't exist
                    $account_number_err = "No Account found with that Account Number";
                }
            } else{
                echo "Oops! Something went wrong. Please try again later";
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
      <h2 align="center"><strong>Login Step 1:</strong> Log in to Access your Account</h2>
      <p align="center">Enter Your Account Login Details to proceed</p>
	  <div class="errorMessage" align="center">&nbsp;</div>
       <table width="450" border="0" align="center" cellpadding="5" cellspacing="1" bgcolor="#336699" class="entryTable">
        <tr id="entryTableHeader"> 
         <td><div align="center">:: Customer Login ::</div></td>
        </tr>
        <tr> 
         <td class="contentArea"> 
		 
		  <table width="100%" border="0" cellpadding="2" cellspacing="1" class="text">
           <tr> 
            <td colspan="3">&nbsp;</td>
           </tr>
           <tr class="text"> 
           <td width="100" align="right">Account No#</td>
            <td width="10" align="center">:</td>
            <td>
			<span id="sprypassword1" style="text-align:left;"> 
              <input name="account_number" value="<?php echo $account_number; ?>" type="text" id="pass" tabindex="20" size="30" /><br />
              
              <span class="passwordRequiredMsg"><?php echo $account_number_err; ?></span>
			</span>
			</td>
           </tr>
           <tr> 
            <td width="100" align="right">Password</td>
            <td width="10" align="center">:</td>
            <td>
			<span id="sprypassword1" style="text-align:left;"> 
              <input name="password" type="password" id="pass" tabindex="20" size="30" /><br />
              <span class="passwordRequiredMsg"><?php echo $password_err; ?></span>
			</span>
			</td>
           </tr>
		   
		  
           <tr> 
            <td colspan="2">&nbsp;</td>
            <td><input name="submitButton" type="submit" id="submitButton" value="Login Now ! " /></td>
           </tr>
		   
		   <tr>
             <td colspan="3">
			 If your account is not register with us, please <a href="register.php">Register it Now</a>.
			 </td>
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
<script>
<!--
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1", "integer", {validateOn:["blur", "change"]});
var sprypassword1 = new Spry.Widget.ValidationPassword("sprypassword1", {minChars:6, maxChars: 12, validateOn:["blur", "change"]});
//-->
</script>
</html>
