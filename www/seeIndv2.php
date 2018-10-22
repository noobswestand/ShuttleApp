<?php
include_once "db.php";

$seeState=-1;
$seeDate=[];

if (isset($_POST["id"]) && isset($_POST["join"]) && $_POST["id"]!=""){
	$error=false;
	$id=$_SESSION["id"];
	$pid=$_POST["id"];
	dbConnect();
	
	//see if request exists
	$sql="SELECT * FROM request WHERE RequestID=$pid";
	$result=query($sql)[0];
	if (!empty($result)){
		
		
		//see if under capacity
		if ($result["DriverID"]!=""){
			$cid=$result["CarID"];
			$sql="SELECT * FROM car
			INNER JOIN request ON car.CarID=request.CarID
			WHERE car.CarID=$cid";
			$result3=query($sql);
			$sql="SELECT COUNT(*) FROM requeststudent
			INNER JOIN request ON requeststudent.RequestID=request.RequestID
			WHERE requeststudent.RequestID=$pid";
			$result4=query($sql);
			if ((int)$result4[0]["COUNT(*)"]>=(int)$result3[0]["Seats"]){
				notify("This ride is full!","Back","msgReturn()");
				die();
			}
		}else{
			$sql="SELECT COUNT(*) FROM requeststudent
			INNER JOIN request ON requeststudent.RequestID=request.RequestID
			WHERE requeststudent.RequestID=$pid";
			$result4=query($sql);
			$cap=$result4[0]["COUNT(*)"];
			if ($cap>5){
				notify("This ride is full! Check back later","Back","msgReturn()");
				die();
			}
		}
		
		$sql="INSERT INTO requeststudent(RequestID,StudentID) VALUES ($pid,$id);";
		insert($sql);
		
	}else{
		$error=true;
	}
	dbClose();
	if ($error==true){
		notify("An error has occured!","Back","msgReturn()");
		die();
	}else{
		notify("Successfully joined!","Back","msgReturn()");
		die();
	}
}


if (isset($_POST["id"]) && $_POST["id"]!=""){
	$id=$_SESSION["id"];
	$pid=$_POST["id"];
	//see if it is a valid user
	dbConnect();
	//$sql="SELECT * FROM request WHERE StudentID=$id AND RequestID=$pid";
	$sql="SELECT * FROM request
	WHERE RequestID=$pid;";
		
	$result=query($sql);
	dbClose();
	if (!empty($result)){
		$seeDate=$result[0];
		$seeState=0;
	}else{
		notify("An error has occured!","Back","msgReturn()");
	}
}else{
	notify("An error occured","msgReturn()");
}

//Just looking at information
if ($seeState==0){
	$to=$seeDate["LocationTo"];
	$from=$seeDate["LocationFrom"];
	$date = strtotime($seeDate["TimePickUp"]);
	$time=date("F",$date)." ".date("j",$date);
	$time2=date("g",$date).":".date("i",$date)." ".date("A",$date);
	
	$img="";
	$text="";
	if ($seeDate["DriverID"]!=""){//accepted by driver
		$img='<button type="button" class="btn btn-success btn-circle"><i class="material-icons" style="margin-top:-3px;">done</i></button>';
		$text="This ride has been approved";
	}else{//null
		$img='<button type="button" class="btn btn-danger btn-circle"><i class="material-icons" style="margin-top:-4px;">access_time</i></button>';
		$text="This ride needs to be accepted";
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
	$err="";
	if ($seeDate["DriverID"]!=""){
		$did=$seeDate["DriverID"];
		$cid=$seeDate["CarID"];
		dbConnect();
		$sql="SELECT * FROM car
		INNER JOIN request ON car.CarID=request.CarID
		WHERE car.CarID=$cid";
		$result3=query($sql);
		$sql="SELECT COUNT(*) FROM requeststudent
		INNER JOIN request ON requeststudent.RequestID=request.RequestID
		WHERE requeststudent.RequestID=$pid";
		$result4=query($sql);
		dbClose();
		if (!empty($result3) && !empty($result4)){
			$car=$result3[0]["License"];
			$cap=$result4[0]["COUNT(*)"]."/".$result3[0]["Seats"];
			if ((int)$result4[0]["COUNT(*)"]>=(int)$result3[0]["Seats"]){
				$err='<button style="margin-top:-5px;margin-right:8px;width:2em;height:2em;" type="button" class="btn btn-danger btn-circle"><i class="material-icons" style="margin-top:-5px;font-size:1.75em;">clear</i></button>';
			}
			?>
			<br><br>
			<p style="margin-bottom:0px;">Capacity</p>
			<div id="submitButtonNull" style="height:auto;"><?php echo $err;?><?php echo $cap?></div>
			
			<?php
			
		}
	}else{
		dbConnect();
		$sql="SELECT COUNT(*) FROM requeststudent
		INNER JOIN request ON requeststudent.RequestID=request.RequestID
		WHERE requeststudent.RequestID=$pid";
		$result4=query($sql);
		$cap=$result4[0]["COUNT(*)"];
		if ($cap>5){
			$err='<button style="margin-top:-5px;margin-right:8px;width:2em;height:2em;" type="button" class="btn btn-danger btn-circle"><i class="material-icons" style="margin-top:-5px;font-size:1.75em;">clear</i></button>';
		}
		dbClose();
		?>
		<br><br>
		<p style="margin-bottom:0px;">Capacity</p>
		<div id="submitButtonNull" style="height:auto;"><?php echo $err;?><?php echo $cap?>/5</div>
		<?php
	}
	
	if ($err==""){
		?>
		<br><br><br>
		<button style="float:top" id="submitButton" onclick="submit()">Join</button>
		<?php
	}else{
		?>
		<br><br><br>
		<button style="float:top" id="submitButtonNull" onclick="">Join</button>
		<?php
	}
	
	?>
	
	<div style="visibility:hidden;">
	<form id="seeForm" method="POST" action="index.php">
	<input type="hidden" name="id" value="<?php echo $_POST["id"];?>">
	<input type="hidden" name="join" value="1">
	</form>
	</div>
	<script>
	function submit(){
		document.getElementById("seeForm").submit();
	}
	</script>
	
	<?php
}
?>