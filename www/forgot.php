
<?php
include_once "db.php";
ini_set("SMTP","smtp.gmail.com");
ini_set("smtp_port","587");

function generateRandomString($length = 20) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

$resetStep=0;


if (isset($_POST["type"]) && $_POST["type"]=="resetPassword"){
	//check to make sure it is still valid
	dbConnect();
	$l=$_POST["valid"];
	$sql="SELECT * FROM reset WHERE Link='$l'";
	$result=query($sql);
	if (!empty($result)){
		$id=$result[0]["StudentID"];
		
		//Update password of user
		$pass1=clean($_POST["pass1"]);
		$pass2=clean($_POST["pass2"]);
		if ($pass1==$pass2){
			
			//Delete the valid link
			$sql="DELETE FROM reset WHERE Link='$l'";
			insert($sql);
			
			$pass=password_hash($pass1,PASSWORD_BCRYPT);
			$sql="UPDATE student SET Password='$pass' WHERE StudentID=$id";
			insert($sql);
			
			notify("Your password has been reset!","Back","msgReturn()");
		}else{
			notify("Passwords did not match!","Back","msgReturn()");
		}
		
	}else{
		notify("An error has occured!","Back","msgReturn()");
	}
	dbClose();
}

//Get new password
if (isset($_GET["l"]) && $_GET["l"]!=""){
	include_once "html.php";
	
	$l=$_GET["l"];
	dbConnect();
	
	//Check valid link
	$sql="SELECT * FROM reset WHERE Link='$l'";
	$result=query($sql);
	if (!empty($result)){
		//check time stamp
		$stime=$result[0]["Time"];
		$time=strtotime($stime)+86400;
		$time2 =time();
		if ($time>$time2){
			$resetStep=1;
		}else{
			//Delete the invalid link
			$sql="DELETE FROM reset WHERE Link='$l'";
			insert($sql);
			notify("The link has expired!","Back","msgReturn()");
		}
	}else{
		notify("Invalid link!","Back","msgReturn()");
	}
	
	dbClose();
}


if (isset($_POST["resetID"]) && $_POST["resetID"]!=""){
	$id=(int)$_POST["resetID"];
	
	dbConnect();
	$sql="SELECT Email FROM studentidtable WHERE StudentID=$id";
	$result2=query($sql);
	dbClose();
	
	if (!empty($result2)){
		
		//generate random link
		$link="";
		$r="";
		dbConnect();
		do{
			$r=generateRandomString();
			$sql="SELECT * FROM reset WHERE Link='$r'";
			$result=query($sql);
			$link="http://127.0.0.1/index.php?l=".$r;
		}while(!empty($result));
		
		$sql="INSERT INTO reset VALUES(null,$id,CURRENT_TIMESTAMP,'$r');";
		insert($sql);
		dbClose();
		
		$email=$result2[0]["Email"];
		
$msg="You have requested to reset your password.<br>
Please click the link below to set a new password.<br>
<a href='$link'>$link</a><br><br>
If you have not requested to reset your password, please ignore this email.<br>
Thank you<br>
-Lakeland Unversity Shuttle Services";
		
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
		$headers .= "From: Shuttle Services";
		$headers .= "Content-Transfer-Encoding: base64";
		
		
		if (mail($email,"Shuttle password reset",$msg,$headers)){
			notify("An email has been sent to your Lakeland email.","Back","msgReturn()");
		}else{
			notify("An error has occurred.","Back","msgReturn()");
		}
	}else{
		notify("That user does not exist!","Back");
	}
}

if ($resetStep==0){
	?>
	<h1 id="loginMessage" class="click">Forgot your password?</h1>

	<form method="POST" action="index.php">
	<br>
	<input type="text" name="resetID" placeholder="*Student ID#" required autocomplete="off">
	<br>
	<br>
	<input type="submit" value="Send Email">
	</form>
	<?php
}
if ($resetStep==1){
	?>
	
	<div class="modal" id="myModal2" role="dialog">
		<div class="modal-dialog modal-sm  modal-dialog-centered">
			<!-- Modal content-->
			<div class="modal-content" style="box-shadow:none;border-radius:0px; background-color:#435159;">
				<div class="modal-body">
					<h1>Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters</h1>
					<button id="submitButton" type="button" class="btn btn-default center-block" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
	
	<h1 id="loginMessage" class="click">Choose a new password</h1>
	
	<form method="POST" action="index.php" id="forgotForm">
	<input type="password" name="pass1" id="pass1" required placeholder="*Password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters">
	<br><br>
	<input type="password" name="pass2" id="pass2" required placeholder="*Confirm Password">
	<input type="hidden" name="type" value="resetPassword">
	<input type="hidden" name="valid" value="<?php echo $_GET["l"];?>">
	<br><br>
	<button id="submitButton" onclick="submit_form()">Reset</button>
	</form>
	
	<script>
	function submit_form(){
		if (document.getElementById("pass1").value==document.getElementById("pass2").value){
			var myInput=document.getElementById("pass1");
			// Validate lowercase letters
			var lowerCaseLetters = /[a-z]/g;
			var upperCaseLetters = /[A-Z]/g;
			var numbers = /[0-9]/g;
			if(myInput.value.match(lowerCaseLetters)
			&& (myInput.value.length >= 8)
			&& myInput.value.match(upperCaseLetters)
			&& myInput.value.match(numbers)) {  
				document.getElementById("forgotForm").submit();
			}else{
				$('#myModal2').modal('toggle');
			}
		}else{
			$('#myModal').modal('toggle');
		}
	}
	</script>
	<?php
}