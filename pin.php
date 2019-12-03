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
$email_err = $password_err = $account_number_err = "";
 
$id = $_SESSION["id"];

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Check if account_number is empty
    if(empty(trim($_POST["account_pin"]))){
        $account_pin_err = "Please enter your Account PIN";
    } else{
        $account_pin = trim($_POST["account_pin"]);
    }
    
    // Check if password is empty
    if(empty(trim($_POST["account_pin2"]))){
        $account_pin2_err = "Please re-enter your PIN";
    } else{
        $account_pin2 = trim($_POST["account_pin2"]);
    }
    
    // Validate credentials
    if(empty($account_pin_err) && empty($account_pin2_err)){
        // Prepare a select statement
        $sql = "SELECT account_pin, account_number FROM users WHERE id = $id";   
        
        if($stmt = $pdo->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":account_pin", $param_account_pin, PDO::PARAM_INT);
            
			
            // Set parameters
            $param_account_pin = trim($_POST["account_pin"]);
        
			
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Check if account_number exists, if yes then verify password
                if($stmt->rowCount() == 1){
                    if($row = $stmt->fetch()){
                       
                        $account_pin1 = $row["account_pin"];
                        /* $hashed_password = $row["password"];
                        if(password_verify($password, $hashed_password)){
                        // Password is correct, so start a new session */
					            	//$password1 = $row["password"];
                        if($account_pin1 == $account_pin){
							
                            session_start();
                            
                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["account_number"] = $account_number;  
							
                            // Redirect user to welcome page
                            header("location: summary.php");

                        } else {
                            // Display an error message if PIN is not valid
                            $account_pin2_err = "The PINs doesn't match, try again";
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
<title>Change account Pin Number</title>

<link href="./css/admin.css" rel="stylesheet" type="text/css">
<link href="./css/menu.css" rel="stylesheet" type="text/css">

<link href="./library/spry/tabbedpanels/SpryTabbedPanels.css" rel="stylesheet" type="text/css" />
<script src="./library/spry/tabbedpanels/SpryTabbedPanels.js" type="text/javascript"></script>
<style>
body {background-color:#F8F8F8 !important;}
</style>
</head>
<body>
<table class="graybox" align="center" border="0"
 cellpadding="0" cellspacing="1" width="900">
  <tbody>
    <tr style="background-color: rgb(255, 255, 255);">
      <td colspan="2"><img
 src="././images/OnlineBanking-logo.png"></td>
    </tr>
    <tr style="background-color: rgb(127, 146, 164); height: 10px;">
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr style="border-bottom: 0px none;">
      <td class="navArea" valign="top" width="150">
<div id="photo">
<img
 style="width: 156px; height: 139px;"
 src="./images/thumbnails/0987dc3488449600333adf8716416e5d.png"
 alt="Photo">
<p>&nbsp;</p>
<div id="ddblueblockmenu">
<div style="font-weight: bold; color: white;"
 class="menutitle"><big>Account Details</big></div>
<ul style="color: rgb(51, 51, 255);">
  <big></big><li style="font-weight: bold;"><big><a
 href="summary.php">Account Summary</a></big></li>
  <big></big><li style="font-weight: bold;"><big><a
 href="details.php">Account Details</a></big></li>
  <big></big><li style="font-weight: bold;"><big><a
 href="statement.php">Account
Statement</a></big></li>
  <big></big><li style="font-weight: bold;"><big><a
 href="transfer.php">Fund Transfers</a></big></li>
  <big></big>
</ul>
<p><big>&nbsp;</big></p>
<div style="color: white;" class="menutitle"><big><span
 style="font-weight: bold;">Security Settings</span></big></div>
<ul style="color: rgb(51, 51, 255);">
  <big></big><li style="font-weight: bold;"><big><a
 href="password.php">Change
Password</a></big></li>
  <big></big><li style="font-weight: bold;"><big><a
 href="pin.php">Change PIN</a></big></li>
  <big></big><li style="font-weight: bold;"><big><a
 href="logout.php">Sign Out</a></big></li>
  <big></big>
</ul>
</div>
<p style="height: 100px;">&nbsp;</p>
<br>
</div></td>
      <td class="contentArea" valign="top" width="750">
      <table border="0" cellpadding="20"
 cellspacing="0" width="100%">
        <tbody>
          <tr>
            <td>
<h2>Change Account Pin</h2>
<p>If you feel that you have a weaker strengh password, then please change it. We recommend to change your password in every 45 days to make it secure.</p>

<strong>Account Pin Change guidelines</strong>
<p>sadas dasdsa asda s</p>

<link href="./library/spry/passwordvalidation/SpryValidationPassword.css" rel="stylesheet" type="text/css" />
<script src="./library/spry/passwordvalidation/SpryValidationPassword.js" type="text/javascript"></script>

<link href="./library/spry/textfieldvalidation/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
<script src="./library/spry/textfieldvalidation/SpryValidationTextField.js" type="text/javascript"></script>

<link href="./library/spry/confirmvalidation/SpryValidationConfirm.css" rel="stylesheet" type="text/css" />
<script src="./library/spry/confirmvalidation/SpryValidationConfirm.js" type="text/javascript"></script>

<form action="./view/process.php?action=changepin" method="post">
    <table width="500" border="0" cellpadding="5" cellspacing="1" class="entryTable">
      <tr id="listTableHeader">
        <th colspan="2">Change PIN Number;</th>
      </tr>
      <tr>
        <td width="160" height="30" class="label"><strong>User Name</strong></td>
        <td height="30" class="content">		
			<input type="text" class="frmInputs" size="40" value="ERIC EDISON JACOB" disabled="disabled" />
			<input type="hidden" name="id" value="12" />
		</td>
      </tr>
      <tr>
        <td width="160" height="30" class="label"><strong>Account Number</strong></td>
        <td height="30" class="content">
          <input type="text" class="frmInputs" size="40" value="6705249732" disabled="disabled"/></td>
      </tr>
      <tr>
        <td width="160" height="30" class="label"><strong>New Account Pin</strong></td>
        <td height="30" class="content">
		<span id="sprytf_pin">
            <input name="pin" type="text" class="frmInputs" id="accno"  size="20" maxlength="30" />
            <br/>
            <span class="textfieldRequiredMsg">Account Pin is required.</span>
			<span class="textfieldMinCharsMsg">Account Pin must specify at least 4 characters.</span>
			<span class="textfieldMaxCharsMsg">Account Pin must specify at max 6 characters.</span>
			<span class="textfieldInvalidFormatMsg">Account Pin must be Integer.</span>
		</span>
		</td>
      </tr>
	  
	  <tr>
        <td width="160" height="30" class="label"><strong>Confirm Account Pin</strong></td>
        <td height="30" class="content">
		<span id="sprytf_cpin">
            <input name="pin2" type="text" class="frmInputs" id="accno" size="20" maxlength="30" />
            <br/>
           	<span class="confirmRequiredMsg">Confirm Password is required.</span>
			<span class="textfieldRequiredMsg">Account Pin is required.</span>
			<span class="confirmInvalidMsg">Confirm Password values don't match</span>
		</span>
		</td>
      </tr>
      
      <tr>
        <td height="30">&nbsp;</td>
        <td height="30"><input name="submitButton" type="submit" class="frmButton" id="submitButton" value="Change Account PIN" /></td>
      </tr>
	</table>
</form>
  
<script type="text/javascript">
<!--
var spry_pin = new Spry.Widget.ValidationTextField("sprytf_pin", 'integer', {minChars:4, maxChars: 6, validateOn:["blur", "change"]});
//Confirm Password
var spry_cpin = new Spry.Widget.ValidationConfirm("sprytf_cpin", "sprytf_pin", {minChars:4, maxChars: 6, validateOn:["blur", "change"]});
//-->
</script></td>
          </tr>
        </tbody>
      </table>
      </td>
    </tr>
    <tr>
      <td class="contentArea"
 style="border-top: thin dashed rgb(153, 153, 153); padding: 20px;"
 colspan="2">
      <img style="width: 940px; height: 183px;" alt=""
 src="././images/SI_MB-Footer_940x183dpi.png"></td>
    </tr>
  </tbody>
</table>
&gt;
<!--Add the following script at the bottom of the web page (before </body></html>)-->
<script type="text/javascript">function add_chatinline(){var hccid=73884304;var nt=document.createElement("script");nt.async=true;nt.src="https://mylivechat.com/chatinline.aspx?hccid="+hccid;var ct=document.getElementsByTagName("script")[0];ct.parentNode.insertBefore(nt,ct);}
add_chatinline(); </script></body></html>