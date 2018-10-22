<?php
include_once "db.php";
$state=0;

if (isset($_POST["step"])){
	switch($_POST["step"]){
		case "1":
			if (isset($_POST["year"])){
				$state=1;
			}
		break;
		case "2":
			if (isset($_POST["month"]) && isset($_SESSION["year"])){
				$state=2;
			}
		break;
		
		case "3":
			if (isset($_POST["day"]) && isset($_SESSION["month"]) && isset($_SESSION["year"])){
				$state=3;
			}
		break;
		
		
		
		default:
			notify("An error has occured!","Back","msgReturn()");
		break;
	}
}


//Show day
if ($state==3){
	dbConnect();
	$year=$_SESSION["year"];
	$month=$_SESSION["month"];
	$day=clean($_POST["day"]);
	$_SESSION["day"]=$day;
	$pid=$_SESSION["pid"];
	$sql="SELECT * FROM request
	WHERE YEAR(TimePickUp)=$year
	AND MONTH(TimePickUp)=$month
	AND DAY(TimePickUp)=$day
	AND DriverID=$pid
	ORDER BY TimePickUp";
	$result=query($sql);
	echo "<ul class='list-group align-items-center'>";
	foreach ($result as $r){
		$to=$r["LocationTo"];
		$from=$r["LocationFrom"];
		$date = strtotime($r["TimePickUpNew"]);
		$time=date("F",$date)." ".date("j",$date)."  ".date("g",$date).":".date("i",$date)." ".date("A",$date);
		$i=$r["RequestID"];
		?>
		<li class="list-group-item" style="text-align:left;" onclick="submitForm(<?php echo $i;?>)">
			<table style="color:#b7b7b7;font-family:loginThin;">
			<tr>
				<th></th>
				<th><font size="4em" color="#fff"><?php echo $time?></font></th>
				<th></th>
			</tr>
			<tr>
				<th></th>
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
	?>
	<div style="visibility:hidden;">
	<form id="seeForm" method="POST" action="index.php">
	<input type="hidden" name="id" id="id" value="-1">
	<input type="hidden" name="step" id="step" value="1">
	<input type="hidden" name="type" id="type" value="dsee">
	<input type="hidden" name="history" id="history" value="1">
	</form>
	</div>
	<script>
	function submitForm(id){
		document.getElementById("id").value=id;
		document.getElementById("seeForm").submit();
	}
	</script>
	<?php
}

//Day select
if ($state==2){
	dbConnect();
	$month=clean($_POST["month"]);
	$_SESSION["month"]=$month;
	$year=$_SESSION["year"];
	$dateObj   = DateTime::createFromFormat('!m', $month);
	$monthName = $dateObj->format('F'); // March
	
	echo "<h1>$monthName-$year</h1>";
	echo "<p>Pick a day</p>";
	$pid=$_SESSION["pid"];
	$sql="SELECT DISTINCT DAY(TimePickUp) FROM request
	WHERE YEAR(TimePickUp)=$year
	AND MONTH(TimePickUp)=$month
	AND DriverID=$pid
	ORDER BY DAY(TimePickUp)";
	$result=query($sql);
	echo '<ul class="list-group align-items-center">';
	foreach($result as $r){
		$d=$r["DAY(TimePickUp)"];
		$sql="SELECT COUNT(*) FROM request
		WHERE YEAR(TimePickUp)=$year AND MONTH(TimePickUp)=$month
		AND DAY(TimePickUp)=$d
		AND DriverID=$pid";
		$c=query($sql)[0]["COUNT(*)"];
		$img='<button style="float:left;" type="button" class="btn btn-circle"><i style="margin-top:-4px;"><strong>'.$c.'</strong></i></button>';
		
		echo "<li class='list-group-item' onclick='history($d)'>$img<a>$d</a></li>";
	}
	echo "</ul>";
	dbClose();
	?>	
	<div style="visibility:hidden;">
	<form id="historyForm" method="POST" action="index.php">
	<input type="hidden" name="step" value="3">
	<input type="hidden" id="day" name="day" value="-1">
	</form>
	</div>
	
	<script>
	function history(day){
		document.getElementById("day").value=day;
		document.getElementById("historyForm").submit();
	}
	</script>
	<?php
	
}

//Month select
if ($state==1){
	dbConnect();
	$year=clean($_POST["year"]);
	$_SESSION["year"]=$year;
	echo "<h1>$year</h1>";
	echo "<p>Pick a month</p>";
	
	$pid=$_SESSION["pid"];
	$sql="SELECT DISTINCT MONTH(TimePickUp) FROM request
	WHERE YEAR(TimePickUp)=$year
	AND DriverID=$pid
	ORDER BY MONTH(TimePickUp)";
	
	$result=query($sql);
	echo '<ul class="list-group align-items-center">';
	foreach($result as $r){
		$m=$r["MONTH(TimePickUp)"];
		$sql="SELECT COUNT(*) FROM request
		WHERE YEAR(TimePickUp)=$year AND MONTH(TimePickUp)=$m
		AND DriverID=$pid";
		$c=query($sql)[0]["COUNT(*)"];
		$img='<button style="float:left;" type="button" class="btn btn-circle"><i style="margin-top:-4px;"><strong>'.$c.'</strong></i></button>';
		
		$dateObj   = DateTime::createFromFormat('!m', $m);
		$monthName = $dateObj->format('F'); // March
		echo "<li class='list-group-item' onclick='history($m)'>$img<a>$monthName</a></li>";
	}
	echo "</ul>";
	dbClose();
	
	?>	
	<div style="visibility:hidden;">
	<form id="historyForm" method="POST" action="index.php">
	<input type="hidden" name="step" value="2">
	<input type="hidden" id="month" name="month" value="-1">
	</form>
	</div>
	
	<script>
	function history(month){
		document.getElementById("month").value=month;
		document.getElementById("historyForm").submit();
	}
	</script>
	<?php
}


//Main screen (year select)
if ($state==0){
	echo "<h1 style='display:inline-block;margin-right:15px;'>Pick a year or </h1>
	<button style='display:inline-block;width:100px' id='createButton' onclick='expot()'>Export</button>";
	$pid=$_SESSION["pid"];
	//Get years
	dbConnect();
	$sql="SELECT DISTINCT YEAR(TimePickUp) from request
	WHERE DriverID=$pid
	ORDER BY YEAR(TimePickUp) DESC";
	$result=query($sql);
	
	echo '<ul class="list-group align-items-center">';
	foreach($result as $r){
		$y=$r["YEAR(TimePickUp)"];
		$sql="SELECT COUNT(*) FROM request
		WHERE YEAR(TimePickUp)=$y
		AND DriverID=$pid";
		$c=query($sql)[0]["COUNT(*)"];
		$img='<button style="float:left;" type="button" class="btn btn-circle"><i style="margin-top:-4px;"><strong>'.$c.'</strong></i></button>';
		echo "<li class='list-group-item' onclick='history($y)'>$img<a>$y</a></li>";
	}
	
	echo "</ul>";
	dbClose();
	?>
	<div style="visibility:hidden;">
	<form id="historyForm" method="POST" action="index.php">
	<input type="hidden" name="step" value="1">
	<input type="hidden" id="year" name="year" value="-1">
	</form>
	</div>
	
	<div style="visibility:hidden;">
	<form id="exportForm" method="POST" action="index.php">
	<input type="hidden" name="type" value="dexport">
	</form>
	</div>
	
	<script>
	function expot(){
		document.getElementById("exportForm").submit();
	}
	function history(year){
		document.getElementById("year").value=year;
		document.getElementById("historyForm").submit();
	}
	</script>
	<?php
}

?>