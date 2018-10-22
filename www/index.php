<?php
session_set_cookie_params(604800,"/");
session_start();
date_default_timezone_set("America/Chicago");

include_once "db.php";
include_once "export.php";


//switch the session vars based on post
if (isset($_POST["type"])){
	switch($_POST["type"]){
		
		/* STUDENT */
		case "main":
			$_SESSION['state']="main";
			//Unset a bunch of variables
			unset($_SESSION["createSeeCreate"]);
			
			unset($_SESSION["day"]);//create/new
			unset($_SESSION["month"]);
			unset($_SESSION["year"]);
			unset($_SESSION["type2"]);
			unset($_SESSION["ride_pid"]);
			
		break;
		case "seeStart":
			$_SESSION["state"]="see";
		break;
		case "seeIndv"://self
			$_SESSION["state"]="seeIndv";
		break;
		case "seeIndv2"://join
			$_SESSION["state"]="seeIndv2";
		break;
		case "newStart":
			$_SESSION["state"]="new";
		break;
		case "create":
			//null
		break;
		
		
		
		/*  Driver  */
		case "dmain":
			$_SESSION['state']="dmain";
		break;
		case "dsee":
			$_SESSION['state']="dsee";
		break;
		case "dseeIndv":
			$_SESSION['state']="dseeIndv";
		break;
		
		case "dunassigned":
			$_SESSION['state']="dunassigned";
		break;
		case "dhistory":
			$_SESSION["state"]="dhistory";
		break;
		case "dexport":
			if ($_SESSION['driver']=="1" && isset($_SESSION['pid'])){
				export();
				die();
			}
		break;
		
		
		
		/*  Admin  */
		
		
		
		/*  ALL  */
		case "login":
			$_SESSION["state"]="login";
		break;
		case "forgot":
			$_SESSION["state"]="forgot";
		break;
		case "settings":
			$_SESSION["state"]="settings";
		break;
	}
}
include_once "html.php";

if (isset($_GET["l"])){
	$_SESSION["state"]="forgot";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no">
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/style.css">
	<link rel="stylesheet" href="css/header.css">
    <title>Shuttle Application</title>
	
	<script src="js/jquery-3.3.1.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	
    <link rel="shortcut icon" type="image/png" href="images/favicon.png"/>
	<script src="js/script.js"></script>
	
	<!--<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
	-->
	
</head>

<body>
	
	<header class="headerbgimage">
		<div class="container" class="hover">
			<a onclick="msgReturn()">
			<h1 class="headerTitle1"><i>shuttle</i>.</h1>
			<h1 class="headerTitle2"><b>LAKELAND</b></h1>
			</a>
		</div>
	</header>
	<div class="coverup"></div>
	
	<?php
	$back=false;
	if (isset($_SESSION["state"])){
	if ($_SESSION["state"]!="main" &&
		$_SESSION["state"]!="dmain" && $_SESSION["state"]!="login"){
		$back=true;
		?>
		<div style="margin-top:-60px;">
			<button id="backButton" onclick="back()"><i class="material-icons">arrow_back</i></button>
			<div style="visibility:hidden;">
			<form id="backForm" method="POST" action="index.php">
			<?php
			if ($_SESSION["state"]!="forgot"){
				if (isset($_SESSION["driver"]) && $_SESSION["driver"]=="1"){
					echo '<input type="hidden" name="type" value="dmain">';
				}else{
					echo '<input type="hidden" name="type" value="main">';
				}
			}else{
				echo '<input type="hidden" name="type" value="login">';
			}
			?>
			</form>
			</div>
			<script>
			function back(){
				document.getElementById("backForm").submit();
			}
			</script>
		</div>
		<?php
	}
	
	}
	?>
	
	<div class="vertical-center" style="margin-top:<?php if ($back==true){echo "0px";}else{echo "0px";}?>;">
		<div class="container text-center">
		<div class="row">
		<div class="col-12" style="text-align:center;">
			
			<?php
			$showLogin=false;
			if (isset($_SESSION['state'])){
				
				//Just in case
				if (!isset($_SESSION['id']) && $_SESSION['state']!="forgot"){
					$_SESSION['state']="login";
				}
				switch($_SESSION['state']){
					
					/* Student */
					case "main":
						include_once "main.php";
					break;
					case "new":
						include_once "new.php";
					break;
					case "see":
						include_once "see.php";
					break;
					case "seeIndv":
						include_once "seeIndv.php";
					break;
					case "seeIndv2":
						include_once "seeIndv2.php";
					break;
					case "newsucc":
						include_once "success.php";
					break;
					
					
					
					/* Driver */
					case "dmain":
						include_once "dmain.php";
					break;
					case "dsee":
						include_once "dsee.php";
					break;
					case "dseeIndv":
						include_once "dseeIndv.php";
					break;
					case "dunassigned":
						include_once "dunassigned.php";
					break;
					case "dhistory":
						include_once "dhistory.php";
					break;
					
					
					/* ALL */
					case "forgot":
						include_once "forgot.php";
					break;
					case "login":
						$showLogin=true;
					break;
					case "settings":
						include_once "settings.php";
					break;
					
					
					
					default:
						$showLogin=true;
					break;
				}
			}else{
				$showLogin=true;
			}
			
			if ($showLogin==true){ ?>
				<div id="login"><p>Log In</p></div>
				<div id="register"><p>Sign Up</p></div>
				<div id="bodyLogin">
					<div style="height:55px;"></div>
					<?php include_once "login.php"; ?>
				</div>
				<div id="bodyRegister">
					<div style="height:55px;"></div>
					<?php include_once "register.php"; ?>
				</div>
				<?php
			}?>
			
			
		</div>
		</div>
		</div>
	</div>


	


</body>
</html>