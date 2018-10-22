<?php

//Once the user is logged in
include_once "db.php";
?>

<script>
$(function(){
    
    $('.list-group li').click(function(e) {
        e.preventDefault()
        
        $that = $(this);
        
        $('.list-group').find('li').removeClass('active');
        $that.addClass('active');
    });
})
</script>
<h1>Welcome</h1>
<p>Select your action</p>
<ul class="list-group align-items-center">
	<li class="list-group-item" onclick="newRide()"><a>Schedule a ride</a></li>
	<li class="list-group-item" onclick="see()"><a>See my schedule</a></li>
	<li class="list-group-item" onclick="settings()"><a>Account settings</a></li>
	<li class="list-group-item" onclick="window.location.href='logout.php'"><a href="logout.php">Logout</a></li>
</ul>

<div style="visibility:hidden;">
<form id="newForm" method="POST" action="index.php">
<input type="hidden" name="type" value="newStart">
</form>
</div>
<div style="visibility:hidden;">
<form id="seeForm" method="POST" action="index.php">
<input type="hidden" name="type" value="seeStart">
</form>
</div>
<div style="visibility:hidden;">
<form id="settingsForm" method="POST" action="index.php">
<input type="hidden" name="type" value="settings">
</form>
</div>

<script>
function newRide(){
	document.getElementById("newForm").submit();
}
function see(){
	document.getElementById("seeForm").submit();
}
function settings(){
	document.getElementById("settingsForm").submit();
}
</script>

