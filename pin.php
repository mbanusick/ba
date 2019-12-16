<?php

// Include config file
require_once "conn.php";

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Define variables and initialize with empty values 
$firstname = $lastname = $pin = $pin2 = $email = $phone = $dateofbirth = $gender = "";
$address = $country = $state = $zip = $account_type = $account_pin = $account_pin2 = $picture = "";
$r_account = $t_amount = $account_balance = "";
$r_account_err = $t_amount_err = $account_balance_err = "";

// Define error variables and initialize with empty values
$firstname_err = $lastname_err = $pin_err = $pin2_err = $email_err = $phone_err = "";
$dateofbirth_err = $gender_err = $address_err = $country_err = $state_err = $zip_err = "";
$account_type_err = $account_pin_err = $account_pin2_err = $picture_err = "";

//session data
$id = $_SESSION["id"];
$ip = $_SERVER['REMOTE_ADDR'];
$account_number = $_SESSION["account_number"];

 // prepare statement for getting user data from DB****************************1
$sql = "SELECT * FROM users WHERE id = $id";   
if($stmt = $pdo->prepare($sql)){
    // Attempt to execute the prepared statement
    if($stmt->execute()){
        // Check if username exists, if yes then verify pin
        if($stmt->rowCount() == 1){
          if($row = $stmt->fetch()){
            $firstname = $row["firstname"];
            $lastname = $row["lastname"];
            $email = $row["email"];
            $phone = $row["phone"];
            $address = $row["address"];
            $state = $row["state"];
            $country = $row["country"];
            $zip = $row["zip"];
            $account_pin = $row["account_pin"];

        } 
      }
  }
}

//PIN change section

if(isset($_POST["oldpin"]) && isset($_POST["pin"]) && isset($_POST["pin2"])) {
  // Check if pin is empty
  if(empty(trim($_POST["oldpin"]))){
      $pin_err = "Please enter your old pin.";
  } else{
      $oldpin = trim($_POST["oldpin"]);
  }

if(empty(trim($_POST["pin"]))) {
  $pin_err = "Please enter your new pin.";
} else {
  // Check if both pins match
  
  if(trim($_POST["pin"]) !== trim($_POST["pin2"])) {
    $pin2_err = "Both pins doesn't match";
  } else {
    $pin = trim($_POST["pin"]);
  }
}

  
  // Validate credentials
  if(empty($pin_err)){
      // Prepare a select statement
      $sql = "SELECT account_pin FROM users WHERE id = $id";
      
      if($stmt = $pdo->prepare($sql)){
      
          // Attempt to execute the prepared statement
          if($stmt->execute()){
              // Check if username exists, if yes then verify pin
              if($stmt->rowCount() == 1){
                  if($row = $stmt->fetch()){
                      //$hashed_pin = $row["pin"];
          $pinold = $row["account_pin"];

                      //if(pin_verify($pin, $hashed_pin)){
           if($oldpin = $pinold){
          
          if(empty($pin_err) && empty($pin2_err)){
          
            // Prepare a select statement
            $sql = "UPDATE users SET account_pin = :pin WHERE id = $id";
      
            if($stmt = $pdo->prepare($sql)){
              // Bind variables to the prepared statement as parameters
              $stmt->bindParam(":pin", $param_pin, PDO::PARAM_INT);
              
              // Set parameters
              $param_pin = $pin; 
              
              
              // Attempt to execute the prepared statement
              if($stmt->execute()){
                    
                    
                //$suc = "New pin created.";
                header("location: pin.php?success=New pin created");
                
                
                } else{
                  // Display an error message if pin is not valid
                  $pin_err = "The pin you entered was not valid.";
                }
              }
            } 
            }
          } else{
              $oldpin_err = "Oops! Something went wrong. Please try again later.";
          }
      }
      
      // Close statement
      unset($stmt);
  }
  
  // Close connection
  
}
}
}
}

?>

<!DOCTYPE html>
<html lang="en" class="nojs">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Change Account Pin Number</title>

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
 href="pin.php">Change
pin</a></big></li>
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
<p>If you feel that you have a weaker strength PIN, then please change it. We recommend to change your PIN from time to time.</p>

<strong>Account Pin Change guidelines</strong>


<link href="./library/spry/pinvalidation/SpryValidationpin.css" rel="stylesheet" type="text/css" />
<script src="./library/spry/pinvalidation/SpryValidationpin.js" type="text/javascript"></script>

<link href="./library/spry/textfieldvalidation/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
<script src="./library/spry/textfieldvalidation/SpryValidationTextField.js" type="text/javascript"></script>

<link href="./library/spry/confirmvalidation/SpryValidationConfirm.css" rel="stylesheet" type="text/css" />
<script src="./library/spry/confirmvalidation/SpryValidationConfirm.js" type="text/javascript"></script>

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <table width="500" border="0" cellpadding="5" cellspacing="1" class="entryTable">
      <tr id="listTableHeader">
        <th colspan="2">Change PIN Number;</th>
      </tr>
      <tr>
        <td width="160" height="30" class="label"><strong>Full Name</strong></td>
        <td height="30" class="content">		
			<input type="text" class="frmInputs" size="40" value="<?php echo $firstname; ?> <?php echo $lastname; ?>" disabled="disabled" />
			<input type="hidden" name="id" value="12" />
		</td>
      </tr>
      <tr>
        <td width="160" height="30" class="label"><strong>Account Number</strong></td>
        <td height="30" class="content">
          <input type="text" class="frmInputs" size="40" value="<?php echo $account_number; ?>" disabled="disabled"/></td>
      </tr>
      <tr>
        <td width="160" height="30" class="label"><strong>Old Account Pin</strong></td>
        <td height="30" class="content">
		    <span id="sprytf_pin">
            <input name="oldpin" type="text" class="frmInputs" id="accno"  size="20" maxlength="30" />
            <br/>
            <span class="textfieldRequiredMsg"><?php echo $account_pin_err; ?></span>
			</span>
		</td>
      </tr>
      <tr>
        <td width="160" height="30" class="label"><strong>New Account Pin</strong></td>
        <td height="30" class="content">
		    <span id="sprytf_pin">
            <input name="pin" type="text" class="frmInputs" id="accno"  size="20" maxlength="30" />
            <br/>
            <span class="textfieldRequiredMsg"><?php echo $account_pin_err; ?></span>
			</span>
		</td>
      </tr>
	  
	  <tr>
        <td width="160" height="30" class="label"><strong>Confirm Account Pin</strong></td>
        <td height="30" class="content">
		<span id="sprytf_cpin">
            <input name="pin2" type="text" class="frmInputs" id="accno" size="20" maxlength="30" />
            <br/>
           	<span class="confirmRequiredMsg"><?php echo $account_pin2_err; ?></span>

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
//Confirm pin
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