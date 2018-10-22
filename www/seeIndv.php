<?php
include_once "db.php";

$seeState=-1;
$seeDate=[];

//get the id + redirect
if (isset($_POST["id"])){
	dbConnect();
	$pid=clean($_POST["id"]);
	$_SESSION["ride_pid"]=$pid;
	dbClose();
	header("location:index.php");
}

if (isset($_SESSION["ride_pid"]) && isset($_POST["drop"])
&& $_SESSION["ride_pid"]!=""){
	$id=$_SESSION["id"];
	$pid=$_SESSION["ride_pid"];
	//see if it is a valid user
	dbConnect();
	$sql="SELECT * FROM request
	INNER JOIN requeststudent ON request.RequestID=requeststudent.RequestID
	WHERE requeststudent.StudentID=$id  AND request.RequestID=$pid;";
	$result=query($sql);
	dbClose();
	if (!empty($result)){
		dbConnect();
		$sql="DELETE FROM requeststudent
		WHERE RequestID=$pid AND StudentID=$id;";
		insert($sql);
		dbClose();
		notify("Successfully dropped out of the ride","Back","msgReturn()");
		die();
	}else{
		notify("An error occured","Back","msgReturn()");
		die();
	}
}


if (isset($_SESSION["ride_pid"]) && isset($_POST["edit"])
&& $_SESSION["ride_pid"]!=""){
	$id=$_SESSION["id"];
	$pid=$_SESSION["ride_pid"];
	//see if it is a valid user
	dbConnect();
	$sql="SELECT * FROM request
	INNER JOIN requeststudent ON request.RequestID=requeststudent.RequestID
	WHERE requeststudent.StudentID=$id  AND request.RequestID=$pid;";
	$result=query($sql);
	
	dbClose();
	if (!empty($result)){
		$seeDate=$result[0];
		$seeState=1;
	}
}

if (isset($_SESSION["ride_pid"]) && $_SESSION["ride_pid"]!="" && $seeState==-1){
	$id=$_SESSION["id"];
	$pid=$_SESSION["ride_pid"];
	//see if it is a valid user
	dbConnect();
	//$sql="SELECT * FROM request WHERE StudentID=$id AND RequestID=$pid";
	$sql="SELECT * FROM request
	INNER JOIN requeststudent ON request.RequestID=requeststudent.RequestID
	WHERE requeststudent.StudentID=$id  AND request.RequestID=$pid;";
		
	$result=query($sql);
	dbClose();
	if (!empty($result)){
		$seeDate=$result[0];
		$seeState=0;
	}else{
		notify("An error has occured!","Back","msgReturn()");
	}
}

if ($seeState==-1){
	notify("An error occured","Back","msgReturn()");
}



//Editing (delete)
if ($seeState==1){
	?>
	<p style="margin-bottom:0px;">Drop the ride?</p>
	<button style="float:top" id="submitButtonYellow" onclick="drop()">Drop</button>
	
	<div style="visibility:hidden;">
	<form id="dropForm" method="POST" action="index.php">
	<!--<input type="hidden" name="id" value="<?php echo $_POST["id"];?>">-->
	<input type="hidden" name="drop" value="1">
	</form>
	</div>
	
	<script>
	function drop(){
		document.getElementById("dropForm").submit();
	}
	</script>
	<?php
}


//Just looking at information
if ($seeState==0){
	$to=$seeDate["LocationTo"];
	$from=$seeDate["LocationFrom"];
	$date = strtotime($seeDate["TimePickUp"]);
	$time=date("F",$date)." ".date("j",$date);
	$time2=date("g",$date).":".date("i",$date)." ".date("A",$date);
	
	//Get adjusted time
	if ($seeDate["TimePickUpNew"]!=""){
		$date2 = strtotime($seeDate["TimePickUpNew"]);
		$date2=($date2-$date)/60;
		if ($date2!=0){
			if($date2>0){$time2.=" +".$date2;}
			else{$time2.=$date2;}
		}
	}
	
	$img="";
	$text="";
	if ($seeDate["DriverID"]!=""){//accepted by driver
		$img='<button type="button" class="btn btn-success btn-circle"><i class="material-icons" style="margin-top:-3px;">done</i></button>';
		$text="Your ride has been accepted";
	}else{//null
		$img='<button type="button" class="btn btn-danger btn-circle"><i class="material-icons" style="margin-top:-4px;">access_time</i></button>';
		$text="Your ride needs to be approved";
	}
	?>
	<h1 id="loginMessage"><?php echo $time;?><br><?php echo $time2;?></h1>
	<ul class='list-group align-items-center'>
		<li class="list-group-item" style="text-align:left;">
			<?php echo $img;?>
			<?php echo $text;?>
		</li>
	</ul>
	<br>
	<p style="margin-bottom:0px;">Where from?</p>
	<button id="submitButtonNull" style="height:auto;"><?php echo $from?></button>
	<br><br>
	<p style="margin-bottom:0px;">Where to?</p>
	<button id="submitButtonNull" style="height:auto;"><?php echo $to?></button>
	
	
	<?php
	//Get stats of the ride
	if ($seeDate["DriverID"]!=""){
		$did=$seeDate["DriverID"];
		$cid=$seeDate["CarID"];
		dbConnect();
		$sql="SELECT * FROM student WHERE IDNumber=$did";
		$result2=query($sql);
		if (!empty($result)){
			$driver=$result2[0]["Name"];
			
			?>
			<br><br>
			<div style='height:2px;background-color:#ccc;width:100%;'></div>
			<p style="margin-bottom:0px;">Driver</p>
			<button id="submitButtonNull" style="height:auto;"><?php echo $driver?></button>
			<br><br>
			<?php
			$sql="SELECT * FROM car
			INNER JOIN request ON car.CarID=request.CarID
			WHERE car.CarID=$cid";
			$result3=query($sql);
			$sql="SELECT COUNT(*) FROM requeststudent
			INNER JOIN request ON requeststudent.RequestID=request.RequestID
			WHERE requeststudent.RequestID=$pid";
			$result4=query($sql);
			if (!empty($result3) && !empty($result4)){
				$car=$result3[0]["License"];
				$cap=$result4[0]["COUNT(*)"]."/".$result3[0]["Seats"];
				
				?>
				
				<p style="margin-bottom:0px;">Car</p>
				<button id="submitButtonNull" style="height:auto;"><?php echo $car?></button>
				<br><br>
				<p style="margin-bottom:0px;">Capacity</p>
				<button id="submitButtonNull" style="height:auto;"><?php echo $cap?></button>
				
				<?php
			}
		}
		
		dbClose();
	}
	?>
	
	<br><br><br>
	<button style="float:top" id="submitButton" onclick="edit()">Edit</button>
	
	<div style="visibility:hidden;">
	<form id="seeForm" method="POST" action="index.php">
	<!--<input type="hidden" name="id" value="<?php echo $_POST["id"];?>">-->
	<input type="hidden" name="edit" value="1">
	</form>
	</div>
	
	<script>
	function edit(){
		document.getElementById("seeForm").submit();
	}
	</script>
	
	<?php
}
?>