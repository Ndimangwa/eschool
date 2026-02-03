<?php 
if (sizeof($argv) !== 6) die("\nCommand Syntanx : \"php generateTranscript.php courseName academicYear lastYearOfStudyToInclude lastSemesterOfStudyToInclude flags\" \n Example : php generateTranscript.php \"Doctor of Medicine\" 2014/2015 5 2 0\n");
require_once("../class/system.php");
$config="../config.php";
include($config);
$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
$courseName = trim($argv[1]);
$academicYear = trim($argv[2]);
$upToYear = intval($argv[3]);
$upToSemester = intval($argv[4]);
$flags = intval($argv[5]);
$numberOfTranscriptsCreated = 0;
try {
	$profile1 = new Profile($database, Profile::getProfileReference($database, $conn), $conn);
	$systemTime1 = new DateAndTime("Ndimangwa", Object::getCurrentTimestamp(), "Fadhili Ngoya");
	$courseId = Course::getCourseIdFromCourseName($database, $conn, $courseName);
	$academicYearId = AccademicYear::getBatchIdFromName($database, $conn, $academicYear);
	$generalDataFolder = "../data/";
	$resultsFolder = $generalDataFolder."results/";
	$transcriptFolder = $generalDataFolder."transcripts/";
	$numberOfTranscriptsCreated = Course::createTranscript($database, $conn, $profile1, $systemTime1, $courseId, $transcriptFolder, $resultsFolder, $academicYearId, $upToYear, $upToSemester, $flags);
} catch (Exception $e)	{
	mysql_close($conn);
	die("\n".$e->getMessage()."\n");
}
mysql_close($conn);
echo "\n[ $numberOfTranscriptsCreated ] transcripts were created\n";
?>