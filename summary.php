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
$r_account = $t_amount = $account_balance = $t_option = $t_option_err = "";
$r_account_err = $t_amount_err = $account_balance_err = "";

// Define error variables and initialize with empty values
$firstname_err = $lastname_err = $password1_err = $password2_err = $email_err = $phone_err = "";
$dateofbirth_err = $gender_err = $address_err = $country_err = $state_err = $zip_err = "";
$account_type_err = $account_pin_err = $account_pin2_err = $picture_err = "";

//session data
$id = $_SESSION["id"];
$ip = $_SERVER['REMOTE_ADDR'];
$account_number = $_SESSION["account_number"];

// Transaction ID generation
function codeGen($string) {
  $char = "ABCDEFGHIJKMNOPQRSTVWXWZABCDEFGHIJKMNOPQRSTVWXWZ023456789";
  $count = strlen($char);
  srand((double)microtime()*1000000);
  $str = "";

  for ($i = 0; $i <= $string; ++$i) {
      $num = rand() % $count;
      $tmp = substr($char, $num, 1);
      $str = $str . $tmp;
  }
  return $str;
}
$txid = 'TRX_' . codeGen(8,12);



 // prepare statement for getting user data from DB*1
$sql = "SELECT * FROM users WHERE id = $id";   
if($stmt = $pdo->prepare($sql)){
    // Attempt to execute the prepared statement
    if($stmt->execute()){
       
        if($stmt->rowCount() == 1){
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

//Transfer Affairs

//form validations
if($_SERVER["REQUEST_METHOD"] == "POST"){
    
    // Validate details
    if(empty(trim($_POST["details"]))){
      $details_err = "Please enter transfer details";     
  }/* elseif(!is_numeric($_POST["t_amount"])){
      $t_amount_err = "Only numbers allowed";
  }*/ else {
      $details = trim($_POST["details"]);
  }

  // Validate t_amount
   if(empty(trim($_POST["t_amount"]))){
       $t_amount_err = "Please enter transfer amount";     
   }/* elseif(!is_numeric($_POST["t_amount"])){
       $t_amount_err = "Only numbers allowed";
   }*/ else {
       $t_amount = trim($_POST["t_amount"]);
   }
   
    // Validate t_options
    if(empty(trim($_POST["t_option"]))){
      $t_option_err = "Please select transfer option";     
  }/* elseif(!is_numeric($_POST["t_amount"])){
      $t_amount_err = "Only numbers allowed";
  }*/ else {
      $t_option = trim($_POST["t_option"]);
  }

 // Validate r_account
   if(empty(trim($_POST["r_account"]))){
       $r_account_err = "Please enter account number";     
   } /*elseif(!is_numeric($_POST["r_account"])){
       $r_account_err = "Only numbers allowed";
   }*/
   else 
          {
            //$r_account = trim($_POST["r_account"]);
           // Prepare a select statement
           $sql = "SELECT id FROM account WHERE account_number = :r_account";
                  
                if($stmt = $pdo->prepare($sql))
                {

                  // Bind variables to the prepared statement as parameters
                  $stmt->bindParam(":r_account", $param_r_account, PDO::PARAM_INT);
                      
                  // Set parameters
                  $param_r_account = trim($_POST["r_account"]);
                      
                    // Attempt to execute the prepared statement
                    if($stmt->execute())
                    {
                              if($stmt->rowCount() != 1)
                              {
                                $r_account_err = "That account does not exist";
                              } 
                              else{
                                  $r_account = trim($_POST["r_account"]);
                                  }
                    }
                 } 
          }

$details2 = $t_option . ':' . $details;

//Check if amount to transfer is greater than balance
if ($t_amount > $account_balance) {
  $account_balance_err = "Your account balance is less than requested amount";
}

 // Check input errors before inserting in database
 if(empty($t_amount_err) && empty($r_account_err) && empty($account_balance_err) && empty($t_option_err)) {

  try {
          $pdo->beginTransaction();

//remove from account -select and update db (Debit)

$newamount = $account_balance - $t_amount;
$pdo-> prepare("UPDATE balance SET amount=$newamount WHERE id = $id") -> execute();

//Transaction 1
$pdo-> prepare("INSERT INTO Transaction (details, amount, account_id, type, txid) 
          VALUES ('$details2', '$t_amount', '$id', '0', '$txid')")-> execute();
      
//$pdo-> prepare("UPDATE balance SET amount=$newamount WHERE id = $id") -> execute();

//add to account -select and update db (Credit)
$sql = "SELECT balance.amount, account.account_number, account.user_id FROM balance INNER JOIN ACCOUNT 
ON balance.account_id = ACCOUNT.user_id WHERE ACCOUNT.account_number = :r_account";

if($stmt = $pdo->prepare($sql)){
  
  // Bind variables to the prepared statement as parameters
  $stmt->bindParam(":r_account", $param_r_account, PDO::PARAM_INT);
  
  // Set parameters
  $param_r_account = trim($_POST["r_account"]);

  if($stmt->execute()){
      
      if($stmt->rowCount() == 1){
        if($row = $stmt->fetch()){
          $oldamount2 = $row["amount"];
          $receive = $row["account_number"];
          $receiver_account_id =$row["user_id"];
          } 
          $newamount2 = $oldamount2 + $t_amount;
          $pdo-> prepare("UPDATE balance INNER JOIN account ON balance.account_id = ACCOUNT.user_id 
          SET balance.amount = $newamount2 WHERE account.account_number = $receive") -> execute();
         
         // Transaction 2
         $pdo-> prepare("INSERT INTO Transaction (details, amount, account_id, type, txid) 
          VALUES ('$details2', '$t_amount', '$receiver_account_id', '1', '$txid')")-> execute();
           }

     }

  }

$pdo-> commit();      
header("location: transfer.php");

    
  } catch(PDOException $e) {
       $pdo->rollBack();
      header("location: summary.php?error={$e->getMessage()}");
  }
}
}

$getTransactions= $pdo->prepare("SELECT * FROM transaction WHERE account_id=$id ORDER BY date_updated DESC");

$getTransactions->execute();

$transactions = [];
while ($row = $getTransactions->fetch(PDO::FETCH_ASSOC)) { 
    array_push($transactions, $row); 
}

?>


<!DOCTYPE html>
<html lang="en" class="nojs">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Account Dashboard: <?php echo $firstname; ?> <?php echo $lastname; ?></title>

<link href="./css/admin.css" rel="stylesheet" type="text/css">
<link href="./css/menu.css" rel="stylesheet" type="text/css">

<link href="./library/spry/tabbedpanels/SpryTabbedPanels.css" rel="stylesheet" type="text/css" />
<script src="./library/spry/tabbedpanels/SpryTabbedPanels.js" type="text/javascript"></script>
<style>
body {background-color:#F8F8F8 !important;}
</style>
<script>
function showHint(str) {
    if (str.length < 10) {
        document.getElementById("txtHint").value = "Invalid Account";
        
        return;
    } else {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("txtHint").value = this.responseText;
                
            }
        };
        xmlhttp.open("GET", "gethnumber.php?q=" + str, true);
       
        xmlhttp.send();
    }
}


function showHint2(str) {
    if (str.length < 10) {
        
        document.getElementById("txtHint2").value = "No Bank Found";
        return;
    } else {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
               
                document.getElementById("txtHint2").value = this.responseText;
            }
        };
      
        xmlhttp.open("GET", "gethbank.php?q=" + str, true);
        xmlhttp.send();
    }
}


</script>

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
<strong>Welcome, <?php echo $firstname; ?> <?php echo $lastname; ?></strong>
<p>You have logged in from IP: <?php echo $ip; ?><br>	</p><div class="TabbedPanels" id="AccountSummaryPanel">
		<ul class="TabbedPanelsTabGroup">
			<li class="TabbedPanelsTab TabbedPanelsTabSelected" tabindex="0">Account Details</li>
			<li class="TabbedPanelsTab" tabindex="0">Account Statements</li>
			<li class="TabbedPanelsTab" tabindex="0">Fund Transfer</li>
		</ul>
		<div class="TabbedPanelsContentGroup">
		<div class="TabbedPanelsContent TabbedPanelsContentVisible" style="display: block;">
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
          <input type="text" class="frmInputs" size="10" value="$<?php $a = number_format($account_balance, 2); echo $a; ?>" disabled="disabled">
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
</div>

      <div class="TabbedPanelsContent" style="display: none;">
      
      <strong>Account Statement</strong>
<p>View list of all credit/ debit / fund transfer transaction summary on this account.</p>

<table width="100%" border="0" align="center" cellpadding="2" cellspacing="1" class="text">
  <tbody><tr align="center" id="listTableHeader"> 
   <th width="80" scope="col">Transaction Date</th>
   <th width="80">Reference No#</th>
   <th width="250">Description</th>
   <th width="60">Debit</th>
   <th width="60">Credit</th>
  </tr>
  
  <?php for($i=0; $i < count($transactions);$i++): ?>
  <tr class="row2"> 
   <td><?=$transactions[$i]["date_updated"]?></td>
   <td><div align="center"><?=$transactions[$i]["txid"]?></div></td>
   <td width="250" align="center"><?=$transactions[$i]["details"]?> of $<?= number_format($transactions[$i]["amount"], 2)?> with Reference# <?=$transactions[$i]["txid"]?></td>
   <td width="50" align="center"><?php if ($transactions[$i]["type"] == 0) { echo '$' . number_format($transactions[$i]["amount"], 2); } else { echo " ";}?> </td>
   <td width="50" align="center"><?php if ($transactions[$i]["type"] == 1) { echo '$'.number_format($transactions[$i]["amount"], 2);} else { echo " ";}?></td>
  </tr>
  <?php endfor; ?>
  
  
  <tr class="row2"> 
   <td>02-16-2019 09:02:50</td>
   <td><div align="center">TRX_GHF3CVB7</div></td>
   <td width="250" align="center">Fund transfer of $50000 with Reference# TRX_GHF3CVB7</td>
   <td width="50" align="center"></td>
   <td width="50" align="center">$50,000.00</td>
  </tr> 
  <tr class="row2"> 
   <td>12-07-2018 08:41:30</td>
   <td><div align="center">TRX_BWB562ICA</div></td>
   <td width="250" align="center">Fund transfer of $80000 with Reference# TRX_BWB562ICA</td>
   <td width="50" align="center">$80,000.00</td>
   <td width="50" align="center"></td>
  </tr> 
  <tr class="row2"> 
   <td>01-08-2018 10:55:53</td>
   <td><div align="center">TRX_BWB5564DG</div></td>
   <td width="250" align="center">Fund transfer of $200000 with Reference# TRX_BWB5564DG</td>
   <td width="50" align="center"></td>
   <td width="50" align="center">$200,000.00</td>
  </tr> 
  <tr class="row2"> 
   <td>12-24-2017 12:27:02</td>
   <td><div align="center">TRX_THB5545TJ</div></td>
   <td width="250" align="center">Fund transfer of $167000 with Reference# TRX_THB5545TJ</td>
   <td width="50" align="center">$167,000.00</td>
   <td width="50" align="center"></td>
  </tr> 
  <tr class="row2"> 
   <td>09-13-2016 11:09:13</td>
   <td><div align="center">TRX_THGYU4234</div></td>
   <td width="250" align="center">Fund transfer of $50000 with Reference# TRX_THGYU4234</td>
   <td width="50" align="center">$50,000.00</td>
   <td width="50" align="center"></td>
  </tr> 
  <tr class="row2"> 
   <td>01-20-2016 02:06:50</td>
   <td><div align="center">TRX_FHDU4TBB</div></td>
   <td width="250" align="center">Fund transfer of $580612 with Reference# TRX_FHDU4TBB</td>
   <td width="50" align="center"></td>
   <td width="50" align="center">$580,612.00</td>
  </tr> 
  <tr class="row2"> 
   <td>08-09-2015 11:33:50</td>
   <td><div align="center">TRX_FHTUG654</div></td>
   <td width="250" align="center">Fund transfer of $350000 with Reference# TRX_FHTUG654</td>
   <td width="50" align="center">$350,000.00</td>
   <td width="50" align="center"></td>
  </tr> 
  <tr class="row2"> 
   <td>03-12-2015 10:06:50</td>
   <td><div align="center">TRX_HJSUG4CS</div></td>
   <td width="250" align="center">Fund transfer of $24570 with Reference# TRX_HJSUG4CS</td>
   <td width="50" align="center"></td>
   <td width="50" align="center">$24,570.00</td>
  </tr> 
  <tr class="row2"> 
   <td>11-18-2014 12:01:50</td>
   <td><div align="center">TRX_DS42GTZQ</div></td>
   <td width="250" align="center">Fund transfer of $130400 with Reference# TRX_DS42GTZQ</td>
   <td width="50" align="center">$130,400.00</td>
   <td width="50" align="center"></td>
  </tr> 
  <tr class="row2"> 
   <td>03-15-2014 05:16:40</td>
   <td><div align="center">TRX_BV34H4JM</div></td>
   <td width="250" align="center">Fund transfer of $69000 with Reference# TRX_BV34H4JM</td>
   <td width="50" align="center">$69,000.00</td>
   <td width="50" align="center"></td>
  </tr> 
  <tr class="row2"> 
   <td>07-15-2013 03:06:12</td>
   <td><div align="center">TRX_MV34H4CU</div></td>
   <td width="250" align="center">Fund transfer of $450000 with Reference# TRX_MV34H4CU</td>
   <td width="50" align="center"></td>
   <td width="50" align="center">$450,000.00</td>
  </tr> 
  <tr class="row2"> 
   <td>08-17-2012 01:36:10</td>
   <td><div align="center">TRX_KL34H4CT</div></td>
   <td width="250" align="center">Fund transfer of $23000 with Reference# TRX_KL34H4CT</td>
   <td width="50" align="center"></td>
   <td width="50" align="center">$23,000.00</td>
  </tr> 
  <tr class="row2"> 
   <td>06-12-2012 07:04:57</td>
   <td><div align="center">TRX_KFD3CT32</div></td>
   <td width="250" align="center">Fund transfer of $100000 with Reference# TRX_KFD3CT32</td>
   <td width="50" align="center"></td>
   <td width="50" align="center">$100,000.00</td>
  </tr> 
</tbody></table>
<p>&nbsp;</p>
<strong style="font-size:15px;">Available Credit Balance: $ <?php $a = number_format($account_balance, 2); echo $a; ?></strong>	


  

			</div>
			<div class="TabbedPanelsContent" style="display: none;">
				<h2>Funds Transfer</h2>
<p>Funds transfer is a process of transfering funds from your account to other account in same Bank or other bank.<br>Please make sure that you have enough funds available in your account to transfer. Also don't forget to validate receiver's account number.</p>

<link href="./files/SpryValidationTextField.css" rel="stylesheet" type="text/css">
<script src="./files/SpryValidationTextField.js.download" type="text/javascript"></script>

<script src="./files/jquery.min.js.download" type="text/javascript"></script>

<link href="./files/SpryValidationTextarea.css" rel="stylesheet" type="text/css">
<script src="./files/SpryValidationTextarea.js.download" type="text/javascript"></script>

<link href="./files/SpryValidationSelect.css" rel="stylesheet" type="text/css">
<script src="./files/SpryValidationSelect.js.download" type="text/javascript"></script>

<div id="errorCls" style="color:#FF0000 !important;font-size:14px;font-weight:bold;">&nbsp;</div>
<div style="color:#99FF00 !important;font-size:14px;font-weight:bold;">&nbsp;</div>

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <table width="550" border="0" cellpadding="5" cellspacing="1" class="entryTable">
      <tbody><tr id="listTableHeader">
        <th colspan="2">Transfer Funds</th>
      </tr>
      <tr>
      <td width="200" height="30" class="label"><strong>Sender's Account Number</strong></td>
        <td height="30" class="content">
          <input name="saccno" type="text" readonly="true" id="saccno" disabled="disabled" value="<?php echo $account_number; ?>" size="20">
          <span class="textfieldRequiredMsg"><?php echo $r_account_err; ?></span>
		</td>
        
      </tr>
	  
	  <tr>
    <td width="200" height="30" class="label"><strong>Receiver's Account Number</strong></td>
        <td height="30" class="content">
        <span id="xxx_accno">
            <input name="r_account" type="text" id="accno" size="20" maxlength="20" onmouseout="showHint2(this.value)"
             onkeyup="showHint(this.value)">
            <br>
            <span class="textfieldRequiredMsg">Account Number is required.</span>
			
		</span>
		</td>
        
      </tr>
	  <tr>
    <td width="200" height="30" class="label"><strong>Receiver's Name</strong></td>
        <td height="30" class="content">		
		<span id="xxx_rname">
            <input name="rname" type="text" size="30" maxlength="30" id="txtHint" disabled="disabled">
            <br>
            <span class="textfieldRequiredMsg">Receiver's Name is required.</span>
		
		</span>
		</td>
    </tr>	  
	  
	  <tr>
        
      </tr>

      <td width="200" height="30" class="label"><strong>Receiver's Bank Name</strong></td>
        <td height="30" class="content">		
		<span id="xxx_rbname">
            <input name="rbname" type="text" size="30" maxlength="30" id="txtHint2" disabled="disabled">
            <br>
            <span class="textfieldRequiredMsg">Receiver's Bank Name is required.</span>
			
		</span>
		</td>
      
      
	  <tr>
        <td width="200" height="30" class="label"><strong>Amount to Transfer USD$</strong></td>
        <td height="30" class="content">
		<span id="xxx_amt">
            <input name="t_amount" id="amt" type="text" size="20" maxlength="30">
            <br>
            <span class="textfieldRequiredMsg"><?php echo $t_amount_err; ?></span>
            <span class="textfieldRequiredMsg"><?php echo $account_balance_err; ?></span>
		</span>
		</td>
      </tr>
	  
	  <tr>
        <td width="200" height="30" class="label"><strong>Fund Transfer Option</strong></td>
        <td height="30" class="content">
		<span id="spryselect_opt">
			<select name="t_option" id="t_option">
				<option value="">-- Please select your option --</option>
				<option value="Local Transfer">Local Transfer</option>
				<option value="International Transfer">International Transfer</option>
			</select>
			<br>
			<span class="selectRequiredMsg"><?php echo $t_option_err; ?></span>
		</span>
		</td>
      </tr>
	  
	 
	  
	  <tr>
        <td width="200" height="30" class="label"><strong>Transfer Description</strong></td>
        <td height="30" class="content">
		<span id="xxx_desc">
            <textarea name="details" id="desc" cols="35" rows="2" maxlength="30"></textarea>
            <br>
            <span class="textareaRequiredMsg">Description is required.</span>
          
		</span>
		</td>
      </tr>
	  
      <tr>
        <td height="30">&nbsp;</td>
        <td height="30"><input name="submitButton" type="submit" id="submitButton" value="Transfer Funds"></td>
      </tr>
	</tbody></table>
</form>
  
<script type="text/javascript">
<!--
var sprytf_rbname = new Spry.Widget.ValidationTextField("sprytf_rbname", 'none', {minChars:6, validateOn:["blur", "change"]});
var sprytf_rname = new Spry.Widget.ValidationTextField("sprytf_rname", 'none', {minChars:6, validateOn:["blur", "change"]});
var sprytf_accno = new Spry.Widget.ValidationTextField("sprytf_accno", 'integer', {minChars:10, maxChars: 10, validateOn:["blur", "change"]});
var sprytf_swift = new Spry.Widget.ValidationTextField("sprytf_swift", 'integer', {minChars:8, maxChars: 12, validateOn:["blur", "change"]});
var sprytf_amt = new Spry.Widget.ValidationTextField("sprytf_amt", "none", {validateOn:["blur", "change"]});

var sprytf_opt = new Spry.Widget.ValidationSelect("spryselect_opt");

var sprytf_dot = new Spry.Widget.ValidationTextField("sprytf_dot", "date", {format:"mm-dd-yyyy", useCharacterMasking: true, validateOn:["blur", "change"]});
var sprytf_desc = new Spry.Widget.ValidationTextarea("sprytf_desc", {isRequired:true, validateOn:["blur", "change"]});
//-->
</script>

<script type="text/javascript">
$(document).ready(function(){
	$('#amt').keyup(function(e){
		$(this).val(format($(this).val()));
    });
	var format = function(num){
		var str = num.toString().replace("$", ""), parts = false, output = [], i = 1, formatted = null;
		if(str.indexOf(".") > 0) {
			parts = str.split(".");
			str = parts[0];
		}
		str = str.split("").reverse();
		for(var j = 0, len = str.length; j < len; j++) {
			if(str[j] != ",") {
				output.push(str[j]);
				if(i%3 == 0 && j < (len - 1)) {
					output.push(",");
				}
				i++;
			}
		}
		formatted = output.reverse().join("");
		return("$" + formatted + ((parts) ? "." + parts[1].substr(0, 2) : ""));
	};

});//ready
</script>			</div>
		</div>
	</div>

	
<script language="JavaScript" type="text/javascript">
	var tp1 = new Spry.Widget.TabbedPanels("AccountSummaryPanel", { defaultTab: 0});
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