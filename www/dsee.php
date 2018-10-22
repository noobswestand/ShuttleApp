<?php
include_once "db.php";


if (isset($_POST["step"])){
	include_once "dsee2.php";
}else{
	//Get what you have
	dbConnect();
	$id=$_SESSION["pid"];
	$day=date('j');
	$month=date('m');
	$year=date('Y');
	$time=date("Y-m-d");
	$sql="SELECT * FROM request WHERE DriverID=$id AND TimePickUp>='$time'
	ORDER BY TimePickUp;";

	$result=query($sql);
	dbClose();
	if (empty($result)){
		notify("You have picked up no rides!","Back","msgReturn()");
	}else{
		
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
		</form>
		</div>
		<script>
		function submitForm(id){
			document.getElementById("id").value=id;
			document.getElementById("seeForm").submit();
		}
		</script>
		<?php
	}//else
}//$_POST["step"]
?>