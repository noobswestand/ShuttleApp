<?php
include_once "db.php";
$already_registered=false;
$not_exist=false;
$registering=false;
$id=-1;


//Final part of registration
if (isset($_POST["type"]) && $_POST["type"]=="confirmPassword" && isset($_SESSION["name"]) && isset($_SESSION["id"]) && isset($_POST["pass1"]) && isset($_POST["pass2"]) && isset($_POST["phone"])){
	dbConnect();
	if (!empty($_POST["pass1"]) && !empty($_POST["pass2"])){
		$pass1=clean($_POST["pass1"]);
		$pass2=clean($_POST["pass2"]);
		
		$phone=clean($_POST["phone"]);
		
		if ($pass1==$pass2){
			//Insert the correct data into the database
			
			$name=$_SESSION["name"];
			$id=$_SESSION["id"];
			
			//Get if they are an assigned driver
			$sql="SELECT Driver FROM studentidtable WHERE StudentID=$id;";
			$result=query($sql);
			$driver=$result[0]["Driver"];
			
			$pass=password_hash($pass1,PASSWORD_BCRYPT);
			$sql="INSERT INTO student(IDNumber,Name,Password,StudentID,Phone,Driver) VALUES(null,'$name','$pass',$id,'$phone',$driver);";
			insert($sql);
			//Make them a pretty message
			?>
			<div class="modal" id="myModal" role="dialog">
				<div class="modal-dialog modal-sm  modal-dialog-centered">
					<!-- Modal content-->
					<div class="modal-content" style="box-shadow:none;border-radius:0px; background-color:#435159;">
						<div class="modal-body">
							<h1>Account Created!</h1>
							<button id="submitButton" type="button" class="btn btn-default center-block" onclick="login()">Login</button>
						</div>
					</div>
				</div>
			</div>
			  <div style="visibility:hidden;">
				<form id="registerForm2" method="POST" action="index.php">
				<input type="text" name="username" value="<?php echo $id?>">
				<input type="text" name="password" value="<?php echo $pass1?>">
				<input type="hidden" name="type" value="login">
				</form>
			  </div>
			  <script>
			  $('#myModal').modal('toggle');
			  function login(){
				  document.getElementById("registerForm2").submit();
			  }
			  setTimeout(function() {
				$("#bodyLogin").hide();
				$("#bodyRegister").show();
				$("#register").css("background-color", "#1ab188");
				$("#login").css("background-color", "#435159");
			}, 25);
			  </script>
			
			<?php
		}else{
			die("Passwords did not match!");
		}
	}else{
		die("Please fill in all fields!");
	}
	dbClose();
}

//Pressing the back button for invalid id / already registered
if (isset($_POST["type"])&&$_POST["type"]=="backRegister"){
	?>
	<script>
	setTimeout(function() {
		$("#bodyLogin").hide();
		$("#bodyRegister").show();
		$("#register").css("background-color", "#1ab188");
		$("#login").css("background-color", "#435159");
	}, 25);
	</script>
	<?php
}


//Entering ID#
if (isset($_POST["id"])&&isset($_POST["type"])
	&&$_POST["type"]=="register"){
	dbConnect();
	$id=(int)clean($_POST["id"]);
	if (empty($id)){
		$not_exist=true;
	}
	//check to see if thier ID is valid;
	if ($not_exist==false){
		$sql="SELECT * FROM studentidtable WHERE StudentID=$id";
		$result=query($sql);
		if (empty($result)){
			$not_exist=true;
		}
	}
	
	if ($not_exist==false){
		//check to see if the user already exists
		$sql="SELECT * FROM student WHERE StudentID=$id";
		$result=query($sql);
		if (!empty($result)){
			$already_registered=true;
		}
	}
	
	
	if ($not_exist==false && $already_registered==false){
		$registering=true;
		$_SESSION["id"]=$id;
		//hide log in button
		?>
		<script>
		setTimeout(function() {
			$("#login").hide();
			showRegister();
		}, 50);
			
		</script>
		<?php
	}
	
	dbClose();
}


//Initial register page
if ($registering==false && $not_exist==false && $already_registered==false){
	?>
	<h1 id="loginMessage">
	<?php
	$messages=array("Welcome!","Getting Started?","Get Started","Start Riding");
	echo $messages[array_rand($messages)];
	?>
	</h1>

	<form method="POST" action="index.php">

	<br>
	<input type="text" name="id" placeholder="*Student ID#" required autocomplete="off">
	<br>
	<br>
	<input type="hidden" name="type" value="register">
	<input type="submit" value="Register">
	</form>
	<div id="registerSpacer"></div>
	<?php
}


//Setting a password + phone
if ($registering==true){
	
	//passwords do not match pop-up
	?>
	
	
	<!-- Modal -->
	<div class="modal" id="myModal" role="dialog">
		<div class="modal-dialog modal-sm  modal-dialog-centered">
			<!-- Modal content-->
			<div class="modal-content" style="box-shadow:none;border-radius:0px; background-color:#435159;">
				<div class="modal-body">
					<h1>The passwords you entered did not match!</h1>
					<button id="submitButton" type="button" class="btn btn-default center-block" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
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
	
	
	
	<h1 id="loginMessage">
	<?php
	dbConnect();
	$sql="SELECT Name FROM studentidtable WHERE StudentID=$id";
	$name=query($sql)[0]["Name"];
	dbClose();
	$messages=array("Welcome, $name!","Hello, $name!");
	$_SESSION["name"]=$name;
	
	echo $messages[array_rand($messages)];
	?></h1>
	<form method="POST" action="index.php" id="registerForm">
	<input type="text" name="phone" id="phone" required placeholder="*Phone#">
	<br><br>
	<input type="password" name="pass1" id="pass1" required placeholder="*Password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters">
	<br><br>
	<input type="password" name="pass2" id="pass2" required placeholder="*Confirm Password">
	<input type="hidden" name="type" value="confirmPassword">
	<br><br>
	</form>
	
	<button id="submitButton" onclick="submit_form()">Start Riding</button>
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
				document.getElementById("registerForm").submit();
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



//ID does not exist
if ($not_exist==true){
	?>
	<form method="POST" action="index.php">
	<h1 id="loginMessage">That account does not exist.</h1>
	<input type="hidden" name="type" value="backRegister">
	<input type="submit" value="Back">
	</form>
	
	<script>
	setTimeout(function() {
		$("#login").hide();
		showRegister();
	}, 50);
	</script>
	<div id="registerSpacer"></div>
	
	<?php
}

//Account already registered
if ($already_registered==true){
	?>
	<form method="POST" action="index.php">
	<h1 id="loginMessage">You are already registered!</h1>
	<input type="hidden" name="type" value="backRegister">
	<input type="submit" value="Back">
	</form>
	
	<script>
	setTimeout(function() {
		$("#login").hide();
		showRegister();
	}, 50);
	</script>
	<div id="registerSpacer"></div>
	
	<?php
}

?>
<script>adjustDiv();</script>
