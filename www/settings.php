<link rel="stylesheet" href="css/toggle.css">
<link rel="stylesheet" href="css/new.css">
<link rel="stylesheet" href="css/create.css">
<?php
include_once "db.php";

$state=0;
if (isset($_POST["type2"])){
	switch($_POST["type2"]){
		case "changePassword":
			$state=2;
		break;
		
		case "changePhone":
			$state=1;
		break;
		
		case "driver":
			if ($_SESSION["driver"]=="1"){
				$state=3;
			}
		break;
		
		case "saveDriver":
			dbConnect();
			$new=clean($_POST["new"]);
			$empty=clean($_POST["empty"]);
			$time=clean($_POST["time"]);
			$newB=1;
			if ($new=="undefined"){
				$newB=0;
			}
			$emptyB=1;
			if ($empty=="undefined"){
				$emptyB=0;
			}
			$timeB=1;
			$timeA=0;
			
			if ($time=="OFF"){
				$timeB=0;
			}else{
				$timeA=$time;
			}
			$id=$_SESSION["id"];
			$sql="UPDATE student SET Notify_new=$newB, Notify_empty=$emptyB, Notify_time=$timeB, Notify_time_amount=$timeA
			WHERE StudentID=$id;";
			insert($sql);
			dbClose();
			
			
			notify("Your settings have been saved!","Close");
			
			
		break;
		
		default:
			$state=0;
		break;
	}
}

//Resetting password
if (isset($_POST["pass1"])){
	dbConnect();
	//Update password of user
	$pass0=clean($_POST["pass0"]);
	$pass1=clean($_POST["pass1"]);
	$pass2=clean($_POST["pass2"]);
	$id=$_SESSION["id"];
	$sql="SELECT Password FROM student WHERE StudentID='$id'";
	$true_pass=query($sql)[0]["Password"];
	if ($pass1!=$pass2){
		notify("Passwords did not match","Back");
	}else{
		if (!password_verify($pass0,$true_pass)){
			notify("Incorrect password","Back");
		}else{
			$pass=password_hash($pass1,PASSWORD_BCRYPT);
			$sql="UPDATE student SET Password='$pass' WHERE StudentID=$id";
			insert($sql);
			notify("Your password has been changed!","OK");
		}
	}
	dbClose();
}

//Setting new phone #
if (isset($_POST["phone"])){
	dbConnect();
	$id=$_SESSION["id"];
	$phone=clean($_POST["phone"]);
	$sql="UPDATE student SET Phone='$phone' WHERE StudentID=$id";
	insert($sql);
	notify("Your Phone Number has been changed!","OK");
	dbClose();
}

//Driver settings
if ($state==3){
	dbConnect();
	$id=$_SESSION["id"];
	echo $id;
	$sql="SELECT * FROM student WHERE StudentID=$id;";
	$result=query($sql)[0];
	dbClose();
	
	$new=$result["Notify_new"];
	$empty=$result["Notify_empty"];
	$time=$result["Notify_time"];
	$timea=$result["Notify_time_amount"];
	?>
	<script>
	var global_day = "";
	var global_previousColor=0;
	var global_previousObj=0;
	function selectDay(time,obj){
		if (global_day==""){
			global_previousColor=$(obj).css("background-color");
			global_previousObj=obj;
			obj.style.backgroundColor="#4286f4";
		}else{
			global_previousObj.style.backgroundColor=global_previousColor;
			
			global_previousColor=$(obj).css("background-color");
			global_previousObj=obj;
			obj.style.backgroundColor="#4286f4";
		}
		global_day=time;
	}
	</script>
	<?php
	function createDay($t,$id="requestDay"){
		global $time;
		global $timea;
		
		$load="";
		if ($time=="0" && $t=="OFF"){
			$load="selectDay('$t',d$t)";
		}
		if ($time!="0" && $t==$timea){
			$load="selectDay('$t',d$t)";
		}
		?>
		<th>
		<div style="width:50px;" class="<?php echo $id;?>" id="d<?php echo $t;?>" onclick="selectDay('<?php echo $t;?>',this)">
			<p style="margin-top:13px;"><?php echo $t;?></p>
		</div>
		</th>
		<script>
			<?php echo $load;?>
		</script>
		
		<?php
	}
	?>
	
	<h1 onload="alert('asdf');">Notification Settings</h1>
	<table align="center">
    <tr>
		<th>
			<p>Notify me when a new ride is requested</p>
		</th>
		<th>
			<label class="switch">
				<input class="fnew" type="checkbox" <?php if ($new=="1"){echo "checked";}?> >
				<span class="slider"></span>
			</label>
		</th>
	 </tr>
	 <tr>
		<th>
			<p>Notify me when everyone from a ride has left</p>
		</th>
		<th>
			<label class="switch">
				<input class="fempty" type="checkbox" <?php if ($empty=="1"){echo "checked";}?>>
				<span class="slider"></span>
			</label>
		</th>
	</tr>
	</table>
	
	<hr>
	
	<table align="center">
	<tr>
		<th><p>Notify me ___ minutes before I have a ride scheduled</p></th>
	</tr>
	</table>
	<table align="center">
	<tr>
		<?php
		createDay("OFF");
		createDay("15");
		createDay("30");
		createDay("45");
		createDay("60");
		?>
	</tr>
	</table>
	
	<br>
	
	
	<!--Maybe not?-->
	<!--
	<p>Select your default vehicle</p>
	<select style="margin:auto;" id="placeFrom" name="placeFrom" onchange="checkOther()">
		<?php
		dbConnect();
		$sql="SELECT * FROM car;";
		$places=query($sql);
		dbClose();
		foreach ($places as $p){
			$r=$p["CarID"];
			$c=$p["Seats"];
			$l=$p["License"]." ($c seats)";
			
			echo "<option value='$r'>$l</option>";
		}		
		?>
	</select>
	-->
	
	<br><br>
	
	<form style="display: none;" id="newForm" method="POST" action="index.php">
	<input type="hidden" id="new" name="new" value="1">
	<input type="hidden" id="empty" name="empty" value="1">
	
	<input type="hidden" id="time" name="time" value="OFF">
	<input type="hidden" id="type" name="type2" value="saveDriver">
	</form>
	<button id="createButton" onclick="send_save()">Save</button>
	
	<script>
	function send_save(){
		document.getElementById("new").value=$('.fnew:checked').val();
		document.getElementById("empty").value=$('.fempty:checked').val();
		document.getElementById("time").value=global_day;
		document.getElementById("newForm").submit();
	}
	</script>
	
	
	<?php
}


//Change phone #
if ($state==1){
	dbConnect();
	$id=$_SESSION['pid'];
	$sql="SELECT Phone FROM student WHERE IDNumber=$id";
	$phone=query($sql)[0]["Phone"];
	dbClose();
	?>
	<h1>Enter a new phone:</h1>
	<p>Current: <?php echo $phone;?></p>
	
	<form method="POST" action="index.php">
	<input type="text" name="phone" required placeholder="*Phone#"></input>
	<br><br>
	<input type="submit" value="Change">
	</form>
	<?php
}

//Change password
if ($state==2){
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
	
	<h1>Change your password</h1>
	<form method="POST" action="index.php" id="registerForm">
	<p>Current password</p>
	<input type="password" name="pass0" required placeholder="Current Password"></input>
	<br><br>
	<p>New password</p>
	<input type="password" name="pass1" id="pass1" required placeholder="*Password"></input>	<br><br>
	<input type="password" name="pass2" id="pass2" required placeholder="*Confirm Password">
	<br><br>
	</form>
	<button id="submitButton" onclick="submit_form()">Change</button>
	
	
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



//Looking around
if ($state==0){
	?>
	<h1>Account settings</h1>
	<ul class="list-group align-items-center">
		<li class="list-group-item" onclick="changePwd()"><a>Change Password</a></li>
		<li class="list-group-item" onclick="changePhone()"><a>Change Phone Number</a></li>
		<?php
		if ($_SESSION["driver"]=="1"){
			?>
			<li class="list-group-item" onclick="driverSettings()"><a>Driver Settings</a></li>';
			<div style="visibility:hidden;">
			<form id="driverForm" method="POST" action="index.php">
			<input type="hidden" name="type2" value="driver">
			</form>
			</div>
			<script>
				function driverSettings(){
					document.getElementById("driverForm").submit();
				}
			</script>
			<?php
		}
		?>
	</ul>

	<div style="visibility:hidden;">
	<form id="pwdForm" method="POST" action="index.php">
	<input type="hidden" name="type2" value="changePassword">
	</form>
	</div>
	<div style="visibility:hidden;">
	<form id="phoneForm" method="POST" action="index.php">
	<input type="hidden" name="type2" value="changePhone">
	</form>
	</div>

	<script>
	function changePwd(){
		document.getElementById("pwdForm").submit();
	}
	function changePhone(){
		document.getElementById("phoneForm").submit();
	}
	</script>

	<?php
}
?>