<link rel="stylesheet" href="css/new.css">
<link rel="stylesheet" href="css/create.css">
<?php
include_once "db.php";
$id=$_POST["id"];

$seeState=-1;
$seeDate=[];

if (isset($_POST["id"]) && $_POST["id"]!=""){
	
	//see if it is a valid user
	dbConnect();
	$pid=clean($_POST["id"]);
	$sql="SELECT * FROM request WHERE RequestID=$pid";
	$result=query($sql);
	dbClose();
	if (!empty($result)){
		$seeDate=$result[0];
		
		if (!isset($_POST["edit"])){
			$seeState=0;
		}else{
			if (!isset($_POST["do"])){
				$seeState=1;
			}else{
				//Link the stuff!
				dbConnect();
				$id=$_SESSION["pid"];
				$car=clean($_POST["car"]);
				$time=(int)clean($_POST["time"]);
				$otime=strtotime($seeDate["TimePickUp"]);
				$time2=$otime+($time*60);
				$time3=date('Y-m-d H:i:s',$time2);
				$sql="UPDATE request SET DriverID=$id, CarID=$car, TimePickUpNew='$time3'
				WHERE RequestID=$pid;";
				insert($sql);
				dbClose();
				notify("Successfully picked up the ride!","OK","msgReturn()");
			}
		}
	}else{
		notify("An error has occured!","Back","msgReturn()");
	}
}else{
	notify("An error occured","msgReturn()");
}


//scheduling the ride
if ($seeState==1){
	
	function createDay($t,$id="requestDay"){
		?>
		<th>
		<div id="<?php echo $id;?>" class="<?php echo $t;?>" onclick="selectDay('<?php echo $t;?>',this)">
			<p style="margin-top:13px;"><?php echo $t;?></p>
		</div>
		</th>
		<?php
	}
	
	$to=$seeDate["LocationTo"];
	$from=$seeDate["LocationFrom"];
	$date = strtotime($seeDate["TimePickUp"]);
	$time=date("F",$date)." ".date("j",$date);
	$time2=date("g",$date).":".date("i",$date)." ".date("A",$date);
	$timeDefault=date("H",$date).":".date("i",$date);
	?>
	
	<h1 id="loginMessage"><?php echo $time;?><br><?php echo $time2;?></h1>
	<p><?php echo $from;?>&#8594;<?php echo $to;?></p>
	<hr>
	
	<br>
	<p>Finalize schedule</p>
	<script>
	var global_day = "";
	var global_previousColor=0;
	var global_previousObj=0;
	var global_text=document.getElementById("loginMessage").innerHTML;
	
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
		
		
		//Change title
		if (time!="0"){
			document.getElementById("loginMessage").innerHTML =
				global_text+time;
		}else{
			document.getElementById("loginMessage").innerHTML =global_text;
		}
		global_day=time;
	}
	function doCreate(){
		document.getElementById("time").value = global_day.toString();
		document.getElementById("newForm").submit();
	}
	</script>
	
	<form id="newForm" method="POST" action="index.php">
	<input type="hidden" id="time" name="time" value="-1">
	<input type="hidden" id="id" name="id" value="<?php echo $_POST["id"];?>">
	<input type="hidden" id="do" name="do" value="1">
	<input type="hidden" id="do" name="edit" value="1">
	
	<p style="margin-bottom:0px;">Vehicle</p>
	<select style="margin:auto;" id="car" name="car">
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
	</form>
	
	<br>
	<p style="margin-bottom:0px;">Adjust Time (minutes)</p>
	<?php
	createDay("-30");
	createDay("-15");
	createDay("0");
	createDay("+15");
	createDay("+30");
	?>
	<script>
	$('.0').click();
	</script>
	<br><br>
	<button id="createButton" onclick="doCreate()">Pick up</button>
	
	<?php
	
}

//Just looking at information
if ($seeState==0){
	$to=$seeDate["LocationTo"];
	$from=$seeDate["LocationFrom"];
	$date = strtotime($seeDate["TimePickUp"]);
	$time=date("F",$date)." ".date("j",$date);
	$time2=date("g",$date).":".date("i",$date)." ".date("A",$date);
	
	
	//Get if there is a time conflict
	dbConnect();
	$id=$_SESSION["pid"];
	$sql="SELECT * FROM request WHERE DriverID=$id";
	$result=query($sql);
	$conflict=0;
	foreach ($result as $r){
		$date2=strtotime($r["TimePickUp"]);
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
	
	//Get how many people are riding
	$cap=0;
	$sql="SELECT COUNT(*) FROM requeststudent
	INNER JOIN request ON requeststudent.RequestID=request.RequestID
	WHERE requeststudent.RequestID=$pid";
	$result4=query($sql);
	if (!empty($result4)){
		$cap=$result4[0]["COUNT(*)"];
	}
	dbClose();
	
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
	<br><br>
	<p style="margin-bottom:0px;">How many people?</p>
	<button id="submitButtonNull" style="height:auto;"><?php echo $cap?></button>
	
	
	
	<br><br><br><br>
	<button style="float:top" id="submitButton" onclick="edit()">Schedule</button>
	
	
	<div style="visibility:hidden;">
	<form id="seeForm" method="POST" action="index.php">
	<input type="hidden" name="id" id="id" value="-1">
	<input type="hidden" name="edit" id="edit" value="1">
	<input type="hidden" name="type" value="dseeIndv">
	</form>
	</div>
	<script>
	function edit(){
		document.getElementById("id").value=<?php echo $_POST["id"]?>;
		document.getElementById("seeForm").submit();
	}
	</script>
	

<?php
}
?>


