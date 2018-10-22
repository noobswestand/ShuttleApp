<link rel="stylesheet" href="css/new.css">

<?php

if (isset($_POST["day"]) && isset($_POST["month"]) && isset($_POST["year"])
&& !empty($_POST["day"]) && !empty($_POST["month"]) && !empty($_POST["year"])){
	$_SESSION["day"]=$_POST["day"];
	$_SESSION["month"]=$_POST["month"];
	$_SESSION["year"]=$_POST["year"];
	if (!(isset($_POST["type2"]) && !empty($_POST["type2"]))){
		header("location:index.php");//so we don't redirect twice
	}
}
if (isset($_POST["type2"]) && !empty($_POST["type2"])){
	$_SESSION["type2"]=$_POST["type2"];
	header("location:index.php");
}

if (isset($_SESSION["day"]) && isset($_SESSION["month"])
	&& isset($_SESSION["year"]) && isset($_SESSION["type2"])){
	
	switch($_SESSION["type2"]){
		case "create":
			include_once "create.php";
		break;
		case "seeOptions":
			include_once "see2.php";
		break;
	}
	
}else{
	?>

	<h1 id="loginMessage">Select a day</h1>
	<script>
	var global_day = 0;
	var global_month = 0;
	var global_year = 0;
	var global_previousColor=0;
	var global_previousObj=0;
		function selectDay(day,month,id,monthNumber,year,obj){
			
			if (id!="requestDayNo"){
				
				if (global_year==0){
					global_previousColor=$(obj).css("background-color");
					global_previousObj=obj;
					obj.style.backgroundColor="#4286f4";
				}else{
					global_previousObj.style.backgroundColor=global_previousColor;
					
					global_previousColor=$(obj).css("background-color");
					global_previousObj=obj;
					obj.style.backgroundColor="#4286f4";
				}
				
				
				document.getElementById("loginMessage").innerHTML = month+" "+String(day)
				global_day=day;
				global_month=monthNumber;
				global_year=year;
			}
			
			if (id=="requestDay"){
				showCreate();
			}
			if (id=="requestDayNow"){
				showSee();
			}
		}
	</script>
	<?php
	//For users to create appointments/ride requests
	function createDay($day,$month,$year,$id="requestDay"){
		?>
		<th>
		<div id="<?php echo $id;?>" onclick="selectDay(<?php echo $day;?>,<?php
			$dateObj   = DateTime::createFromFormat('!m', $month);
			echo "'";
			echo $dateObj->format('F'); // March
			echo "'";
			?>,'<?php echo $id;?>',<?php echo $month;?>,<?php echo $year;?>,this)">
			<p style="margin-top:5px;"><?php echo $day;?></p>
		</div>
		</th>
		<?php
	}


	//Create a month selection screen
	$day=date('d');
	$day2=date('w');
	$month=date('m');
	$year=date('Y');
	$dateObj   = DateTime::createFromFormat('!m', $month);
	$monthName = $dateObj->format('F');
	$number = cal_days_in_month(CAL_GREGORIAN, $month, $year)-($day-1);
	$number2=0;
	if ($month<12){
		$number2 = cal_days_in_month(CAL_GREGORIAN, $month+1, $year)-$number;
	}else{
		$year+=1;
		$number2 = cal_days_in_month(CAL_GREGORIAN, 0, $year)-$number;
	}

	//Create 7byX table to put in days
	$style="margin-bottom:0px; text-align:center;margin-left:10px;";
	?>
	<table style="max-width:375px;width:90%;margin:auto; table-layout: fixed;">
		<tr style="color:#fff;margin-left:5px;">
			<th><p style="<?php echo $style;?>">Su</p></th>
			<th><p style="<?php echo $style;?>">Mo</p></th>
			<th><p style="<?php echo $style;?>">Tu</p></th>
			<th><p style="<?php echo $style;?>">We</p></th>
			<th><p style="<?php echo $style;?>">Th</p></th>
			<th><p style="<?php echo $style;?>">Fr</p></th>
			<th><p style="<?php echo $style;?>">Sa</p></th>
		</tr>
		<?php

	//Fill in empty non-selectable with grey days
	echo "<tr>";
	$c=0;
	for($i=($day2%7);$i>0;$i-=1){
		if ($day-$i<1){
			$ii=0;
			if ($month==1){
				$ii = cal_days_in_month(CAL_GREGORIAN, 12, $year)-$i+1;
			}else{
				$ii = cal_days_in_month(CAL_GREGORIAN, $month-1, $year)-$i+1;
			}
			createDay($ii,$month,$year,"requestDayNo");
			
		}else{
			createDay($day-$i,$month,$year,"requestDayNo");
		}
		$c+=1;
	}
	//Create days for first month
	for($i=$day;$i<$day+$number;$i+=1){
		if ($c%7==0){
			echo "<tr>";
		}
		if ($i==$day){
			createDay($i,$month,$year,"requestDayNow");
		}else{
			createDay($i,$month,$year);
		}
		if ($c%7==6){
			echo "</tr>";
		}
		$c+=1;
	}


	//Create days for next month
	$month+=1;
	for($i=1;$i<$number2;$i+=1){
		if ($c%7==0){
			echo "<tr>";
		}
		createDay($i,$month,$year);
		if ($c%7==6){
			echo "</tr>";
		}
		$c+=1;
	}
	?>
	</table><br><br>
	<button id="createButton" onclick="doCreate()">Create</button>
	<button id="seeButton" onclick="doSee()">See Options</button>
	<button id="nullButton" style="visibility: hidden;">Null</button>
	
	
	<form style="display: none;" id="newForm" method="POST" action="index.php">
	<input type="hidden" id="day" name="day" value="-1">
	<input type="hidden" id="month" name="month" value="-1">
	<input type="hidden" id="year" name="year" value="-1">
	<input type="hidden" id="type" name="type2" value="-1">
	</form>

	<script>
	function showCreate(){
		$("#createButton").show();
		$("#seeButton").hide();
		document.getElementById ( "nullButton" ).style.display = "none" ;
	}
	function showSee(){
		$("#createButton").hide();
		$("#seeButton").show();
		document.getElementById ( "nullButton" ).style.display = "none" ;
	}
	function doCreate(){
		document.getElementById("day").value = global_day.toString();
		document.getElementById("month").value = global_month.toString();
		document.getElementById("year").value = global_year.toString();
		document.getElementById("type").value = "create";
		document.getElementById("newForm").submit();
	}
	function doSee(){
		document.getElementById("day").value = global_day.toString();
		document.getElementById("month").value = global_month.toString();
		document.getElementById("year").value = global_year.toString();
		document.getElementById("type").value = "seeOptions";
		document.getElementById("newForm").submit();
	}

	setTimeout(function() {
		$("#createButton").hide();
		$("#seeButton").hide();
	}, 1);

	</script>
	<?php
}
?>