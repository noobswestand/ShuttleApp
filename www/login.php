<?php
include_once "db.php";


if (isset($_POST["username"])&&isset($_POST["password"])&&isset($_POST["type"])
	&&$_POST["type"]=="login"){
	$error=false;
	dbConnect();
	$username=(int)clean($_POST["username"]);
	$password=clean($_POST["password"]);
	
	if (!empty($username)&&!empty($password)){
		$sql="SELECT Password FROM student WHERE StudentID='$username'";
		$result=query($sql);
		
		//exist
		if (empty($result)){
			$error=true;
		}else{
			$pass=$result[0]["Password"];
			//incorrect password
			if (!password_verify($password,$pass)){
				$error=true;
			}else{//correct password
			
				//check if they are a driver
				$sql="SELECT * FROM student WHERE StudentID='$username'";
				$result=query($sql);
				$_SESSION["driver"]=$result[0]["Driver"];
				
				if ($_SESSION["driver"]=="1"){
					$_SESSION['state']="dmain";
				}else{
					$_SESSION['state']="main";
				}
				$_SESSION['id']=$username;
				$_SESSION['pid']=$result[0]["IDNumber"];
				$_SESSION['name']=$result[0]["Name"];
				header("location:index.php");
			}
		}
	}else{
		$error=true;
	}
	if ($error==true){
		//Make them a pretty message
		notify("Incorrect username or password!","OK");
	}
	dbClose();
}


?>

<h1 id="loginMessage"><?php
	$messages=array("Welcome!","Welcome Back!","Need A Lift?","Sign Back In","Going Somewhere?");
	echo $messages[array_rand($messages)];
	
?></h1>
<form method="POST" action="index.php">
<br>
<input type="text" name="username" placeholder="*Student ID#" required autocomplete="off">
<br>
<br>
<input type="password" name="password" placeholder="*Password" required autocomplete="off">
<br>
<br>
<input type="hidden" name="type" value="login">
<input type="submit" value="Login">
</form>

<div style="visibility:hidden;">
<form id="forgotForm" method="POST" action="index.php">
<input type="hidden" name="type" value="forgot">
</form>
</div>


<a style="color:#fff;" onclick="forgot()">Forgot Password?</a>
<script>
function forgot(){
	document.getElementById("forgotForm").submit();
}
</script>
<?php

?>