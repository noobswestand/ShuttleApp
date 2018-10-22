

<?php
include_once "db.php";
$id=$_SESSION["id"];

dbConnect();
$day=date('j');
$month=date('m');
$year=date('Y');
//$st=time();
//$dt = new DateTime("@$st"); 
//$time=date_format($dt,"Y-n-d");
$time=date("Y-m-d");

$sql="SELECT * FROM request
INNER JOIN requeststudent ON request.RequestID=requeststudent.RequestID
WHERE requeststudent.StudentID=$id
AND request.TimePickUp>='$time'
ORDER BY TimePickUp;";
$result=query($sql);
dbClose();


if (empty($result)){
	notify("You have no rides scheduled!","Back","msgReturn()");
}else{
	echo "<h1>Your rides</h1>";
	echo "<ul class='list-group align-items-center'>";
	
	foreach ($result as $r){
		$to=$r["LocationTo"];
		$from=$r["LocationFrom"];
		$date = strtotime($r["TimePickUp"]);
		$time=date("F",$date)." ".date("j",$date);
		$time2=date("g",$date).":".date("i",$date)." ".date("A",$date);
		
		//Get adjusted time
		if ($r["TimePickUpNew"]!=""){
			$date2 = strtotime($r["TimePickUpNew"]);
			$time2=date("g",$date2).":".date("i",$date2)." ".date("A",$date2);
			
		}
		$time.=" ".$time2;
		
		
		$i=$r["RequestID"];
		$img="";
		if ($r["DriverID"]!=""){//accepted by driver
			$img='<button type="button" class="btn btn-success btn-circle"><i class="material-icons" style="margin-top:-3px;">done</i></button>';
		}else{//null
			$img='<button type="button" class="btn btn-danger btn-circle"><i class="material-icons" style="margin-top:-4px;">access_time</i></button>';
		}
		
		?>
		<li class="list-group-item" style="text-align:left;padding-left:15px;" onclick="submitForm(<?php echo $i;?>)">
			<table style="color:#b7b7b7;font-family:loginThin;width:100%;">
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
	echo "</ul>";
	?>
	<div style="visibility:hidden;">
	<form id="seeForm" method="POST" action="index.php">
	<input type="hidden" name="id" id="id" value="-1">
	<input type="hidden" name="type" value="seeIndv">
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
?>



