<?php
// Include config file
require_once "conn.php";

// Define variables and initialize with empty values 
$firstname = $lastname = $password1 = $password2 = $email = $phone = $dateofbirth = $gender = "";
$address = $country = $state = $zip = $account_type = $account_pin = $account_pin2 = $picture = "";

// Define error variables and initialize with empty values
$firstname_err = $lastname_err = $password1_err = $password2_err = $email_err = $phone_err = "";
$dateofbirth_err = $gender_err = $address_err = $country_err = $state_err = $zip_err = "";
$account_type_err = $account_pin_err = $account_pin2_err = $picture_err = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 	 
	 // Validate f.name and l.name
    if(empty(trim($_POST["firstname"]))){
        $firstname_err = "Please enter your Firstname.";     
    } else{
        $firstname = trim($_POST["firstname"]);
    }
	if(empty(trim($_POST["lastname"]))){
        $lastname_err = "Please enter your Lastname.";     
    } else{
        $lastname = trim($_POST["lastname"]);
    }
	
	 // Validate password and confirm password
    if(empty(trim($_POST["password1"]))){
        $password1_err = "Please enter a Password.";     
    } elseif(strlen(trim($_POST["password1"])) < 6){
        $password1_err = "Password must have atleast 6 characters.";
    } else{
        $password1 = trim($_POST["password1"]);
    }
    
    if(empty(trim($_POST["password2"]))){
        $password2_err = "Please Confirm Password.";     
    } else{
        $password2 = trim($_POST["password2"]);
        if(empty($password1_err) && ($password1 != $password2)){
            $password2_err = "Passwords did not match.";
        }
    }
	
	
	// Validate email
    if(empty(trim($_POST["email"]))){
        $email_err = "Please enter your Email";
    } else {
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE email = :email";
        
        if($stmt = $pdo->prepare($sql)){
		
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);
            
            // Set parameters
            $param_email = trim($_POST["email"]);
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                if($stmt->rowCount() == 1){
                    $email_err = "This email already exists";
                } else{
                    $email = trim($_POST["email"]);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
         
        // Close statement
        unset($stmt);
    }
	
	// Validate phone
    if(empty(trim($_POST["phone"]))){
        $phone_err = "Please enter your Phone Number";     
    }/*  elseif (!(is_numeric(trim($_POST["phone"])))) {
		$phone_err = "Please input numbers";
	} */ else {
        $phone = trim($_POST["phone"]);
    }
	
	// Validate DOB
    if(empty(trim($_POST["dateofbirth"]))){
        $dateofbirth_err = "Please enter your Date of Birth";     
    } else {
        $dateofbirth = trim($_POST["dateofbirth"]);
    }
	
	// Validate Gender
    if(empty(trim($_POST["gender"]))){
        $gender_err = "Please select your Gender";     
    } else{
        $gender = trim($_POST["gender"]);
	}
	
	// Validate Address
	if(empty(trim($_POST["address"]))){
        $address_err = "Please enter your address.";     
    } else{
        $address = trim($_POST["address"]);
	}
	
	// Validate Country
	if(empty(trim($_POST["country"]))){
	$country_err = "Please enter your country.";     
	} else{
	$country = trim($_POST["country"]);
	}
	
	// Validate State
	if(empty(trim($_POST["state"]))){
	$state_err = "Please enter your state";     
	} else{
	$state = trim($_POST["state"]);
	}
	
	// Validate Zip
	if(empty(trim($_POST["zip"]))){
	$zip_err = "Please enter your Zip Code";     
	} else{
	$zip = trim($_POST["zip"]);
	}
	
    // Validate Account type
    if(empty(trim($_POST["account_type"]))){
    $account_type_err = "Please select your Account type";     
    } else{
    $account_type = trim($_POST["account_type"]);
    }
	
	 // Validate PIN and PIN2
	 if(empty(trim($_POST["account_pin"]))){
        $account_pin_err = "Please enter a PIN";     
    } elseif((strlen(trim($_POST["account_pin"])) != 4)) {
        $account_pin_err = "PIN must be 4 numbers";
    } else{
        $account_pin = trim($_POST["account_pin"]);
    }
    
    if(empty(trim($_POST["account_pin2"]))){
        $account_pin2_err = "Please Confirm PIN";     
    } else{
        $account_pin2 = trim($_POST["account_pin2"]);
        if(empty($account_pin_err) && ($account_pin_err != $account_pin2_err)){
            $account_pin2_err = "PINs do not match.";
        }
    }
	
	
  // Check input errors before inserting in database
   if(empty($firstname_err) && empty($lastname_err) && empty($password1_err) && empty($password2_err) && empty($email_err) && empty($phone_err) 
   && empty($dateofbirth_err) && empty($gender_err) && empty($address_err) && empty($country_err) && empty($state_err) && empty($zip_err) 
   && empty($account_type_err) && empty($account_pin_err) && empty($account_pin2_err)) {
        try {
            $pdo->beginTransaction();
             // Prepare an insert statement
            $sql = "INSERT INTO users (firstname, lastname, password, email, phone, dateofbirth, gender, address, country, state, zip, account_type, account_pin) 
			VALUES (:firstname, :lastname, :password, :email, :phone, :dateofbirth, :gender, :address, :country, :state, :zip, :account_type, :account_pin)";
            
            if($stmt = $pdo->prepare($sql)){
                // Bind variables to the prepared statement as parameters
				$stmt->bindParam(":firstname", $param_firstname, PDO::PARAM_STR);
				$stmt->bindParam(":lastname", $param_lastname, PDO::PARAM_STR);
                $stmt->bindParam(":password", $param_password, PDO::PARAM_STR);
                $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);
				$stmt->bindParam(":phone", $param_phone, PDO::PARAM_INT);
				$stmt->bindParam(":dateofbirth", $param_dateofbirth, PDO::PARAM_INT);
                $stmt->bindParam(":gender", $param_gender, PDO::PARAM_STR);
				$stmt->bindParam(":address", $param_address, PDO::PARAM_STR);
				$stmt->bindParam(":country", $param_country, PDO::PARAM_STR);
				$stmt->bindParam(":state", $param_state, PDO::PARAM_STR);
				$stmt->bindParam(":zip", $param_zip, PDO::PARAM_INT);
				$stmt->bindParam(":account_type", $param_account_type, PDO::PARAM_STR);
				$stmt->bindParam(":account_pin", $param_account_pin, PDO::PARAM_INT);
				
                // Set parameters
				$param_firstname = $firstname;
				$param_lastname = $lastname;
				$param_password = $password1;
                //$param_password = password_hash($password1, PASSWORD_DEFAULT); // Creates a password hash
                $param_email = $email;
                $param_phone = $phone;
                $param_dateofbirth = $dateofbirth;
				$param_gender = $gender;
				$param_address = $address;
				$param_country = $country;
				$param_state = $state;
				$param_zip = $zip;
				$param_account_type = $account_type;
				$param_account_pin = $account_pin;
				

                // Attempt to execute the prepared statement
                if($stmt->execute()){                    
                   
                    // Redirect to login page
                                    
                    $pdo->commit();

                    //include 'regmail.php';
                    
                    header("location: login.php?success=Registration was Successful; Await Account Approval");
                } else{
                    echo "Account not Created. Please try again later.";
                }
            }
            
            // Close statement
            unset($stmt);
        } catch(PDOException $e) {
             $pdo->rollBack();
            header("location: register.php?error={$e->getMessage()}");
        }
       
    }
    
    // Close connection
    unset($pdo);
}

?>



<!DOCTYPE html>
<html lang="en" class="nojs">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Create an Account Online</title>

<link href="./css/admin.css" rel="stylesheet" type="text/css">
<link href="./css/styles.css" rel="stylesheet" type="text/css">

<link href="./library/spry/textfieldvalidation/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
<script src="./library/spry/textfieldvalidation/SpryValidationTextField.js" type="text/javascript"></script>

<link href="./library/spry/passwordvalidation/SpryValidationPassword.css" rel="stylesheet" type="text/css" />
<script src="./library/spry/passwordvalidation/SpryValidationPassword.js" type="text/javascript"></script>

<link href="./library/spry/selectvalidation/SpryValidationSelect.css" rel="stylesheet" type="text/css" />
<script src="./library/spry/selectvalidation/SpryValidationSelect.js" type="text/javascript"></script>

<link href="./library/spry/textareavalidation/SpryValidationTextarea.css" rel="stylesheet" type="text/css" />
<script src="./library/spry/textareavalidation/SpryValidationTextarea.js" type="text/javascript"></script>

<link href="./library/spry/confirmvalidation/SpryValidationConfirm.css" rel="stylesheet" type="text/css" />
<script src="./library/spry/confirmvalidation/SpryValidationConfirm.js" type="text/javascript"></script>

</head>

<body style="background-color:#ECECEC;margin-top:50px;">
<table width="900" border="0" align="center" cellpadding="0" cellspacing="1" class="graybox">
 <tr style="background-color:#FFFFFF"> 
  <td><img src="./images/OnlineBanking-logo.png"/></td>
 </tr>
 <tr> 
  <td valign="top"> 
  <table width="100%" border="0" cellspacing="0" cellpadding="20">
    <tr> 
     <td class="contentArea">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">    
      	<h2 align="center"><strong>Register Account: </strong></h2>
      	<p align="center">Please register your account with us to take the advantage of our Online Banking facilities.</p>
	  	<div class="errorMessage" align="center">&nbsp;</div>
	  
       <table width="550" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#336699" class="entryTable">
        
        <tr> 
         <td class="contentArea"> 
		 
		 <table width="550" border="0" cellspacing="0" cellpadding="5" class="entryTable">
          <tr id="entryTableHeader">
            <th colspan="2">Personal Information</th>
          </tr>
          <tr>
            <td width="120" height="30" class="label"><label for="accno"><strong>First Name</strong></label></td>
            <td height="30" class="content">
			<span id="sprytf_firstname">
            <input name="firstname" value="<?php echo $firstname; ?>" type="text" class="frmInputs" id="accno" size="40" maxlength="30" />
            <br/>
            <span class="textfieldRequiredMsg"><?php echo $firstname_err; ?></span>
			</span>
			</td>
		  </tr>
		  
		  <tr>
            <td width="120" height="30" class="label"><label for="accno"><strong>Last Name</strong></label></td>
            <td height="30" class="content">
			<span id="sprytf_lastname">
            <input name="lastname" value="<?php echo $lastname; ?>" type="text" class="frmInputs" id="accno" size="40" maxlength="30" />
            <br/>
            <span class="textfieldRequiredMsg"><?php echo $lastname_err; ?></span>
			</span>
			</td>
		  </tr>
		  
          <tr>
            <td height="30" class="label"><label for="pass"><strong>Password</strong></label></td>
            <td height="30" class="content">
			<span id="sprypwd"> 
            <input name="password1" value="<?php echo $password1; ?>" type="password" class="frmInputs" id="pass" size="30" /><br />
            <span class="passwordMaxCharsMsg"><?php echo $password1_err; ?></span>
			</span>
			</td>
          </tr>
		  
		  <tr>
            <td height="30" class="label"><label for="pass"><strong>Confirm Password</strong></label></td>
            <td height="30" class="content">
			<span id="sprycpwd"> 
              <input name="password2" value="<?php echo $password2; ?>" type="password" class="frmInputs" id="pass" size="30" /><br />
              <span class="confirmRequiredMsg"><?php echo $password2_err; ?></span>
			</span>
			</td>
          </tr>
		  
		  <tr>
            <td width="120" height="30" class="label"><label for="accno"><strong>Email ID</strong></label></td>
            <td height="30" class="content">
			<span id="sprytf_email">
            <input name="email" value="<?php echo $email; ?>" type="text" class="frmInputs" id="accno" size="30" maxlength="30" />
            <br/>
            <span class="textfieldRequiredMsg"><?php echo $email_err; ?></span>
			</span>
			</td>
		  </tr>
		  <tr>
            <td width="120" height="30" class="label"><label for="accno"><strong>Phone Number</strong></label></td>
            <td height="30" class="content">
			<span id="sprytf_phone">
            <input name="phone" value="<?php echo $phone; ?>" type="text" class="frmInputs" id="accno" size="20" maxlength="30" /><small> ie +1xxx_xxx _xxxx</small>
            <br/>
            <span class="textfieldRequiredMsg"><?php echo $phone_err; ?></span>
			</span>
			</td>
		  </tr>
		  
		  <tr>
            <td width="120" height="30" class="label"><label for="accno"><strong>Date of Birth</strong></label></td>
            <td height="30" class="content">
			<span id="sprytf_dob">
            <input name="dateofbirth" value="<?php echo $dateofbirth; ?>" type="text" class="frmInputs" id="accno"  size="20" maxlength="30" />
            <br/>
            <span class="textfieldRequiredMsg"><?php echo $dateofbirth_err; ?></span>
			</span>
			</td>
		  </tr>
		  
		  <tr>
            <td width="120" height="30" class="label"><label for="accno"><strong>Profile Pics</strong></label></td>
            <td height="30" class="content">
			<span id="sprytf_lastname">
            <input name="pic" type="file" class="frmInputs"  size="30" maxlength="30" />
            <br/>
            <span class="textfieldRequiredMsg"><?php echo $picture_err; ?></span>			
			</span>
			</td>
		  </tr>
		  
		  <tr>
            <td width="120" height="30" class="label"><label for="accno"><strong>Gender</strong></label></td>
            <td height="30" class="content">
			<span id="spryselect_gender">
			  <select name="gender" id="gender">
					<option value="">Please select your gender</option>
					<option value="Male">Male</option>
					<option value="Female">Female</option>
			  </select>
			 <br/>
			 <span class="selectRequiredMsg"><?php echo $gender_err; ?></span>
			</span>
			</td>
		  </tr>
		  
		  
		  
		  <!-- Address Info -->
		  <tr id="entryTableHeader">
            <th scope="col" colspan="2">Address Information</th>
          </tr>
          
		  <tr>
            <td width="120" height="30" class="label"><label for="accno"><strong>Address</strong></label></td>
            <td height="30" class="content">
			<span id="spryta_address">
				<textarea name="address" value="<?php echo $address; ?>" id="textarea1" cols="35" rows="2"></textarea>
  			<br/>
            <span class="textareaRequiredMsg"><?php echo $address_err; ?></span>
			</span>
			</td>
		  </tr>
		  
		  <tr>
            <td width="120" height="30" class="label"><label for="accno"><strong>Country Name</strong></label></td>
            <td height="30" class="content">
			<span id="sprytf_city">
            <input name="country" value="<?php echo $country; ?>" type="text" class="frmInputs" id="accno" size="30" maxlength="30" />
            <br/>
            <span class="textfieldRequiredMsg"><?php echo $country_err; ?></span>
			</span>
			</td>
		  </tr>
		  
		  <tr>
            <td width="120" height="30" class="label"><label for="accno"><strong>State</strong></label></td>
            <td height="30" class="content">
			<span id="sprytf_state">
            <input name="state" value="<?php echo $state; ?>" type="text" class="frmInputs" id="accno"  size="30" maxlength="30" />
            <br/>
            <span class="textfieldRequiredMsg"><?php echo $state_err; ?></span>
			</span>
			</td>
		  </tr>
		  
		  <tr>
            <td width="120" height="30" class="label"><label for="accno"><strong>Zip Code</strong></label></td>
            <td height="30" class="content">
			<span id="sprytf_zip">
            <input name="zip" value="<?php echo $zip; ?>" type="text" class="frmInputs" id="accno" size="15" maxlength="30" />
            <br/>
            <span class="textfieldRequiredMsg"><?php echo $zip_err; ?></span>
			</span>
			</td>
		  </tr>
		  
		  
		  <!-- Account Information Info -->
		  <tr id="entryTableHeader">
            <th colspan="2">Bank Account Information</th>
          </tr>
          
		  <tr>
            <td width="120" height="30" class="label"><label for="accno"><strong>Account Type</strong></label></td>
            <td height="30" class="content">
			<span id="spryselect_acctype">
			  <select name="account_type" id="account_type">
					<option value="">Please select Account Type</option>
					<option value="CA">Checking Account</option>
					<option value="SA">Saving Account</option>
					<option value="FDA">Fixed deposit Account</option>
			  </select>
			 <br/>
			 <span class="selectRequiredMsg"><?php echo $account_type_err; ?></span>
			</span>
			</td>
		  </tr>
		  
		  <tr>
            <td width="120" height="30" class="label"><label for="accno"><strong>Account Pin </strong></label></td>
            <td height="30" class="content">
			<span id="sprytf_pin">
            <input name="account_pin" value="<?php echo $account_pin; ?>" type="text" class="frmInputs" id="accno"  size="20" maxlength="30" />
            <br/>
            <span class="textfieldRequiredMsg"><?php echo $account_pin_err; ?></span>
			</span>
			</td>
		  </tr>
		  
		  <tr>
            <td width="120" height="30" class="label"><label for="accno"><strong>Verify Pin Number</strong></label></td>
            <td height="30" class="content">
			<span id="sprytf_cpin">
            <input name="account_pin2" value="<?php echo $account_pin2; ?>" type="text" class="frmInputs" id="accno" size="20" maxlength="30" />
            <br/>
			<span class="confirmRequiredMsg"><?php echo $account_pin2_err; ?></span>
			</span>
			</td>
		  </tr>
		  
		  <tr>
            <td width="120" height="30" class="label">&nbsp;</td>
            <td height="30" class="content">
			If you already have an Account with us, please <a href="login.php">Login Now</a>.
			</td>
          </tr>
          <tr>
            <td width="120" height="30">&nbsp;</td>
            <td height="30">
			<input name="submitButton" type="submit" class="frmButton" id="submitButton" value="Register Account!" />
			</td>
          </tr>
        </table>
		 
		  </td>
        </tr>
       </table>
       <p>&nbsp;</p>
      </form></td>
    </tr>
	<tr>
		<td class="contentArea" style="border-top:#999999 thin dashed;">
		
		</td>
	</tr>
   </table>
   
   </td>
 </tr>
</table>
<p>&nbsp;</p>
<script type="text/javascript">
<!--
//Firstname
var sprytf_firstname = new Spry.Widget.ValidationTextField("sprytf_firstname", 'none', {validateOn:["blur", "change"]});
//Lastname
var sprytf_lastname = new Spry.Widget.ValidationTextField("sprytf_lastname", 'none', {validateOn:["blur", "change"]});
//Password
var sprypass1 = new Spry.Widget.ValidationPassword("sprypwd", {minChars:6, maxChars: 12, validateOn:["blur", "change"]});
//Confirm Password
var spryconf1 = new Spry.Widget.ValidationConfirm("sprycpwd", "sprypwd", {minChars:6, maxChars: 12, validateOn:["blur", "change"]});
//Email ID
var spryemail = new Spry.Widget.ValidationTextField("sprytf_email", 'email', {validateOn:["blur", "change"]});

//Date of Birth
var sprydob = new Spry.Widget.ValidationTextField("sprytf_dob", 'date', {format:"mm-dd-yyyy", useCharacterMasking: true, validateOn:["blur", "change"]});
//Gender
var sprygender = new Spry.Widget.ValidationSelect("spryselect_gender");


//address
var spry_ad = new Spry.Widget.ValidationTextarea("spryta_address", {isRequired:true});
//city
var sprytf_city = new Spry.Widget.ValidationTextField("sprytf_city", 'none', {validateOn:["blur", "change"]});
//State
var sprytf_state = new Spry.Widget.ValidationTextField("sprytf_state", 'none', {validateOn:["blur", "change"]});
//ZipCode
var sprytf_zip = new Spry.Widget.ValidationTextField("sprytf_zip", 'integer', {validateOn:["blur", "change"]});

//Account Type
var spryselect1 = new Spry.Widget.ValidationSelect("spryselect_acctype");
//Account Number
var spry_accno = new Spry.Widget.ValidationTextField("sprytf_accno", 'integer', {minChars:8, maxChars: 12, validateOn:["blur", "change"]});

var spry_pin = new Spry.Widget.ValidationTextField("sprytf_pin", 'integer', {minChars:4, maxChars: 6, validateOn:["blur", "change"]});
//Confirm Password
var spry_cpin = new Spry.Widget.ValidationConfirm("sprytf_cpin", "sprytf_pin", {minChars:4, maxChars: 6, validateOn:["blur", "change"]});

//-->
</script>
</body>