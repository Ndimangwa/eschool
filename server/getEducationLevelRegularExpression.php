<?php 
/*
INPUT param1, param2
OUTPUT: code, message, expressions {levelId {validateExpression, validateMessage}}
*/
require_once("../class/system.php");
$config="../config.php";
if (! (isset($_POST['param1']) && isset($_POST['param2']) && isset($_POST['param3']) && isset($_POST['param4']))) die(json_encode(array("code"=>"1","message"=>"Perhaps data lost on Transit")));
$indexNumberExpression = $_POST['param1'];
$indexNumberMessage = $_POST['param2'];
$gradeEarnedExpression = $_POST['param3'];
$gradeEarnedMessage = $_POST['param4'];
include($config);
$conn = mysql_connect($hostname, $user, $pass) or die(array("code"=>"1","message"=>"Could not connect to a database system"));
$list1 = null;
try {
	$list1 = EducationLevel::loadAllData($database, $conn);
} catch (Exception $e)	{
	$message = $e->getMessage();
	die(json_encode(array("code"=>"1","message"=>$message)));
}
$resultArray = array();
$resultArray['code'] = "0";
$resultArray['message'] = "Server-Successful";
$resultArray['expressions'] = array();
foreach ($list1 as $alist1)	{
	$levelId = $alist1['id'];
	$level1 = null;
	try {
		$level1 = new EducationLevel($database, $levelId, $conn);
	} catch (Exception $e)	{
		$message = $e->getMessage();
		die(json_encode(array("code"=>"1","message"=>$message)));
	}
	$resultArray['expressions'][$levelId] = array();
	$resultArray['expressions'][$levelId]['indexNumberExpression'] = $indexNumberExpression;
	$resultArray['expressions'][$levelId]['indexNumberMessage'] = $indexNumberMessage;
	$resultArray['expressions'][$levelId]['gradeEarnedExpression'] = $gradeEarnedExpression;
	$resultArray['expressions'][$levelId]['gradeEarnedMessage'] = $gradeEarnedMessage;
	if (! is_null($level1->getIndexNumberExpression()))	{
		$resultArray['expressions'][$levelId]['indexNumberExpression'] = $level1->getIndexNumberExpression();
	}
	if (! is_null($level1->getIndexNumberMessage()))	{
		$resultArray['expressions'][$levelId]['indexNumberMessage'] = $level1->getIndexNumberMessage();
	}
	if (! is_null($level1->getGradeEarnedExpression()))	{
		$resultArray['expressions'][$levelId]['gradeEarnedExpression'] = $level1->getGradeEarnedExpression();
	}
	if (! is_null($level1->getGradeEarnedMessage()))	{
		$resultArray['expressions'][$levelId]['gradeEarnedMessage'] = $level1->getGradeEarnedMessage();
	}
}
mysql_close($conn);
echo json_encode($resultArray);
?>