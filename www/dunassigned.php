<?php
include_once "db.php";

//Get what is unassigned
dbConnect();
$day=date('j');
$month=date('m');
$year=date('Y');
$time=date("Y-m-d");
$sql="SELECT * FROM request WHERE TimePickUp>='$time' AND DriverID IS NULL;";
$result=query($sql);
dbClose();
?>
<h1>Unassigned rides</h1>
<?php
if (empty($result)){
	notify("There are no unassigned rides!","Back","msgReturn()");
}else{
	echo "<ul class='list-group align-items-center'>";
	foreach ($result as $r){
		$to=$r["LocationTo"];
		$from=$r["LocationFrom"];
		$date = strtotime($r["TimePickUp"]);
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
	</ul>
	
	
	<div style="visibility:hidden;">
	<form id="seeForm" method="POST" action="index.php">
	<input type="hidden" name="id" id="id" value="-1">
	<input type="hidden" name="type" value="dseeIndv">
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