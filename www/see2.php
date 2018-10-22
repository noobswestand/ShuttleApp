<h1 id="loginMessage">See what is available</h1>
<div style='height:2px;background-color:#ccc;width:100%;'></div>
<br>
<?php
//For students who try to schedule on the same day - this will pop up

include_once "db.php";
$id=$_SESSION["id"];
$day=$_SESSION["day"];
$month=$_SESSION["month"];
$year=$_SESSION["year"];
/*
$day=$_POST["day"];
$month=$_POST["month"];
$year=$_POST["year"];
$id=$_SESSION["id"];
*/
$_POST["createHide"]="1";
include_once "see3.php";
?>

