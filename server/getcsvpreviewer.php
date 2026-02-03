<?php 
/*
INPUT param1 
OUTPUT: code, message, rows {i{tr{j{td}}}}
*/
require_once("../class/system.php");
$config="../config.php";
include($config);
$conn = mysql_connect($hostname, $user, $pass) or die(json_encode(array("code"=>"1","message"=>"Could not connect to a database services")));
$profile1 = null;
try {
	$__profileId = Profile::getProfileReference($database, $conn);
	$profile1 = new Profile($database, $__profileId, $conn);
} catch(Exception $e)	{
	$message = $e->getMessage();
	mysql_close($conn);
	die(json_encode(array("code"=>"1","message"=>"$message")));
}
mysql_close($conn);
if (! isset($_POST['param1'])) die(json_encode(array("code"=>"1","message"=>"Some parameters were not set properly")));
$filename = $_POST['param1'];
$resultArray = array();
$resultArray['code'] = "0";
$resultArray['message'] = "Server-Successful";
$resultArray['recordsLimitPerPage'] = $profile1->getMaximumNumberOfDisplayedRowsPerPage();
$resultArray['rows'] = array();
$counter = 0;
if (! file_exists($filename)) die(json_encode(array("code"=>"1","message"=>"File Does Not Exists")));
$file1 = fopen($filename, "r") or die(json_encode(array("code"=>"1","message"=>"Could not Open the Source File for Reading")));
while (($line = fgets($file1)) !== false) {
    // process the line read.
	$resultArray['rows'][$counter] = array();
	$resultArray['rows'][$counter]['tr'] = array();
	$dataArray = explode(",",$line);
	for ($j=0; $j<sizeof($dataArray);$j++)	{
		$dt = $dataArray[$j];
		$resultArray['rows'][$counter]['tr'][$j] = array();
		$resultArray['rows'][$counter]['tr'][$j]['td'] = $dt;
	}
	$counter++;
}
fclose($file1); 
echo json_encode($resultArray);
?>