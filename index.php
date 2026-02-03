<?php 
if (session_status() == PHP_SESSION_NONE)	{
	session_start();
}
if (isset($_SESSION['login'][0]['id']))	{
	header("Location: profile/");
	exit();
}
include("config.php");
require_once("common/validation.php");
require_once("class/system.php");
$conn=mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
$profile1 = null;
try {
	$profile1 = new Profile($database, Profile::getProfileReference($database, $conn), $conn);
	$profile1->loadXMLFolder("data/profile");
} catch (Exception $e)	{
	die($e->getMessage());
}
mysql_close($conn);
//Will redirect Automatically If Necessary
System::systemSSLTLSCertificateVerification($profile1);
if (! $profile1->isInstallationComplete())	{
	header("Location: installation/");
	exit();
}
$dataFolder = "data";
$themeFolder = "sunny"; //--set this as a default 
if (! is_null($profile1->getTheme())) $themeFolder = $profile1->getTheme()->getThemeFolder();
//We Just need to get A Default Timezone so we can extra a year
date_default_timezone_set("Africa/Dar_es_Salaam");
$date=date("Y:m:d:H:i:s");
$systemDate1 = new DateAndTime("Ndimangwa", $date, "Fadhili");
?>
<html>
<head>
<title><?= $profile1->getProfileName() ?></title>
<link rel="stylesheet" type="text/css" media="all" href="client/jquery-ui-1.11.3/themes/<?= $themeFolder ?>/jquery-ui.css"/>
<link rel="stylesheet" type="text/css" media="all" href="client/css/purecss/pure-min.css"/>
<link rel="stylesheet" type="text/css" media="all" href="client/css/site.css"/>
<style type="text/css">

</style>
<script type="text/javascript" src="client/jquery.js"></script>
<script type="text/javascript" src="client/jquery-ui-1.11.3/jquery-ui.js"></script>
<script type="text/javascript" src="client/jquery-easy-ticker-master/jquery.easy-ticker.js"></script>
<script type="text/javascript" src="client/js/jvalidation.js"></script>
<script type="text/javascript" src="client/js/page.js"></script>
<script type="text/javascript">

</script>
</head>
<body class="ui-sys-body">

<div class="ui-sys-main">
	<div class="ui-sys-front-header">
		<div class="ui-sys-front-logo mobile-collapse">
<?php 
	$logo = $dataFolder."/".$profile1->getDataFolder()."/logo/default.jpg";
	if (! is_null($profile1->getLogo())) $logo = $dataFolder."/".$profile1->getDataFolder()."/logo/".$profile1->getLogo();
?>
			<img alt="LG" src="<?= $logo ?>"/>
		</div>
		<div class="ui-sys-front-header-1 mobile-collapse">
			<div class="ui-sys-inst-name"><?= $profile1->getProfileName() ?></div>
<?php 
	if (! is_null($profile1->getWebsite()))	{
?>
			<div class="ui-sys-micro-data ui-sys-website">Website: <?= $profile1->getWebsite() ?></div>
<?php 
	}
	if (! is_null($profile1->getEmailList()))	{
		$list = Object::getCommaSeparatedListFromArray($profile1->getEmailList());
?>
			<div class="ui-sys-micro-data ui-sys-email">Email: <?= $list ?></div>
<?php
	}
	if (! is_null($profile1->getTelephoneList()))	{
		$list = Object::getCommaSeparatedListFromArray($profile1->getTelephoneList());
?>
			<div class="ui-sys-micro-data ui-sys-telex">Telephone: <?= $list ?></div>
<?php 
	}
	if (! is_null($profile1->getFaxList()))	{
		$list = Object::getCommaSeparatedListFromArray($profile1->getFaxList());
?>
			<div class="ui-sys-micro-data ui-sys-fax">Fax: <?= $list ?></div>
<?php 
	}
?>
		</div>
		<div class="ui-sys-clearboth">&nbsp;</div>
	</div>
<?php 
	if (! is_null($profile1->getSystemName()))	{
?>
	<div class="ui-sys-front-systemname mobile-collapse"><?= $profile1->getSystemName() ?></div>
<?php 
	}
?>
	<div class="ui-sys-front-topbutton mobile-collapse">
<?php 
	if (true)	{
		$admissionYear = ""; if (! is_null($profile1->getAdmissionAcademicYear())) $admissionYear = $profile1->getAdmissionAcademicYear()->getAccademicYear();
?>
		<a class="button-link" title="Admission for <?= $admissionYear ?>" href="admission/">ADMISSION FOR <?= $admissionYear ?></a>&nbsp;&nbsp;
<?php
	}
	if ($profile1->isAdmissionClosed())	{
?>
		<span>The Registration System is Currently Closed</span>
<?php
	} else {
?>
		<a class="button-link" title="Student Registration System" href="registration/">Student Registration</a>
<?php 
	}
?>
	</div>
	<div class="oi ui-sys-front-content ui-sys-bg-grid-green">
		<div class="ui-sys-front-left-panel mobile-collapse">
			<div class="ui-sys-panel-container ui-sys-panel">
				<div class="ui-sys-panel-header"></div>
				<div class="ui-sys-panel-body">
<?php 
	$missionAndVision1 = $profile1->getMissionAndVisionList();
	$mission = "";
	$vision = "";
	if (! is_null($missionAndVision1))	{
		$missionAndVision1 = $missionAndVision1[0];
		$mission = $missionAndVision1->getElementsByTagName('mission')->item(0)->nodeValue;
		$vision = $missionAndVision1->getElementsByTagName('vision')->item(0)->nodeValue;
		//Substitute carriage return and line feed
		$mission = str_replace('\r\n', PHP_EOL, $mission);
		$mission = str_replace('\r', PHP_EOL, $mission);
		$mission = str_replace('\n', PHP_EOL, $mission);
		$vision = str_replace('\r\n', PHP_EOL, $vision);
		$vision = str_replace('\r', PHP_EOL, $vision);
		$vision = str_replace('\n', PHP_EOL, $vision);
	}
?>
<!--Begin Mission-->
<div class="ui-widget ui-widget-content">
	<div class="ui-widget-header">Mission Statement</div>
	<div><?= $mission ?></div>
	<div></div>
</div>
<!--End Mission-->
<br/><br/>
<!--Begin Vision-->
<div class="ui-widget ui-widget-content">
	<div class="ui-widget-header">Vision Statement</div>
	<div><?= $vision ?></div>
	<div></div>
</div>
<!--End Vision-->
				</div>
				<div class="ui-sys-panel-footer"></div>
			</div>
		</div>
		<div class="ui-sys-front-right-panel mobile-collapse">
			<div class="ui-sys-panel-container ui-sys-panel">
				<div class="ui-sys-panel-header ui-widget ui-widget-header">Login</div>
				<div class="ui-sys-panel-body">
					<form class="pure-form pure-form-aligned" id="form1" method="POST" action="">
						<fieldset>
							<div class="pure-control-group">
								<label for="username">Username</label>
								<input type="text" name="username" id="username" size="32" required pattern="<?= $exprL64Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprL64Name ?>" validate_message="Username: <?= $msgL64Name ?>"/>
							</div>
							<div class="pure-control-group">
								<label for="password">Password</label>
								<input type="password" name="password" id="password" size="32" required pattern="<?= $exprPassword ?>" validate="true" validate_control="text" validate_expression="<?= $exprPassword ?>" validate_message="Password: <?= $msgPassword ?>"/>
							</div>
							<div class="pure-controls">
								<span id="perror" class="ui-sys-error-message">
							
								</span>
							</div>
							<div class="pure-controls">
								&nbsp;&nbsp;&nbsp;&nbsp;<input class="button-login" id="btnSubmit" type="button" value="Login"/>
							</div>
						</fieldset>
					</form>
				</div>
				<div class="ui-sys-panel-footer">
					<a href="#">Can't Access Your Account?</a>
				</div>
			</div>
		</div>
		<div class="ui-sys-clearboth">&nbsp;</div>
	</div>
<script type="text/javascript">
			(function($)	{
				$('#btnSubmit').on('click', function(event)	{
					event.preventDefault();
					var targetElement = "perror";
					//Validation Should begun
					$target1 = $('#' + targetElement);
					$target1.empty();
					var form1 = document.getElementById('form1');
					if (! form1)	{
						$('<span/>').html("Form Reference Could not be found")
							.appendTo($target1);
						return false;
					}
					form1.action = location.href.replace(/^http:/, 'https');
					if (! generalFormValidation(this, 'form1', targetElement))	{
						return false;
					}
					//Ajax should do its job here
					//You can load gif image 
					$.ajax({
						url: "server/service_authenticate.php",
						method: "POST",
						data: { param1: $('#username').val(),
								param2: $('#password').val() },
						dataType: "json",
						cache: false,
						async: false
					}).done(function(data, textStatus, jqXHR)	{
						if (parseInt(data.code) === 0)	{
							//Successful 
							window.location.href = "profile/";
						} else	{
							//Failed 
							$('<span/>').text(data.message)
								.appendTo($target1);
							return;
						}
					}).fail(function(jqXHR, textStatus, errorThrown)	{
						$('<span/>').text(textStatus)
							.appendTo($target1);
					}).always(function(data, textStatus, jqXHR)	{
						console.log("Logging Operation Done");
					});
				});
			})(jQuery);
</script>
	<div class="ui-sys-footer mobile-collapse">
<?php   
	//You must have a DateAndTime Object carrying date, we are interested with only year 
	//So any default_timezone is okay with us at this point 
	include("template/footer.php");
?>
	</div>
</div>

</body>
</html>