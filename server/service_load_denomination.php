<?php 
$config="../config.php";
include($config);
/*Expects param1=> religionId 
OUTPUT json , code[0], message[text], 0{option{id, val}}
*/
if (! isset($_REQUEST['param1'])) die(json_encode(array("code"=>"1", "message"=>"Parameter List Were Not Set Properly")));
$conn = mysql_connect($hostname, $user, $pass) or die(json_encode(array("code"=>"1", "message"=>"Could not connect to database services")));
$religionId = $_REQUEST['param1'];
$query = "SELECT denominationId, denominationName FROM denomination WHERE religionId='$religionId'";
$result = mysql_db_query($database, $query, $conn) or die(json_encode(array("code"=>"1", "message"=>"Could Not Extract Denomination List")));
$resultArray = array();
$resultArray['code'] = "0";
$resultArray['message'] = "Server--Successful";
$resultArray['options'] = array();
$counter = 0;
while (list($__id, $__name) = mysql_fetch_row($result))	{
	$resultArray['options'][$counter] = array();
	$resultArray['options'][$counter]['option'] = array();
	$resultArray['options'][$counter]['option']['id'] = $__id;
	$resultArray['options'][$counter]['option']['val'] = $__name;
	$counter++;
}
mysql_close($conn);
echo json_encode($resultArray);
?>