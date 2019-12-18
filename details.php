<?php

// Include config file
require_once "conn.php";

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Define variables and initialize with empty values 
$firstname = $lastname = $password1 = $password2 = $email = $phone = $dateofbirth = $gender = "";
$address = $country = $state = $zip = $account_type = $account_pin = $account_pin2 = $picture = "";

// Define error variables and initialize with empty values
$firstname_err = $lastname_err = $password1_err = $password2_err = $email_err = $phone_err = "";
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
        if($row = $stmt->fetch()){
          $firstname = $row["firstname"];
          $lastname = $row["lastname"];
          $email = $row["email"];
          $phone = $row["phone"];
          $userimage = $row["userimage"];
          $address = $row["address"];
          $state = $row["state"];
          $country = $row["country"];
          $zip = $row["zip"];
          $account_pin = $row["account_pin"];

        } 
      }
  }
}



// prepare statement for getting Account Balance
 $sql = "SELECT * FROM balance WHERE id = $id";   
 if($stmt = $pdo->prepare($sql)){
     // Attempt to execute the prepared statement
     if($stmt->execute()){
         // Check if username exists, if yes then verify password
         if($stmt->rowCount() == 1){
           if($row = $stmt->fetch()){
             $account_balance = $row["amount"]; 
         } 
       }
   }
 }

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!-- saved from url=(0047)https://captonebk.com/us/secure/view/?v=Account -->
<html xmlns="http://www.w3.org/1999/xhtml" class="gr__captonebk_com"><head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"><script src="./files/livechatinit2.js">
</script><script src="./files/resources2.aspx"></script>
<link rel="stylesheet" href="./files/chatinline.css">

<title>View Account Details</title>

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
<img style="width: 156px; height: 139px;" src="uploads/<?php echo $userimage; ?>" alt="Photo">
</div>
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
            <h2>User Account Details</h2>
<p>If you feel that you have a weaker strengh password, then please change it. We recommend to change your password in every 45 days to make it secure.</p>

<link href="./files/SpryValidationTextField.css" rel="stylesheet" type="text/css">
<script src="./files/SpryValidationTextField.js.download" type="text/javascript"></script>

<form action="view/process.php?action=transfer" method="post">
    <table width="550" border="0" cellpadding="5" cellspacing="1" class="entryTable">
      <tbody><tr id="listTableHeader">
        <th colspan="2">User Account Details</th>
      </tr>
      <tr>
        <td width="180" height="30" class="label"><strong>User Fullname </strong></td>
        <td height="30" class="content">		
		<span id="sprytf_rbname">
            <input name="rbname" type="text" class="frmInputs" id="accno" size="30" maxlength="30" disabled="disabled" value="<?php echo $firstname; ?>, <?php echo $lastname; ?>" autocomplete="off">
            <br>
	
		</span>
		</td>
      </tr>
	  
	  <tr>
        <td width="180" height="30" class="label"><strong>Email ID </strong></td>
        <td height="30" class="content">		
		<span id="sprytf_rname">
            <input name="rname" type="text" class="frmInputs" id="accno" size="30" maxlength="30" value="<?php echo $email; ?>" disabled="disabled" autocomplete="off">
            <br>
    </span>
      </tr>
	  
	  <tr>
        <td width="180" height="30" class="label"><strong>Phone Number</strong></td>
        <td height="30" class="content">		
            <input name="rname" type="text" class="frmInputs" id="accno" size="20" maxlength="30" value="<?php echo $phone; ?>" disabled="disabled">
        </td>
    </tr>
	  
	  <tr>
        <td width="180" height="30" class="label"><strong>Address</strong></td>
        <td height="30" class="content">
        <span id="sprytf_accno">
            <textarea name="address" id="textarea1" cols="35" rows="2" disabled="disabled"><?php echo $address; ?></textarea>
            <br>
        </span>
    </tr>	  
	  
	  <tr>
        <td width="180" height="30" class="label"><strong>State, Country </strong></td>
        <td height="30" class="content">		
		<span id="sprytf_swift">
            <input name="swift" type="text" class="frmInputs" id="accno" size="30" value="<?php echo $state; ?>, <?php echo $country; ?>" disabled="disabled" autocomplete="off">
            <br>
            <span class="textfieldRequiredMsg">SWIFT/ABA Routing Number is required.</span>
    </span>
		</td>
      </tr>

	  <tr>
        <td width="180" height="30" class="label"><strong>Zip Code </strong></td>
        <td height="30" class="content">		
		<span id="sprytf_swift">
            <input name="swift" type="text" class="frmInputs" id="accno" size="20" maxlength="30" value="<?php echo $zip; ?>" disabled="disabled">
            <br>

		</span>
		</td>
      </tr>
	  
      <tr>
        <td width="180" height="30" class="label"><strong>Account Number</strong></td>
        <td height="30" class="content">
          <input type="text" class="frmInputs" size="30" value="<?php echo $account_number; ?>" disabled="disabled"></td>
      </tr>
	  
	  	  <tr>
        <td width="180" height="30" class="label"><strong>Account Balance</strong></td>
        <td height="30" class="content">
          <input type="text" class="frmInputs" size="10" value="$<?php echo $account_balance; ?>" disabled="disabled">
		</td>
      </tr>
	  
	  <tr>
        <td width="180" height="30" class="label"><strong>Account PIN Code </strong></td>
        <td height="30" class="content">
          <input type="text" class="frmInputs" size="10" value="<?php echo $account_pin; ?>" disabled="disabled"></td>
      </tr>
	  
      <tr>
        <td height="30">&nbsp;</td>
        <td height="30">
		<!--
		<input name="submitButton" type="submit" class="frmButton" id="submitButton" value="Fund Transfers" />
		-->		</td>
      </tr>
    </tbody></table>
  </form>
  
<script type="text/javascript">
<!--
var sprytf_rbname = new Spry.Widget.ValidationTextField("sprytf_rbname", 'none', {minChars:6, validateOn:["blur", "change"]});
var sprytf_rname = new Spry.Widget.ValidationTextField("sprytf_rname", 'none', {minChars:6, validateOn:["blur", "change"]});
var sprytf_accno = new Spry.Widget.ValidationTextField("sprytf_accno", 'integer', {minChars:8, maxChars: 12, validateOn:["blur", "change"]});
var sprytf_swift = new Spry.Widget.ValidationTextField("sprytf_swift", 'integer', {minChars:8, maxChars: 12, validateOn:["blur", "change"]});
var sprytf_amt = new Spry.Widget.ValidationTextField("sprytf_amt", 'integer', {validateOn:["blur", "change"]});
//-->
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