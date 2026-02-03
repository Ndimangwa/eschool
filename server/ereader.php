<?php 
include("../config.php");
require_once("../common/validation.php");
require_once("../class/system.php");
$conn=mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
$profile1 = null;
$__profileId = Profile::getProfileReference($database, $conn);
try {
	$profile1 = new Profile($database, $__profileId , $conn);
	$profile1->loadXMLFolder("../data/profile");
} catch (Exception $e)	{
	die($e->getMessage());
}
mysql_close($conn);
if ($profile1->isInstallationComplete())	{
	header("Location: ../");
	exit();
}
$dataFolder = "data";
$themeFolder = "sunny"; //--set this as a default 
if (! is_null($profile1->getTheme())) $themeFolder = $profile1->getTheme()->getThemeFolder();
//We Just need to get A Default Timezone so we can extra a year
$timezone="Africa/Dar_es_Salaam";
if (! is_null($profile1->getPHPTimezone())) $timezone = $profile1->getPHPTimezone()->getZoneName();
date_default_timezone_set($timezone);
$date=date("Y:m:d:H:i:s");
$__systemDayOffset = 0;
if (! is_null($profile1->getFirstDayOfAWeek()))	{
	$__systemDayOffset = $profile1->getFirstDayOfAWeek()->getOffsetValue();
}
$systemDate1 = new DateAndTime("Ndimangwa", $date, "Fadhili");
$thispage=$_SERVER['PHP_SELF'];
if (! isset($readerSection)) die("Reader Section is Not Set");
?>
<html>
<head>
<title><?= $profile1->getProfileName() ?></title>
<link rel="stylesheet" type="text/css" media="all" href="../client/jquery-ui-1.11.3/themes/<?= $themeFolder ?>/jquery-ui.css"/>
<link rel="stylesheet" type="text/css" media="all" href="../client/css/site.css"/>
<style type="text/css">

</style>
<script type="text/javascript" src="../client/jquery.js"></script>
<script type="text/javascript" src="../client/jquery-ui-1.11.3/jquery-ui.js"></script>
<script type="text/javascript" src="../client/jquery-easy-ticker-master/jquery.easy-ticker.js"></script>
<script type="text/javascript" src="../client/js/jvalidation.js"></script>
<script type="text/javascript" src="../client/js/page.js"></script>
<script type="text/javascript">
(function($)	{
	$(function()	{
		/*Date Handling*/
		$('.datepicker').datepicker({
			dateFormat: 'dd/mm/yy',
			firstDay: <?= $__systemDayOffset ?>,
			changeYear: true,
			yearRange:'1961:2099'
		});
		//Tesiting
		//$('input[type="text"]').textinput();
	});
})(jQuery);
</script>
</head>
<body class="ui-sys-body">

<div class="ui-sys-printable ui-sys-size-a4-potrait">
<?php 
	if ($readerSection == "profileData")	{
		
	} else {
?>
		Nothing were set or Wrongly Selected Page
<?php
	}
?>
</div>

</body>
</html>