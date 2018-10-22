

<?php
//For when they are creating a new ride but there is something available
include_once "db.php";
$id=$_SESSION["id"];
$day=$_SESSION["day"];
$month=$_SESSION["month"];
$year=$_SESSION["year"];

if (isset($_POST["createnow"])){
	$_SESSION["createSeeCreate"]=true;
	?>
	<form method="POST" action="index.php" id="createForm2">
	<input type="hidden" name="day" value="<?php echo $day;?>">
	<input type="hidden" name="month" value="<?php echo $month;?>">
	<input type="hidden" name="year" value="<?php echo $year;?>">
	<input type="hidden" name="type" value="create">
	</form>
	<script>
		document.getElementById("createForm2").submit();
	</script>
	<?php
}

dbConnect();

$st=time();
$dt = new DateTime("@$st"); 
$time=date_format($dt,"Y-m-d");
$sql="SELECT DISTINCT * FROM request
INNER JOIN requeststudent ON request.RequestID=requeststudent.RequestID
WHERE requeststudent.StudentID=$id

AND DAY(TimePickUp)=$day
AND MONTH(TimePickUp)=$month
AND YEAR(TimePickUp)=$year

ORDER BY TimePickUp;";
$result3=query($sql);

$myRideAmount=sizeof($result3);

$result=[];
$drawLine=false;
if (!empty($result3)){
	$drawLine=true;
	array_push($result,array("LocationTo"=>"kNxKq5W1UctIthwrpG0H"));
	foreach ($result3 as $r){
		array_push($r,array("type"=>"0"));
		array_push($result,$r);
	}
}

$sql="SELECT DISTINCT * FROM request
INNER JOIN requeststudent ON request.RequestID=requeststudent.RequestID
WHERE requeststudent.StudentID<>$id

AND DAY(TimePickUp)=$day
AND MONTH(TimePickUp)=$month
AND YEAR(TimePickUp)=$year

ORDER BY TimePickUp;";
$test=[];

$result2=query($sql);
$putOtherText=false;
if (!empty($result2)){
	array_push($result,array("LocationTo"=>"kNxKV5W1UctIthwrpG0H"));
	$found=false;
	$putOtherText=true;
	
	$i=[];
	//see if it is your own request and remove it from the other people's ride section
	foreach ($result2 as $r){
		
		//avoid repeats
		$found2=false;
		foreach($i as $_i){
			if ($_i==$r["RequestID"]){
				$found2=true;
				break;
			}
		}
		if ($found2==false){
			array_push($i,$r["RequestID"]);
		}
		
		$found=false;
		foreach($result3 as $rr){
			if ($rr["RequestID"]==$r["RequestID"]){
				$found=true;
				break;
			}
		}
		if ($found==false && $found2==false){
			array_push($r,array("type"=>"1"));
			array_push($result,$r);
			array_push($test,$r);
		}
	}
}


//See if there are any requests without any people on them
$sql="
SELECT            a.*
FROM              request a
NATURAL LEFT JOIN requeststudent b
WHERE             b.RequestID IS NULL

AND DAY(TimePickUp)=$day
AND MONTH(TimePickUp)=$month
AND YEAR(TimePickUp)=$year";
$result4=query($sql);
if (!empty($result4)){
	if ($putOtherText==false){
		array_push($result,array("LocationTo"=>"kNxKV5W1UctIthwrpG0H"));
	}
	foreach ($result4 as $r){
		array_push($r,array("type"=>"1"));
		array_push($result,$r);
		array_push($test,$r);
	}
}

//fail safe to see if no-one else has a ride
if (empty($test) && $myRideAmount==0){
	array_pop($result);
}

dbClose();

if (empty($result)){
	notify("There are no rides scheduled for today!","Back","msgReturn()");
}else{
	array_push($result,array("LocationTo"=>"kNxKq5W1UctIthWqpG0H"));
	//echo "<h1>You have rides available</h1>";
	echo "<ul class='list-group align-items-center'>";
	
	
	foreach ($result as $r){
		$to=$r["LocationTo"];
		
		if ($to=="kNxKV5W1UctIthwrpG0H"){
			if ($drawLine==true){
				echo "<div style='height:2px;background-color:#ccc;width:100%;'></div>";
			}
			echo "<h1>Other people's rides</h1>";
		}else if ($to=="kNxKq5W1UctIthwrpG0H"){
			echo "<h1>Your rides</h1>";
		}else if ($to=="kNxKq5W1UctIthWqpG0H"){
			if (!isset($_POST["createHide"])){
			?>
			<div style='height:2px;background-color:#ccc;width:100%;'></div>
			<h1>Create one anyways?</h1>
			<button type=button id="submitButton" onclick="schedule()">Create</button>
			<form method="POST" action="index.php" id="createForm">
			<input type="hidden" name="day" value="<?php echo $day;?>">
			<input type="hidden" name="month" value="<?php echo $month;?>">
			<input type="hidden" name="year" value="<?php echo $year;?>">
			<input type="hidden" name="createnow" value="1">
			<input type="hidden" name="type" value="create">
			</form>
			<script>
			function schedule(){
				document.getElementById("createForm").submit();
			}
			</script>
			<?php
			}
		}else{
			$from=$r["LocationFrom"];
			$date = strtotime($r["TimePickUp"]);
			$time=date("F",$date)." ".date("j",$date)."  ".date("g",$date).":".date("i",$date)." ".date("A",$date);
			
			$i=$r["RequestID"];
			$t=$r[0]["type"];
			$img="";
			if ($r["DriverID"]!=""){//accepted by driver
				$img='<button type="button" class="btn btn-success btn-circle"><i class="material-icons" style="margin-top:-3px;">done</i></button>';
			}else{//null
				$img='<button type="button" class="btn btn-danger btn-circle"><i class="material-icons" style="margin-top:-4px;">access_time</i></button>';
			}
			
			?>
			<li class="list-group-item" style="text-align:left;" onclick="submitForm(<?php echo $i;?>,<?php echo $t?>)">
				<table style="color:#b7b7b7;font-family:loginThin;">
				<tr>
					<th></th>
					<th><font size="4em" color="#fff"><?php echo $time?></font></th>
					<th></th>
				</tr>
				<tr>
					<th><?php echo $img;?></th>
					<th><?php echo $from?></th>
					<th></th>
				</tr>
				<tr>
					<th></th>
					<th><?php echo $to?></th>
					<th></th>
				</tr>
				
				</table>
			</li>
			<?php
		}
	}
	echo "</ul>";
	?>
	<div style="visibility:hidden;">
	<form id="seeForm" method="POST" action="index.php">
	<input type="hidden" name="id" id="id" value="-1">
	<input type="hidden" name="type" value="seeIndv2">
	</form>
	</div>
	<div style="visibility:hidden;">
	<form id="seeForm2" method="POST" action="index.php">
	<input type="hidden" name="id" id="id2" value="-1">
	<input type="hidden" name="type" value="seeIndv">
	</form>
	</div>
	<script>
	function submitForm(id,t){
		if (t=="1"){
			document.getElementById("id").value=id;
			document.getElementById("seeForm").submit();
		}else{
			document.getElementById("id2").value=id;
			document.getElementById("seeForm2").submit();
		}
	}
	</script>
	<?php
}
?>



