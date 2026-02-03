<?php
/*
INPUT: param1 [schemaId] , param2 [Content], param3 [loginId]

OUTPUT: json code, message 
*/
if (session_status() == PHP_SESSION_NONE)	{
	session_start();
}
if (! isset($_SESSION['login'][0]['id']))	{
	die(json_encode(array("code"=>"1","message"=>"You are not Logged In to the System")));
}
require_once("../class/system.php");
$config="../config.php";
include($config);
if (! (isset($_POST['param1']) && isset($_POST['param2']) && isset($_POST['param3']) && isset($_POST['param4']))) die(json_encode(array("code"=>"1","message"=>"Some parameters were not set properly")));
$schemaId = $_POST['param1'];
$extraInformation = $_POST['param2'];
$loginId = $_POST['param3'];
$specialInstruction = $_POST['param4'];
$conn = mysql_connect($hostname, $user, $pass) or die(json_encode(array("code"=>"1","message"=>"Could not connect to a database services")));
date_default_timezone_set("Africa/Dar_es_Salaam");
$date=date("Y:m:d:H:i:s");
$jobId = null;
try {
	$schema1 = new ApprovalSequenceSchema($database, $schemaId, $conn);
	$jobId = $schema1->getFirstApprovingJob();
} catch (Exception $e)	{
	$message = $e->getMessage();
	die(json_encode(array("code"=>"1","message"=>$message)));
}
$query = "INSERT INTO approvalSequenceData(schemaId, requestedBy, specialInstruction, timeOfRegistration, extraInformation) VALUES ('$schemaId', '$loginId', '$specialInstruction', '$date', '$extraInformation')";
if (! is_null($jobId))	{
	$query = "INSERT INTO approvalSequenceData(schemaId, requestedBy, nextJobToApprove, specialInstruction, timeOfRegistration, extraInformation) VALUES ('$schemaId', '$loginId', '$jobId', '$specialInstruction', '$date', '$extraInformation')";
}
$result = mysql_db_query($database, $query, $conn) or die(json_encode(array("code"=>"1","message"=>"[Approval Data] Could not execute query")));
mysql_close($conn);
echo json_encode(array("code"=>"0","message"=>"successful"));
?>