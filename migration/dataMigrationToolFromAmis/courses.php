<?php 
include("../../config.php");
require_once("../../class/system.php");
$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
$oldDatabase="ischool1";
/*Begin Data Here*/
$query = "SELECT courseId, courseName, duration, regcode, nxtregnumber, regnumberwidth, nxtexamnumber, examnumberwidth FROM tblCourse";
$oldResults = mysql_db_query($oldDatabase, $query, $conn) or die("\nCould not fetch Older Records\n");
//Clean table 
$query = "DELETE FROM course";
$result = mysql_db_query($database, $query, $conn) or die("Could not delete courses");
$codeprefix = "CODE";
$counter = 0;
while (list($courseId, $courseName, $duration, $regcode, $nxtregnumber, $regnumberwidth, $nxtexamnumber, $examnumberwidth)=mysql_fetch_row($oldResults))	{
	$counter++;
	if (is_null($regcode) || $regcode == "") $regcode = $codeprefix.$counter;
	$query = "INSERT INTO course (courseId, courseName, duration, courseCode, departmentId, nextRegistrationNumber, registrationNumberWidth, nextExaminationNumber, examinationNumberWidth, levelId) VALUES('$courseId', '$courseName', '$duration', '$regcode', '1', '$nxtregnumber', '$regnumberwidth', '$nxtexamnumber', '$examnumberwidth', '15')";
	mysql_db_query($database, $query, $conn) or die("[$counter] Could not insert record ".mysql_error());
}
/*End Data Here*/
mysql_close($conn);
echo "\nSuccessful\n";
?>
