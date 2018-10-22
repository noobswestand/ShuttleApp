<?php
//Looking at + editing individual rides

$seeState=-1;
$seeDate=[];
$pid=-1;

if (isset($_POST["id"]) && $_POST["id"]!=""){
	//see if it is a valid request
	dbConnect();
	$pid=clean($_POST["id"]);
	$sql="SELECT * FROM request WHERE RequestID=$pid";
	$result=query($sql);
	dbClose();
	if (!empty($result)){
		$seeDate=$result[0];
		switch($_POST["step"]){
			case "2":
				$seeState=1;
			break;
			case "3":
				if (isset($_POST["car"])){
					$seeState=2;
					//Save the changed car
					dbConnect();
					$carid=clean($_POST["car"]);
					$sql="UPDATE request SET CarID=$carid
					WHERE RequestID=$pid";
					insert($sql);
					dbClose();
				}else{
					notify("An error has occured!","Back","msgReturn()");
					die();
				}
			break;
			
			case "4":
				dbConnect();
				$sql="UPDATE request SET DriverID=NULL,CarID=NULL,TimePickUpNew=NULL
				WHERE RequestID=$pid";
				insert($sql);
				dbClose();
				notify("Dropped the ride","Back","msgReturn()");
			break;
			
			default:
				$seeState=0;
			break;
		}
	}else{
		notify("An error has occured!","Back","msgReturn()");
	}
}

//Change car
if ($seeState==2){
	notify("Successfully changed the car!","Back","return2()");
	?>
	<div style="visibility:hidden;">
	<form id="editForm" method="POST" action="index.php">
	<input type="hidden" name="id" id="id" value="<?php echo $pid;?>">
	<input type="hidden" name="step" id="step" value="2">
	</form>
	</div>
	<script>
	function return2(){
		document.getElementById("editForm").submit();
	}
	</script>
	<?php
}



//Editing
if ($seeState==1){
	$to=$seeDate["LocationTo"];
	$from=$seeDate["LocationFrom"];
	$vid=$seeDate["CarID"];
	$pid=$seeDate["RequestID"];
	$date = strtotime($seeDate["TimePickUpNew"]);
	$time=date("F",$date)." ".date("j",$date);
	$time2=date("g",$date).":".date("i",$date)." ".date("A",$date);
	
	//Get riders
	dbConnect();
	$sql="SELECT * FROM student
	INNER JOIN requeststudent ON student.StudentID=requeststudent.StudentID
	WHERE requeststudent.RequestID=$pid;";
	$result=query($sql);
	
	
	?>
	<link rel="stylesheet" href="css/create.css">
	<h2><?php echo $time;?><br><?php echo $time2;?></h2>
	<p><?php echo $from;?><br><?php echo $to;?></p>
	
	
	<button href="#demo" id="submitButton2" onclick="changeText()" data-toggle="collapse">Show Riders</button>
	<div id="demo" class="collapse">
		<br>
		<?php
		foreach ($result as $r){
			$name=$r["Name"];
			$id=$r["StudentID"];
			$phone=$r["Phone"];
			$sql="SELECT COUNT(*) FROM requeststudent WHERE StudentID=$id";
			$sql2="SELECT COUNT(*) FROM requeststudent WHERE StudentID=$id
			AND Went<>0";
			$result=query($sql)[0];
			$result2=query($sql2)[0];
			
			$per=round(100*(((int)$result2["COUNT(*)"])/((int)$result["COUNT(*)"])));
			?>
			
			<button href="#demo<?php echo $id;?>" id="submitButton" style="height:auto;" data-toggle="collapse"><?php echo $name;?></button>
			<div id="demo<?php echo $id;?>" class="collapse">
				<button id="submitButtonNull">Phone: <?php echo $phone;?></button>
				<button id="submitButtonNull">Rides attended: <?php echo $per;?>%</button>
			</div>
			
			
			<br>
			<?php
		}
		?>
	</div>
	<br><br>
	
	<form id="newForm" method="POST" action="index.php">
	<input type="hidden" id="id" name="id" value="<?php echo $_POST["id"];?>">
	<input type="hidden" id="do" name="step" value="3">
	
	<p style="margin-bottom:0px;">Vehicle</p>
	<select style="margin:auto;" id="car" name="car">
	<?php
	$cap=0;
	$sql="SELECT COUNT(*) FROM requeststudent
	INNER JOIN request ON requeststudent.RequestID=request.RequestID
	WHERE requeststudent.RequestID=$pid";
	$result4=query($sql);
	if (!empty($result4)){
		$cap=$result4[0]["COUNT(*)"];
	}
	
	
	$sql="SELECT * FROM car;";
	$places=query($sql);
	foreach ($places as $p){
		$r=$p["CarID"];
		$c=$p["Seats"];
		$l=$p["License"]." ($c seats)";
		$s="";
		if ($r==$vid){
			$s='selected="selected"';
		}
		$d="";
		if ($cap>$c){
			$d="disabled";
		}
		echo "<option value='$r' $s $d>$l</option>";
	}
	$sql="SELECT Seats FROM car WHERE CarID=$vid";
	$vida=query($sql)[0]["Seats"];
	
	?>
	</select>
	<?php
	foreach ($places as $p){
		$r=$p["CarID"];
		$c=$p["Seats"];
		$l=$p["License"]." ($c seats)";
		echo "<p id='span_id_$l' style='display:none;'>$c</p>";
	}
	?>
	</form>
	<button onclick="change()" id="submitButton2">Change Vehicle</button>
	
	<script>
	
	var selectedOption; 
	$('#car').on('change', function() {
		selectedOption = $( "#car option:selected" ).text();
		var text=document.getElementById('span_id_'+selectedOption).innerHTML;
		global_car_amount2=parseInt(text);
	});
	
	global_car_amount=<?php echo $vida;?>;
	global_car_amount2=<?php echo $vida;?>;
	global_people_amount=<?php echo $cap;?>;
	
	function change(){
		if (global_car_amount2<global_people_amount){
			//Notify here
			
		}else{
			//Save changes
			document.getElementById("newForm").submit();
		}
	}
	
	
	function changeText(){
		if (document.getElementById("submitButton2").innerHTML=="Show Riders"){
			document.getElementById("submitButton2").innerHTML="Hide Riders";
		}else{
			document.getElementById("submitButton2").innerHTML="Show Riders";
		}
	}
	</script>
	
	<br><br><br>
	<button id="submitButtonYellow">Drop</button>
	
	<div class="modal" id="notifyModal" role="dialog" data-keyboard="false" data-backdrop="static">
		<div class="modal-dialog modal-sm  modal-dialog-centered">
		<div class="modal-content" style="box-shadow:none;border-radius:0px; background-color:#435159;">
			<div class="modal-body">
				<h1 class="modal-title" id="myModalLabel">Drop the ride from your schedule?(The students will be notified)</h1>
				<button type="button" class="btn btn-default center-block submitButtonYellow" id="modal-btn-si">Drop</button>
				<h1></h1>
				<button type="button" class="btn btn-default center-block submitButton" id="modal-btn-no">Never Mind</button>
			</div>
		</div>
	</div>
	</div>
	
	<form id="deleteForm" method="POST" action="index.php">
	<input type="hidden" id="id" name="id" value="<?php echo $_POST["id"];?>">
	<input type="hidden" id="do" name="step" value="4">
	</form>
	<script>
	var modalConfirm = function(callback){
	  
	  $("#submitButtonYellow").on("click", function(){
		$("#notifyModal").modal('show');
	  });

	  $("#modal-btn-si").on("click", function(){
		callback(true);
		$("#notifyModal").modal('hide');
	  });
	  
	  $("#modal-btn-no").on("click", function(){
		callback(false);
		$("#notifyModal").modal('hide');
	  });
	};

	modalConfirm(function(confirm){
	  if(confirm){
		//drop!
		document.getElementById("deleteForm").submit();
	  }else{
		//clicked no
	  }
	});
	</script>
	<?php
	
	dbClose();
}

//Just looking
if ($seeState==0){
	$to=$seeDate["LocationTo"];
	$from=$seeDate["LocationFrom"];
	$vid=$seeDate["CarID"];
	$pid=$seeDate["RequestID"];
	
	//get the car license + capacity
	dbConnect();
	$sql="SELECT License,Seats FROM car
	INNER JOIN request ON request.CarID=car.CarID
	WHERE car.CarID=$vid";
	$r=query($sql);
	$v=$r[0]["License"];
	$c=$r[0]["Seats"];
	
	//Get how many people are currently riding that ride
	$sql="SELECT COUNT(*) FROM requeststudent
	INNER JOIN request ON requeststudent.RequestID=request.RequestID
	WHERE requeststudent.RequestID=$pid";
	$result4=query($sql);
	if (!empty($result4)){
		$cap=$result4[0]["COUNT(*)"]."/".$c;
	}
	
	$date = strtotime($seeDate["TimePickUpNew"]);
	$time=date("F",$date)." ".date("j",$date);
	$time2=date("g",$date).":".date("i",$date)." ".date("A",$date);
	$time3=date("Y",$date);
	//Get if there is a time conflict
	$id=$_SESSION["pid"];
	$sql="SELECT * FROM request WHERE DriverID=$id AND RequestID<>$pid";
	$result=query($sql);
	$conflict=0;
	foreach ($result as $r){
		$date2=strtotime($r["TimePickUpNew"]);
		if (abs($date-$date2)<1800){//30 minute warning
			if ($conflict==0 || $conflict==3){
				$conflict=2;
			}
		}
		if (abs($date-$date2)<900){//15 minute crit
			$conflict=1;
		}
		if (abs($date-$date2)<3600){//60 minute info
			if ($conflict==0){
				$conflict=3;
			}
		}
	}
	
	$img="";
	$text="";
	if ($conflict==0){//accepted by driver
		$img='<button type="button" class="btn btn-success btn-circle"><i class="material-icons" style="margin-top:-3px;">done</i></button>';
		$text="No time conflicts";
	}
	if ($conflict==1){//crit
		$img='<button type="button" class="btn btn-danger btn-circle"><i class="material-icons" style="margin-top:-4px;">access_time</i></button>';
		$text="You have a ride scheduled within 15 minutes of this time";
	}
	if ($conflict==2){//warning
		$img='<button type="button" class="btn btn-warning btn-circle"><i class="material-icons" style="margin-top:-4px;">av_timer</i></button>';
		$text="You have a ride scheduled within 30 minutes of this time";
	}
	if ($conflict==3){//info
		$img='<button type="button" class="btn btn-info btn-circle"><i class="material-icons" style="margin-top:-4px;">timer</i></button>';
		$text="You have a ride scheduled within 60 minutes of this time";
	}
	//Find the distance between the two spots
	$distToggle=false;
	$dist=0;
	$sql="SELECT * FROM place WHERE Name='$from'";
	$sql2="SELECT * FROM place WHERE Name='$to'";
	$result=query($sql);
	$result2=query($sql2);
	if (!empty($result) && !empty($result2)){
		$distToggle=true;
		
		$dist=vincentyGreatCircleDistance($result[0]["Lat"],$result[0]["Lon"],
			$result2[0]["Lat"],$result2[0]["Lon"]);
		
	}
	dbClose();
	
	
	if (!isset($_POST["history"])){
		?>
		<h1 id="loginMessage"><?php echo $time;?><br><?php echo $time2;?></h1>
		<ul class='list-group align-items-center'>
			<li class="list-group-item" style="text-align:left;">
				<?php echo $img;?>
				<?php echo $text;?>
			</li>
		</ul>
		<?php
	}else{
		?>
		<h1 id="loginMessage"><?php echo $time."-".$time3;?><br><?php echo $time2;?></h1>
		<?php
	}
	?>
	<br>
	<p style="margin-bottom:0px;">Where from?</p>
	<button id="submitButtonNull" style="height:auto;"><?php echo $from;?></button>
	<br><br>
	<p style="margin-bottom:0px;">Where to?</p>
	<button id="submitButtonNull" style="height:auto;"><?php echo $to;?></button>
	<br><br>
	<p style="margin-bottom:0px;">What vehicle?</p>
	<button id="submitButtonNull" style="height:auto;"><?php echo $v;?></button>
	<br><br>
	<p style="margin-bottom:0px;">Capacity</p>
	<button id="submitButtonNull" style="height:auto;"><?php echo $cap;?></button>
	
	
	<?php
	if ($distToggle==true){
		?>
		<br><br>
		<p style="margin-bottom:0px;">Distance</p>
		<button id="submitButtonNull" style="height:auto;">~<?php echo $dist;?> miles</button>
		<?php
	}
	?>
	
	
	<br><br><br>
	<?php
	if (!isset($_POST["history"])){
		?>
		<button id="submitButton" onclick="editForm()" style="height:auto;">Edit</button>
		<br><br>
		
		<div style="visibility:hidden;">
		<form id="editForm" method="POST" action="index.php">
		<input type="hidden" name="id" id="id" value="<?php echo $pid;?>">
		<input type="hidden" name="step" id="step" value="2">
		</form>
		</div>
		<script>
		function editForm(id){
			document.getElementById("editForm").submit();
		}
		</script>
		<?php
	}
}


?>