<?php


function vincentyGreatCircleDistance(
	$latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000){
	// convert from degrees to radians
	$latFrom = deg2rad($latitudeFrom);
	$lonFrom = deg2rad($longitudeFrom);
	$latTo = deg2rad($latitudeTo);
	$lonTo = deg2rad($longitudeTo);

	$lonDelta = $lonTo - $lonFrom;
	$a = pow(cos($latTo) * sin($lonDelta), 2) +
	pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta), 2);
	$b = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lonDelta);

	$angle = atan2(sqrt($a), $b);
	return round(($angle * $earthRadius * 0.621371)/1000,2);
}


function notify($msg,$ok,$action="msgHide()"){
	?>
	<div class="modal" id="notifyModal" role="dialog" data-keyboard="false" data-backdrop="static">
		<div class="modal-dialog modal-sm  modal-dialog-centered">
			<!-- Modal content-->
			<div class="modal-content" style="box-shadow:none;border-radius:0px; background-color:#435159;">
				<div class="modal-body">
					<h1><?php echo $msg;?></h1>
					<button id="submitButton" type="button" class="btn btn-default center-block" onclick="<?php echo $action;?>"><?php echo $ok;?></button>
				</div>
			</div>
		</div>
	</div>
	<script>
	$('#notifyModal').modal('toggle');
	function msgHide(){
	  $('#notifyModal').modal('toggle');
	}
	</script>
	
  <?php
}
$val="";
$val2="";
if (isset($_SESSION['state'])){
	if ($_SESSION["state"]=="forgot"){
		$val="login";
	}else{
		if (!isset($_SESSION["driver"]) || $_SESSION["driver"]==0){
			$val="main";
		}else{
			$val="dmain";
		}
	}
	$val2="seeStart";
}

?>

<form style="display: none;" id="returnForm" method="POST" action="index.php">
<input type="hidden" name="type" value="<?php echo $val;?>">
</form>
<script>
function msgReturn(){
	document.getElementById("returnForm").submit();
}
</script>


<form style="display: none;" id="scheduleForm" method="POST" action="index.php">
<input type="hidden" name="type" value="<?php echo $val2;?>">
</form>
<script>
function seeSchedule(){
	document.getElementById("scheduleForm").submit();
}
</script>