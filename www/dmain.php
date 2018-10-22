
<?php
include_once "db.php";

dbConnect();
$day=date('j');
$month=date('n');
$year=date('Y');
$time=date("Y-m-d");
$sql="SELECT COUNT(*) FROM request WHERE TimePickUp>='$time' AND DriverID IS NULL;";

$result=query($sql);
$count=$result[0]["COUNT(*)"];
//Get how many unassigned rides there are
if ($count==0){
	$img='<button style="float:left;" type="button" class="btn btn-success btn-circle"><i class="material-icons" style="margin-top:-3px;">done</i></button>';
}else{//null
	$img='<button style="float:left;" type="button" class="btn btn-danger btn-circle"><i style="margin-top:-4px;"><strong>'.$count.'</strong></i></button>';
}

dbClose();
?>
<h1>Welcome</h1>
<p>Select your action</p>
<ul class="list-group align-items-center">
	<li class="list-group-item" onclick="see()"><a>See my schedule</a></li>
	<li class="list-group-item" onclick="rides()"><?php echo $img;?><a>Unassigned Rides</a></li>
	<li class="list-group-item" onclick="settings()"><a>Account settings</a></li>
	<li class="list-group-item" onclick="history()"><a>History</a></li>
	<li class="list-group-item" onclick="window.location.href='logout.php'"><a href="logout.php">Logout</a></li>
</ul>


<div style="visibility:hidden;">
<form id="seeForm" method="POST" action="index.php">
<input type="hidden" name="type" value="dsee">
</form>
</div>

<div style="visibility:hidden;">
<form id="ridesForm" method="POST" action="index.php">
<input type="hidden" name="type" value="dunassigned">
</form>
</div>

<div style="visibility:hidden;">
<form id="settingsForm" method="POST" action="index.php">
<input type="hidden" name="type" value="settings">
</form>
</div>

<div style="visibility:hidden;">
<form id="historyForm" method="POST" action="index.php">
<input type="hidden" name="type" value="dhistory">
</form>
</div>


<script>
function see(){
	document.getElementById("seeForm").submit();
}
function rides(){
	document.getElementById("ridesForm").submit();
}
function settings(){
	document.getElementById("settingsForm").submit();
}
function history(){
	document.getElementById("historyForm").submit();
}
</script>