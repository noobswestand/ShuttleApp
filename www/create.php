<?php
include_once "db.php";
$day=$_SESSION["day"];
$month=$_SESSION["month"];
$year=$_SESSION["year"];


$dateObj   = DateTime::createFromFormat('!m', $month);
$monthName = $dateObj->format('F');
?>
<link rel="stylesheet" href="css/create.css">
<?php

//See if there are any rides for that day
dbConnect();
$sql="SELECT * FROM request
WHERE DAY(TimePickUp)=$day
AND MONTH(TimePickUp)=$month
AND YEAR(TimePickUp)=$year";
$result=query($sql);
dbClose();

if (!empty($result) && !isset($_SESSION["createSeeCreate"])){
	$_SESSION["createSee"]=true;
	include_once "see3.php";
}else{
	?>
	<h1><?php echo $monthName."  ".$day;?></h1>
	<?php
	//Check posts
	if (isset($_POST["placeFrom"]) && isset($_POST["placeTo"]) && 
	isset($_POST["placeToTime"]) && isset($_POST["rideBackTime"])){
		//Select times
		$day=$_SESSION["day"];
		$month=$_SESSION["month"];
		$year=$_SESSION["year"];
		$placeTo=$_POST["placeTo"];
		$placeFrom=$_POST["placeFrom"];
		
		
		//Check for other
		if ($placeTo=="Other" && isset($_POST["placeToOther"])){
			$placeTo=$_POST["placeToOther"];
		}
		if ($placeFrom=="Other" && isset($_POST["placeFromOther"])){
			$placeFrom=$_POST["placeFromOther"];
		}
		

		$id=$_SESSION["id"];
		$nextDay=0;
		if (isset($_POST["nextDay"])){
			$nextDay=$_POST["nextDay"];
		}

		dbConnect();
		$time0=clean($_POST["placeToTime"]);
		$time1="";
		$failed=false;
		$failed2=false;
		$failed3=false;
		if (isset($_POST["rideBackTime"]) && !empty($_POST["rideBackTime"])){
			$time1=clean($_POST["rideBackTime"]);
			//check time settings
			$time3=strtotime($time0);
			$time3Hour=date('H', $time3);
			$time3Minute=date('i', $time3);

			$time4=strtotime($time1);
			$time4Hour=date('H', $time4);
			$time4Minute=date('i', $time4);
			if ((($time3Hour>$time4Hour) ||
			($time3Minute>=$time4Minute && $time3Hour==$time4Hour))
			&& $nextDay=="0"){
				$failed3=true;
				notify("<br><br>Invalid time settings!","Back","msgReturn()");
				unset($_SESSION["createSeeCreate"]);
			}
			
		}

		$time0=$year."-".$month."-".$day." ".$time0.":00";
		$time0=strtotime($time0);
		$time0=date('Y-m-d H:i:s',$time0);

		if ($failed3==false){
			//$sql="INSERT INTO request(RequestID,StudentID,LocationTo,LocationFrom,TimePickUp,DriverID)
			//VALUES (null,$id,'$placeTo','$placeFrom','$time0',null)";
			
			$sql="INSERT INTO request(RequestID,LocationTo,LocationFrom,TimePickUp,DriverID)
			VALUES (null,'$placeTo','$placeFrom','$time0',null);";
			$result=insert($sql);
			$sql="INSERT INTO requeststudent(requestID,studentID) VALUES ($result,$id);";
			$result=insert($sql);
			
			/*
			$failed=!$result;
			if ($failed){
				notify("An error has occurred","Back","msgReturn()");
				unset($_SESSION["createSeeCreate"]);
			}
			*/
			$failed2=false;
			
			//Ride back
			if (!$failed && !empty($_POST["rideBackTime"])){
				$day=(string)(((int)$day) + $nextDay);
				$time1=$year."-".$month."-".$day." ".$time1.":00";
				$sql="INSERT INTO request(RequestID,LocationTo,LocationFrom,TimePickUp,DriverID)
				VALUES (null,'$placeFrom','$placeTo','$time1',null)";
				$result=insert($sql);
				$sql="INSERT INTO requeststudent(requestID,studentID) VALUES ($result,$id);";
				$result=insert($sql);
				/*
				$failed2=!$result;
				if ($failed2){
					notify("<br><br>An error has occurred","Back","msgReturn()");
					unset($_SESSION["createSeeCreate"]);
				}
				*/
			}
		}
		dbClose();
		if (!$failed && !$failed2 && !$failed3){
			$_SESSION["state"]="newsucc";
			unset($_SESSION["createSeeCreate"]);
			header("location:index.php");
		}
	}else{
		
	//Create array of places you can go
	//$places=Array("Lakeland Unversity","North Side Sheboygan Walmart","Blue Harbor","Osthoff Resort","Other");
	dbConnect();
	$sql="SELECT Name FROM place;";
	$names=query($sql);
	dbClose();
	$places=[];
	foreach ($names as $n){
		array_push($places,$n["Name"]);
	}
	array_push($places,"Other");
	
	?>
	<style>
	#registerPasswordMatch{
		background-color:#435159;
		text-align:center;
		position: relative;
		left: -50%;
		width:450px;
		height:300px;
	}
	
	#registerPasswordMatch1{
		position:absolute;
		left: 50%;
		z-index:999999;
		margin-top:-20px;
	}
	</style>
	
	<div id="registerPasswordMatch1">
    <div id="registerPasswordMatch">
      <h1 id="loginMessage"><br><br>You cannot have the same<br>start and end location!</h1>
	  <button id="submitButton" onclick="clear_warning()">OK</button>
    </div>
  </div>
	
	
	<form method="POST" action="index.php" id="createForm">
	
	
	
			<p style="margin-bottom:0px;">Where From?</p>
			<select style="margin:auto;" id="placeFrom" name="placeFrom" onchange="checkOther()">
			<?php
			foreach ($places as $p){
				echo "<option value='$p'>$p</option>";
			}
			?>
			<br>
			<input id="placeFromOther" type="text" name="placeFromOther" placeholder="*Location">
			</select>
			<br><br>
			<p style="margin-bottom:0px;">At what time?</p>
			<input id="TimePickUp" type="time" name="placeToTime" required />
		
			<br><br>
			<p style="margin-bottom:0px;">Where To?</p>
			<select id="placeTo" name="placeTo" onchange="checkOtherTo()">
			<?php
			foreach ($places as $p){
				echo "<option value='$p'>$p</option>";
			}
			?>
			<input id="placeToOther" type="text" name="placeToOther" placeholder="*Location">
			</select>
			
			<br><br>
			<div id="placeRideBack">
			<p style="margin-bottom:0px;">Need a ride back to<br>Lakeland Unversity?</p>
			</div>
			<div id="placeRideBack2">
			<button type=button id="submitButton" onclick="rideBack()">Yes</button>
			</div>
			
			<div id="placeRideBack3">
			<p style="margin-bottom:0px;">Time for ride back<br>(<u><a id="acancel" onclick="cancel()">Cancel</a></u>)</p>
			<input id="TimePickUp2" type="time" name="rideBackTime"/>
			</div>
			<div id="placeRideBack4">
			<input type="checkbox" name="nextDay" value="1"><p style="display:inline-block;margin:0px;">Next Day?</p>
			</div>
			
			
	<br><br>
	<button type=button id="submitButton" onclick="schedule()">Schedule</button>
	</form>
	
	<!-- Modal -->
	<div class="modal" id="myModal" role="dialog">
		<div class="modal-dialog modal-sm  modal-dialog-centered">
			<!-- Modal content-->
			<div class="modal-content" style="box-shadow:none;border-radius:0px; background-color:#435159;">
				<div class="modal-body">
					<h1>You cannot have the same start and end location</h1>
					<button id="submitButton" type="button" class="btn btn-default center-block" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
	
	
	<script>
		setTimeout(function() {
			$("#placeFromOther").hide();
			$("#placeToOther").hide();
			$("#placeRideBack3").hide();
			$("#placeRideBack4").hide();
			$("#registerPasswordMatch1").hide();
		}, 25);
		
		function cancel(){
			$("#placeRideBack").show();
			$("#placeRideBack2").show();
			$("#placeRideBack3").hide();
			$("#placeRideBack4").hide();
			document.getElementById("TimePickUp2").value="";
			
		}
		
		function checkOtherTo(){
			var e = document.getElementById("placeTo");
			var strUser = e.options[e.selectedIndex].value;
			if (strUser=="Other"){
				$("#placeToOther").show();
				document.getElementById("placeToOther").required=true;
			}else{
				$("#placeToOther").hide();
				document.getElementById("placeToOther").required=false;
			}
		}
		
		var ride=false;
		function checkOther(){
			var e = document.getElementById("placeFrom");
			var strUser = e.options[e.selectedIndex].value;
			if (strUser=="Other"){
				$("#placeFromOther").show();
				document.getElementById("placeFromOther").required=true;
			}else{
				$("#placeFromOther").hide();
				$("#placeRideBack3").hide();
				$("#placeRideBack4").hide();
				document.getElementById("placeFromOther").required=false;
			}
			//Check if they need a ride back
			if (strUser=="Lakeland Unversity" && ride==false){
				$("#placeRideBack").show();
				$("#placeRideBack2").show();
			}else{
				$("#placeRideBack").hide();
				$("#placeRideBack2").hide();
			}
			
		}
		function rideBack(){
			//ride=true;
			$("#placeRideBack").hide();
			$("#placeRideBack2").hide();
			$("#placeRideBack3").show();
			$("#placeRideBack4").show();
		}
		function schedule(){
			if (document.getElementById("placeFrom").value==document.getElementById("placeTo").value
			&& document.getElementById("placeTo").value!="Other"){
				//$("#registerPasswordMatch1").show();
				//$("#registerPasswordMatch").show();
				$('#myModal').modal('toggle');
				
			}else{
				var inpObj = document.getElementById("createForm");
				if (inpObj.checkValidity()) {
					inpObj.submit();
				}
			}
		}
		function clear_warning(){
			$("#registerPasswordMatch1").hide();
		}
		
	</script>
	
	<?php
	}
}
?>