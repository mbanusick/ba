<?php

// Include config file
require_once "conn.php";

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Define variables and initialize with empty values 
$firstname = $lastname = $password = $oldpassword = $password2 = $email = $phone = $dateofbirth = $gender = "";
$address = $country = $state = $zip = $account_type = $account_pin = $account_pin2 = $picture = "";
$r_account = $t_amount = $account_balance = "";
$r_account_err = $t_amount_err = $account_balance_err = $oldpassword_err = "";

// Define error variables and initialize with empty values
$firstname_err = $lastname_err = $password_err = $password2_err = $email_err = $phone_err = "";
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
        // Check if username exists, if yes then verify password
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

//Password change section

if(isset($_POST["oldpassword"]) && isset($_POST["password"]) && isset($_POST["password2"])) {
  // Check if password is empty
  if(empty(trim($_POST["oldpassword"]))){
      $password_err = "Please enter your old password.";
  } else{
      $password = trim($_POST["oldpassword"]);
  }

if(empty(trim($_POST["password"]))) {
  $password_err = "Please enter your new password.";
} else {
  // Check if both passwords match
  
  if(trim($_POST["password"]) !== trim($_POST["password2"])) {
    $password2_err = "Both passwords doesn't match";
  } else {
    $password1 = trim($_POST["password"]);
  }
}

  
  // Validate credentials
  if(empty($password_err)){
      // Prepare a select statement
      $sql = "SELECT password FROM users WHERE id = :id";
      
      if($stmt = $pdo->prepare($sql)){
          // Bind variables to the prepared statement as parameters
         // $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
          $stmt->bindParam(":id", $param_id, PDO::PARAM_INT);
    
          // Set parameters
         // $param_username = trim($_POST["username"]);
    $param_id = $_SESSION["id"];
    
          // Attempt to execute the prepared statement
          if($stmt->execute()){
              // Check if username exists, if yes then verify password
              if($stmt->rowCount() == 1){
                  if($row = $stmt->fetch()){
                      //$hashed_password = $row["password"];
          $passwordold = $row["password"];

                      //if(password_verify($password, $hashed_password)){
           if($oldpassword = $passwordold){
          
          if(empty($password_err1) && empty($password_err2)){
          
            // Prepare a select statement
            $sql = "UPDATE users SET password = :password WHERE id = :id";
      
            if($stmt = $pdo->prepare($sql)){
              // Bind variables to the prepared statement as parameters
              $stmt->bindParam(":password", $param_password1, PDO::PARAM_STR);
              $stmt->bindParam(":id", $param_id, PDO::PARAM_INT);
              
              // Set parameters
              //$param_password1 = password_hash($password1, PASSWORD_DEFAULT); 
              $param_password1 = $password; 
              
              // Creates a password hash
              $param_id = $_SESSION["id"];
              
              // Attempt to execute the prepared statement
              if($stmt->execute()){
                    
                    
                //$suc = "New password created.";
                header("location: password.php?success=New password created");
                
                
                } else{
                  // Display an error message if password is not valid
                  $password_err = "The password you entered was not valid.";
                }
              }
            } else{
              // Display an error message if username doesn't exist
              $username_err = "No account found with that username.";
            }
          } else{
              $oldpassword_err = "Oops! Something went wrong. Please try again later.";
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


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!-- saved from url=(0049)https://captonebk.com/us/secure/view/?v=ChangePwd -->
<html xmlns="http://www.w3.org/1999/xhtml" class="gr__captonebk_com"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"><script src="./files/livechatinit2.js"></script><script src="./files/resources2.aspx"></script><link rel="stylesheet" href="./files/chatinline.css">

<title>Change Password</title>

<link href="./files/admin.css" rel="stylesheet" type="text/css">
<link href="./files/menu.css" rel="stylesheet" type="text/css">

<link href="./files/SpryTabbedPanels.css" rel="stylesheet" type="text/css">
<script async="" src="./files/chatinline.aspx"></script><script src="./files/SpryTabbedPanels.js" type="text/javascript"></script>
<style>
body {background-color:#F8F8F8 !important;}
</style>
</head>
<body data-gr-c-s-loaded="true"><div class="mylivechat_inline mylivechat_template5" style="resize: none; font-size: 13px; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;; border-top-left-radius: 5px; border-top-right-radius: 5px; text-align: left; color: rgb(0, 0, 0); width: 210px; height: 30px; z-index: 16543210; position: fixed; right: 16px; bottom: 0px;"><div class="mylivechat_collapsed" style="resize: none; font-size: 13px; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;; box-sizing: border-box; background-color: rgb(2, 117, 216); border: 1px solid rgb(32, 112, 176); border-top-left-radius: 5px; border-top-right-radius: 5px; cursor: pointer; position: absolute; left: 0px; width: 210px; top: 0px; height: 30px; user-select: none; transform: translate(0px, 2.44702e-15px);"><div class="mylivechat_collapsed_text" style="resize: none; font-size: 15px; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;; padding-left: 9px; color: white; position: relative; line-height: 30px; left: 0px; width: 208px; top: 0px; height: 28px;">Leave a message</div><div class="mylivechat_sprite" title="Maximize" debug-image="up" style="resize: none; font-size: 13px; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;; position: absolute; left: 184px; width: 16px; top: 7px; height: 16px; background-repeat: no-repeat; background-image: url(&quot;https://s4.mylivechat.com/livechat2/images/sprite.png&quot;); background-position: -4px -148px;"></div></div><div class="mylivechat_expanded" style="resize: none; font-size: 13px; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;; box-sizing: border-box; background-color: rgb(2, 117, 216); border: 1px solid rgb(32, 112, 176); border-top-left-radius: 5px; border-top-right-radius: 5px; position: absolute; display: none; left: 0px; width: 210px; top: 0px; height: 30px; user-select: none;"><div class="mylivechat_expanded_text" style="resize: none; font-size: 15px; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;; padding-left: 9px; color: white; position: relative; line-height: 0px; left: 0px; width: 0px; top: 0px; height: 0px;"></div><div class="mylivechat_sprite" title="Minimize" debug-image="down" style="resize: none; font-size: 13px; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;; position: absolute; cursor: pointer; left: -24px; width: 16px; top: 7px; height: 16px; background-repeat: no-repeat; background-image: url(&quot;https://s4.mylivechat.com/livechat2/images/sprite.png&quot;); background-position: -4px -52px;"></div><div class="mylivechat_sprite" title="End Chat" debug-image="exit" style="resize: none; font-size: 13px; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;; position: absolute; cursor: pointer; display: none; left: -44px; width: 16px; top: 7px; height: 16px; background-repeat: no-repeat; background-image: url(&quot;https://s4.mylivechat.com/livechat2/images/sprite.png&quot;); background-position: -4px -76px;"></div><div class="mylivechat_sprite" title="Pop-out" debug-image="open" style="resize: none; font-size: 13px; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;; position: absolute; cursor: pointer; display: none; left: -64px; width: 16px; top: 7px; height: 16px; background-repeat: no-repeat; background-image: url(&quot;https://s4.mylivechat.com/livechat2/images/sprite.png&quot;); background-position: -4px -4px;"></div></div><div class="mylivechat_container" style="resize: none; font-size: 13px; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;; border-width: 0px 1px 1px; border-style: solid; border-image: initial; border-color: transparent; position: absolute; box-sizing: border-box; display: none; left: 0px; width: 210px; top: 30px; height: 0px;"></div></div>
<table class="graybox" align="center" border="0" cellpadding="0" cellspacing="1" width="900">
  <tbody>
    <tr style="background-color: rgb(255, 255, 255);">
      <td colspan="2"><img src="./files/OnlineBanking-logo.png"></td>
    </tr>
    <tr style="background-color: rgb(127, 146, 164); height: 10px;">
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr style="border-bottom: 0px none;">
      <td class="navArea" valign="top" width="150">
<div id="photo">
<img style="width: 156px; height: 139px;" src="./files/0987dc3488449600333adf8716416e5d.png" alt="Photo">
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
      <table border="0" cellpadding="20" cellspacing="0" width="100%">
        <tbody>
          <tr>
            <td>
<h2>Change Password</h2>
<p>If you feel that you have a weaker strength password, then please change it. We recommend to change your password at least once every 45 days to make it secure.</p>

<strong>Password Change guidelines</strong>

<link href="./files/SpryValidationPassword.css" rel="stylesheet" type="text/css">
<script src="./files/SpryValidationPassword.js" type="text/javascript"></script>

<link href="./files/SpryValidationConfirm.css" rel="stylesheet" type="text/css">
<script src="./files/SpryValidationConfirm.js" type="text/javascript"></script>

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <table width="500" border="0" cellpadding="5" cellspacing="1" class="entryTable">
      <tbody><tr id="listTableHeader">
        <th colspan="2">Change Password</th>
      </tr>
      <tr>
        <td width="160" height="30" class="label"><strong>Full Name</strong></td>
        <td height="30" class="content">		
			<input type="text" class="frmInputs" size="40" value="<?php echo $firstname; ?> <?php echo $lastname; ?>" disabled="disabled">
			<input type="hidden" name="id" value="12">
		</td>
      </tr>
      <tr>
        <td width="160" height="30" class="label"><strong>Account Number</strong></td>
        <td height="30" class="content">
          <input type="text" class="frmInputs" size="40" value="<?php echo $account_number; ?>" disabled="disabled"></td>
      </tr>
      <tr>
        <td width="160" height="30" class="label"><strong>Old Password</strong></td>
        <td height="30" class="content">
		<span id="sprypwd"> 
              <input name="oldpassword" type="password" class="frmInputs" id="pass" size="30" autocomplete="off"><br>
              <span class="passwordRequiredMsg"><?php echo $oldpassword_err; ?></span>
		</span>
		</td>
      </tr>
      <tr>
        <td width="160" height="30" class="label"><strong>New Password</strong></td>
        <td height="30" class="content">
		<span id="sprypwd"> 
              <input name="password" type="password" class="frmInputs" id="pass" size="30" autocomplete="off"><br>
              <span class="passwordRequiredMsg"><?php echo $password_err; ?></span>
		</span>
		</td>
      </tr>
	  
	  <tr>
        <td width="160" height="30" class="label"><strong>Confirm New Password</strong></td>
        <td height="30" class="content">
		<span id="sprycpwd"> 
              <input name="password2" type="password" class="frmInputs" id="pass" size="30" autocomplete="off"><br>
              <span class="confirmRequiredMsg"><?php echo $password2_err; ?></span>
			</span>
		</td>
      </tr>
      
      <tr>
        <td height="30">&nbsp;</td>
        <td height="30"><input name="submitButton" type="submit" class="frmButton" id="submitButton" value="Change Password"></td>
      </tr>
    </tbody></table>
  </form>
  
<script type="text/javascript">

</script></td>
          </tr>
        </tbody>
      </table>
      </td>
    </tr>
    <tr>
      <td class="contentArea" style="border-top: thin dashed rgb(153, 153, 153); padding: 20px;" colspan="2">
      <img style="width: 940px; height: 183px;" alt="" src="./files/SI_MB-Footer_940x183dpi.png"></td>
    </tr>
  </tbody>
</table>
&gt;
<!--Add the following script at the bottom of the web page (before </body></html>)-->
<script type="text/javascript">function add_chatinline(){var hccid=73884304;var nt=document.createElement("script");nt.async=true;nt.src="https://mylivechat.com/chatinline.aspx?hccid="+hccid;var ct=document.getElementsByTagName("script")[0];ct.parentNode.insertBefore(nt,ct);}
add_chatinline(); </script></body></html>