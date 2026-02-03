<?php 
$extraInformation="master.midwifery_2_sem_2";
$config = "../config.php";
include($config);
if (! isset($argv[1])) die("Syntanx Error use \"php subject.php filename.csv\"");
$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to database services");
$linenumber = 0;
$file1 = fopen($argv[1], "r") or die("Could not Open file for Reading");
while (($line = fgets($file1)) !== false)	{	
	$linenumber++;
	if ($linenumber == 1) continue; //Skip Header
	$lineArr = explode(",", $line);
	if (sizeof($lineArr) < 4) continue;
	$subjectCode = trim($lineArr[0]);
	$subjectName = trim($lineArr[1]);
	$lectureHours = trim($lineArr[2]);
	$lectureUnits = trim($lineArr[3]);
	$query = "INSERT INTO subject(subjectCode, subjectName, lectureHours, lectureUnits, extraInformation) VALUES ('$subjectCode', '$subjectName', '$lectureHours', '$lectureUnits', '$extraInformation')";
	$result = mysql_db_query($database, $query, $conn) or die("Could not insert into database ".mysql_error());
}
fclose($file1);
mysql_close($conn);
echo "\nSuccessful\n";
?>