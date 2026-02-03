<?php 
if (session_status() == PHP_SESSION_NONE)	{
	session_start();
}
/*
Input 
	param1: username 
	param2: email
	param3: phone 
	param4: currentLoginId {We need to check uniqueness with records which are not the same as the currentLoginId, it could be null too}
*/
$config="../config.php";
include($config);
require_once("../common/validation.php");
require_once("../class/system.php");
require_once("authorization.php");
require_once("accounting.php");
$conn = mysql_connect($hostname, $user, $pass) or die(json_encode(array("code"=>"1", "message"=>"Could not establish connection with a database service")));
date_default_timezone_set("Africa/Dar_es_Salaam");
$date=date("Y:m:d:H:i:s");
$systemDate1 = new DateAndTime("Ndimangwa", $date, "Fadhili");
if (! (isset($_POST['param1']) && isset($_POST['param2']) && isset($_POST['param3']) && isset($_POST['param4']))) die(json_encode(array("code"=>"1", "message"=>"Could not verify some Parameters are missing")));
$loginName=$_POST['param1'];
$email = $_POST['param2'];
$phone = $_POST['param3'];
$currentLoginId = $_POST['param4'];
$query = "SELECT loginId FROM login WHERE (loginName='$loginName' OR email='$email' OR phone='$phone') AND loginId <> '$currentLoginId'";
$result = mysql_db_query($database, $query, $conn) or die(json_encode(array("code"=>"1","message"=>"Could not pull data from the database service")));
if (mysql_num_rows($result) != 0) {
	mysql_close($conn);
	die(json_encode(array("code"=>"1","message"=>"The username or email or phone address is already in use")));
}
mysql_close($conn);
echo json_encode(array("code"=>"0","message"=>"available"));
?>