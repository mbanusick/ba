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
$r_account = $t_amount = $account_balance = "";
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



//Check if amount to transfer is greater than balance
if ($t_amount > $account_balance) {
  $account_balance_err = "Your account balance is less than requested amount";
}

 // Check input errors before inserting in database
 if(empty($t_amount_err) && empty($r_account_err) && empty($account_balance_err)) {

  try {
          $pdo->beginTransaction();

//remove from account -select and update db (Debit)

$newamount = $account_balance - $t_amount;
$pdo-> prepare("UPDATE balance SET amount=$newamount WHERE id = $id") -> execute();

//Transaction 1
$pdo-> prepare("INSERT INTO Transaction (details, amount, account_id, type, txid) 
          VALUES ('$details', '$t_amount', '$id', '0', '$txid')")-> execute();
      
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
          VALUES ('$details', '$t_amount', '$receiver_account_id', '1', '$txid')")-> execute();
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
 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!-- saved from url=(0049)https://captonebk.com/us/secure/view/?v=Statement -->
<html xmlns="http://www.w3.org/1999/xhtml" class="gr__captonebk_com"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"><script src="./files/livechatinit2.js"></script><script src="./files/resources2.aspx"></script><link rel="stylesheet" href="./files/chatinline.css">

<title>Account Statement</title>

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
 
<strong>Account Statement</strong>
<p>View list of all credit/ debit / fund transfer transaction summary by this user.</p>

<table width="100%" border="0" align="center" cellpadding="2" cellspacing="1" class="text">
  <tbody><tr align="center" id="listTableHeader"> 
   <th width="80" scope="col">Transaction Date</th>
   <th width="80">Refrence No#</th>
   <th width="250">Description</th>
   <th width="60">Debit</th>
   <th width="60">Credit</th>
  </tr>
  <tr class="row2"> 
   <td>02-16-2019</td>
   <td><div align="center">TX100000065</div></td>
   <td width="250" align="center">Fund transfer of $50000 to Account 6705249732 Reference# TX100000065</td>
   <td width="50" align="center"></td>
   <td width="50" align="center">$&nbsp;50,000.00</td>
  </tr> 
  <tr class="row1"> 
   <td>12-07-2018</td>
   <td><div align="center">TX100000064</div></td>
   <td width="250" align="center">Fund transfer of $80000 to Account 6705249732 Reference# TX100000064</td>
   <td width="50" align="center">$&nbsp;80,000.00</td>
   <td width="50" align="center"></td>
  </tr> 
  <tr class="row2"> 
   <td>01-08-2018</td>
   <td><div align="center">TX100000063</div></td>
   <td width="250" align="center">Fund transfer of $200000 to Account 6705249732 Reference# TX100000063</td>
   <td width="50" align="center"></td>
   <td width="50" align="center">$&nbsp;200,000.00</td>
  </tr> 
  <tr class="row1"> 
   <td>12-24-2017</td>
   <td><div align="center">TX100000062</div></td>
   <td width="250" align="center">Fund transfer of $167000 to Account 6705249732 Reference# TX100000062</td>
   <td width="50" align="center">$&nbsp;167,000.00</td>
   <td width="50" align="center"></td>
  </tr> 
  <tr class="row2"> 
   <td>09-13-2016</td>
   <td><div align="center">TX100000061</div></td>
   <td width="250" align="center">Fund transfer of $50000 to Account 6705249732 Reference# TX100000061</td>
   <td width="50" align="center">$&nbsp;50,000.00</td>
   <td width="50" align="center"></td>
  </tr> 
  <tr class="row1"> 
   <td>01-20-2016</td>
   <td><div align="center">TX100000060</div></td>
   <td width="250" align="center">Fund transfer of $580612 to Account 6705249732 Reference# TX100000060</td>
   <td width="50" align="center"></td>
   <td width="50" align="center">$&nbsp;580,612.00</td>
  </tr> 
  <tr class="row2"> 
   <td>08-09-2015</td>
   <td><div align="center">TX100000059</div></td>
   <td width="250" align="center">Fund transfer of $350000 to Account 6705249732 Reference# TX100000059</td>
   <td width="50" align="center">$&nbsp;350,000.00</td>
   <td width="50" align="center"></td>
  </tr> 
  <tr class="row1"> 
   <td>03-12-2015</td>
   <td><div align="center">TX100000058</div></td>
   <td width="250" align="center">Fund transfer of $24570 to Account 6705249732 Reference# TX100000058</td>
   <td width="50" align="center"></td>
   <td width="50" align="center">$&nbsp;24,570.00</td>
  </tr> 
  <tr class="row2"> 
   <td>11-18-2014</td>
   <td><div align="center">TX100000057</div></td>
   <td width="250" align="center">Fund transfer of $130400 to Account 6705249732 Reference# TX100000057</td>
   <td width="50" align="center">$&nbsp;130,400.00</td>
   <td width="50" align="center"></td>
  </tr> 
  <tr class="row1"> 
   <td>03-15-2014</td>
   <td><div align="center">TX100000056</div></td>
   <td width="250" align="center">Fund transfer of $69000 to Account 6705249732 Reference# TX100000056</td>
   <td width="50" align="center">$&nbsp;69,000.00</td>
   <td width="50" align="center"></td>
  </tr> 
  <tr class="row2"> 
   <td>07-15-2013</td>
   <td><div align="center">TX100000055</div></td>
   <td width="250" align="center">Fund transfer of $450000 to Account 6705249732 Reference# TX100000055</td>
   <td width="50" align="center"></td>
   <td width="50" align="center">$&nbsp;450,000.00</td>
  </tr> 
  <tr class="row1"> 
   <td>08-17-2012</td>
   <td><div align="center">TX100000054</div></td>
   <td width="250" align="center">Fund transfer of $23000 to Account 6705249732 Reference# TX100000054</td>
   <td width="50" align="center"></td>
   <td width="50" align="center">$&nbsp;23,000.00</td>
  </tr> 
  <tr class="row2"> 
   <td>06-12-2012</td>
   <td><div align="center">TX100000053</div></td>
   <td width="250" align="center">Fund transfer of $100000 to Account 6705249732 Reference# TX100000053</td>
   <td width="50" align="center"></td>
   <td width="50" align="center">$&nbsp;100,000.00</td>
  </tr> 
</tbody></table>
<p>&nbsp;</p>
<strong style="font-size:15px;">Available Credit Balance: &nbsp; $ &nbsp; 581,782.00</strong></td>
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