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
$pagenumber = -1;
$prevpagenumber = 9999;
$thispage=$_SERVER['PHP_SELF'];
//Monitor if we had already done an Application
if (! (isset($_REQUEST['pagenumber']) && isset($_REQUEST['prevpagenumber'])) && (intval($profile1->getApplicationCounter()) > 0))	{
	//We need to jumpdirectly to a page 
	$jumpdirectly="Jump Directly";
}
if (isset($_REQUEST['pagenumber']))	{ $pagenumber=intval($_REQUEST['pagenumber']); }
if (isset($_REQUEST['prevpagenumber']))	{ $prevpagenumber=intval($_REQUEST['prevpagenumber']); }
?>
<html>
<head>
<title><?= $profile1->getProfileName() ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" type="text/css" media="all" href="../client/jquery-ui-1.11.3/themes/<?= $themeFolder ?>/jquery-ui.css"/>
<link rel="stylesheet" type="text/css" media="all" href="../client/css/purecss/pure-min.css"/>
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

<div class="ui-sys-main">
	<div class="ui-sys-front-header">
		<div class="ui-sys-front-logo mobile-collapse">
<?php 
	$logo = "../".$dataFolder."/".$profile1->getDataFolder()."/logo/default.jpg";
	if (! is_null($profile1->getLogo())) $logo = "../".$dataFolder."/".$profile1->getDataFolder()."/logo/".$profile1->getLogo();
?>
			<img alt="LG" src="<?= $logo ?>"/>
		</div>
		<div class="ui-sys-front-header-1 mobile-collapse">
			<div class="ui-sys-inst-name">INSTALLATION SETUP</div>
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
	
<!--BEGIN OF PUTTING CODE-->
<div class="ui-sys-bg-grid-green">
<?php 
	if (isset($jumpdirectly))	{
?>
		<div class="ui-sys-panel-container ui-widget">
			<div class="ui-sys-panel ui-widget-content">
				<div class="ui-sys-panel-header ui-widget-header">PARTIAL INSTALLATION</div>
				<div class="ui-sys-panel-body">
					The System has detected that you have incomplete Installation, kindly click the button below to proceed from where you ended
					<div class="ui-sys-inline-controls-center">
						<a class="button-link" href="<?= $thispage ?>?prevpagenumber=9999&pagenumber=<?= $profile1->getApplicationCounter() ?>">Proceed with Installation</a>
					</div>
				</div>
				<div class="ui-sys-panel-footer"></div>
			</div>
		</div>
		<div class="ui-sys-clear-both">&nbsp;</div>
<?php
	} else if (isset($prevpagenumber) && isset($pagenumber) && $pagenumber == 19)	{
		$storedSafePage = intval($profile1->getApplicationCounter());
		$enableUpdate = false;
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$profile1 = null;
		$login1 = null;
		try {
			$profile1 = new Profile($database, $__profileId, $conn);
			$profile1->loadXMLFolder("../data/profile");
			$login1 = new Login($database, Login::getStartUpLoginId($database, $conn), $conn);
		} catch (Exception $e)	{
			die($e->getMessage());
		}
		//Step 1: Handling Data From the Previous Page 
		if ($prevpagenumber < $pagenumber)	{
			
			//$telephone = mysql_real_escape_string($_REQUEST['telephoneList']);
			$systemName = mysql_real_escape_string($_REQUEST['systemName']);
			$extraInformation = mysql_real_escape_string($_REQUEST['extraInformation']);
			if ($pagenumber -1 < $storedSafePage)	{
				//UPDATE WINDOW
				if ($systemName != $profile1->getSystemName())	{
					$profile1->setSystemName($systemName); $enableUpdate = true;
				}
				if ($extraInformation != $profile1->getExtraInformation())	{
					$profile1->setExtraInformation($extraInformation); $enableUpdate = true;
				}
			} else {
				//INSERT WINDOW
				$profile1->setSystemName($systemName);
				$profile1->setExtraInformation($extraInformation);
				$profile1->setApplicationCounter($pagenumber);
				$enableUpdate = true;
			}
			if ($enableUpdate)	{
				try {
					$profile1->commitUpdate();
				} catch (Exception $e)	{ die($e->getMessage()); }
			}
		}
		//Step 2: Handling Data for this Page 
		//$website = "";
		if ($pagenumber < $storedSafePage)	{
			//$website = $profile1->getWebsite()
		}
		//Beginning UI 
		//Now Everything is set it is up to us to do the final settings of the Profile 
		$profile1->setInstallationComplete("1");
		$login1->setRoot("1");
		$login1->setAdmissionTime($systemDate1->getDateAndTimeString());
		$login1->setAdmitted("1");
		try {
			$login1->setUserStatus(UserStatus::getUserStatusIdFromCode($database, $conn, UserStatus::$__ALIVE));
			$login1->setUserType(UserType::getUserTypeIdFromCode($database, $conn, UserType::$__USER));
			$profile1->commitUpdate();
			$login1->commitUpdate();
		} catch (Exception $e)	{ die($e->getMessage()); }
?>
		<div class="ui-sys-panel-container mobile-collapse">
			<div class="ui-sys-panel ui-sys-data-capture">
				<div class="ui-sys-panel-header"></div>
				<div class="ui-sys-panel-body ui-widget">
					<div class="ui-widget-content">
						<div class="ui-widget-header">Installation Report</div>
						You have successful Installed the System <br />
						Click <a href="../" class="button-link">Here</a> to proceed to the Home Page
					</div>
				</div>
				<div class="ui-sys-panel-footer"></div>
			</div>
		</div>
		<div class="ui-sys-clear-both"></div>
<?php
		mysql_close($conn);
	} else if (isset($prevpagenumber) && isset($pagenumber) && $pagenumber == 18)	{
		$storedSafePage = intval($profile1->getApplicationCounter());
		$enableUpdate = false;
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$profile1 = null;
		$login1 = null;
		try {
			$profile1 = new Profile($database, $__profileId, $conn);
			$profile1->loadXMLFolder("../data/profile");
			$login1 = new Login($database, Login::getStartUpLoginId($database, $conn), $conn);
		} catch (Exception $e)	{
			die($e->getMessage());
		}
		//Step 1: Handling Data From the Previous Page 
		if ($prevpagenumber < $pagenumber)	{
			//$telephone = mysql_real_escape_string($_REQUEST['telephoneList']);
			$loginName = mysql_real_escape_string($_REQUEST['loginName']);
			$email = mysql_real_escape_string($_REQUEST['email']);
			$password = sha1($_REQUEST['password']);
			$firstSecurityQuestionId = mysql_real_escape_string($_REQUEST['firstSecurityQuestionId']);
			$firstSecurityAnswer = mysql_real_escape_string($_REQUEST['firstSecurityAnswer']);
			$secondSecurityQuestionId = mysql_real_escape_string($_REQUEST['secondSecurityQuestionId']);
			$secondSecurityAnswer = mysql_real_escape_string($_REQUEST['secondSecurityAnswer']);
			if ($pagenumber -1 < $storedSafePage)	{
				//UPDATE WINDOW
				if ($loginName != $login1->getLoginName())	{
					$login1->setLoginName($loginName); $enableUpdate = true;
				}
				if ($email != $login1->getEmail())	{
					$login1->setEmail($email); $enableUpdate = true;
				}
				if ($password != $login1->getPassword())	{
					$login1->setPassword($password); $enableUpdate = true;
				}
				if (is_null($login1->getFirstSecurityQuestion()) || ($firstSecurityQuestionId != $login1->getFirstSecurityQuestion()->getQuestionId()))	{
					$login1->setFirstSecurityQuestion($firstSecurityQuestionId); $enableUpdate = true;
				}
				if ($firstSecurityAnswer != $login1->getFirstSecurityAnswer())	{
					$login1->setFirstSecurityAnswer($firstSecurityAnswer); $enableUpdate = true;
				}
				if (is_null($login1->getSecondSecurityQuestion()) || ($secondSecurityQuestionId != $login1->getSecondSecurityQuestion()->getQuestionId()))	{
					$login1->setSecondSecurityQuestion($secondSecurityQuestionId); $enableUpdate = true;
				}
				if ($secondSecurityAnswer != $login1->getSecondSecurityAnswer())	{
					$login1->setSecondSecurityAnswer($secondSecurityAnswer); $enableUpdate = true;
				}
			} else {
				//INSERT WINDOW
				$login1->setLoginName($loginName);
				$login1->setEmail($email);
				$login1->setPassword($password);
				$login1->setFirstSecurityQuestion($firstSecurityQuestionId);
				$login1->setFirstSecurityAnswer($firstSecurityAnswer);
				$login1->setSecondSecurityQuestion($secondSecurityQuestionId);
				$login1->setSecondSecurityAnswer($secondSecurityAnswer);
				$profile1->setApplicationCounter($pagenumber);
				$enableUpdate = true;
			}
			if ($enableUpdate)	{
				//This will do a blindly update on profile 
				//However the profile Object will check for this situation 
				try {
					$login1->commitUpdate();
					$profile1->commitUpdate(); //Blindly update 
				} catch (Exception $e)	{
					die($e->getMessage());
				}
			}
		}
		//Step 2: Handling Data for this Page 
		//$website = "";
		$systemName = "";
		$extraInformation = "";
		if ($pagenumber < $storedSafePage)	{
			//$website = $profile1->getWebsite()
			$systemName = $profile1->getSystemName();
			$extraInformation = $profile1->getExtraInformation();
		}
		//Beginning UI 
?>
		<div class="ui-sys-panel-container mobile-collapse">
			<div class="ui-sys-panel ui-sys-data-capture">
				<div class="ui-sys-panel-header">Extras</div>
				<div class="ui-sys-panel-body">
					<div>
						<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $_SERVER['PHP_SELF'] ?>">
							<input type="hidden" name="prevpagenumber" value="<?= $pagenumber ?>"/>
							<input type="hidden" name="pagenumber" value="<?= $pagenumber + 1 ?>"/>
							<fieldset>
								<div class="pure-control-group">
									<label for="systemName">System Name </label>
									<input type="text" name="systemName" id="systemName" size="32" value="<?= $systemName ?>" required pattern="<?= $exprA108Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprA108Name ?>" validate_message="System Name : <?= $msgA108Name ?>"/>
								</div>
								<div class="pure-control-group">
									<label for="extraInformation">Extra Information </label>
									<input type="text" name="extraInformation" id="extraInformation" size="32" value="<?= $extraInformation ?>" required pattern="<?= $exprL64Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprL64Name ?>" validate_message="Extra Information : <?= $msgL64Name ?>"/>
								</div>
								<div class="pure-controls">
									<span id="perror" class="ui-sys-error-message"></span>
								</div>
								<div class="pure-controls">
									<span>
										<a class="button-link" href="<?= $thispage ?>?prevpagenumber=<?= $pagenumber ?>&pagenumber=<?= $pagenumber - 1 ?>">&lt;&lt; Previous</a>&nbsp;&nbsp;
								<input id="cmd1" type="button" value="Next &gt;&gt;"/>
									</span>
								</div>
							</fieldset>
						</form>
					</div>
				</div>
				<div class="ui-sys-panel-footer"></div>
			</div>
		</div>
		<div class="ui-sys-clear-both"></div>
<script type="text/javascript">
(function($)	{
	$('#cmd1').on('click', function(event)	{
		event.preventDefault();
		generalFormSubmission(this, 'form1', 'perror');
	});
})(jQuery);
</script>
<?php
		mysql_close($conn);
	} else if (isset($prevpagenumber) && isset($pagenumber) && $pagenumber == 17)	{
		$storedSafePage = intval($profile1->getApplicationCounter()); 
		$enableUpdate = false;
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$profile1 = null;
		$promise1 = null;
		$login1 = null;
		try {
			$profile1 = new Profile($database, $__profileId, $conn);
			$profile1->loadXMLFolder("../data/profile");
			$login1 = new Login($database, Login::getStartUpLoginId($database, $conn), $conn);
		} catch (Exception $e)	{
			die($e->getMessage());
		}
		//Step 1: Handling Data From the Previous Page 
		if ($prevpagenumber < $pagenumber)	{
			//$telephone = mysql_real_escape_string($_REQUEST['telephoneList']);
			$logoArray1 = $_FILES['logo'];
			$enablePhotoUpdate = false;
			if ($pagenumber - 1 < $storedSafePage)	{
				if (Object::isThereAnyFileReceivedFromTheClient($logoArray1))	{
					$enablePhotoUpdate = true;
				}
			} else {
				if (Object::isThereAnyFileReceivedFromTheClient($logoArray1))	{
					$enablePhotoUpdate = true;
				}
				$enableUpdate = true;
				$profile1->setApplicationCounter($pagenumber);
			}
			$logoFolder = "../data/users/photo";
			$logofilename = "user_".$login1->getLoginId();
			$fileextension = Object::getUploadedFileExtension($logoArray1);
			$fileextension = strtolower($fileextension);
			$uploadedFileName = $logofilename.".".$fileextension;
			if ($enablePhotoUpdate)	{
				//We will just overwrite 
				$validTypes = array("image/jpeg", "image/png", "image/jpg");
				$validExtensions = array("jpeg", "png", "jpg");
				$maximumUploadedSize = 2097153;
				$promise1 = Object::saveUploadedFile($logoArray1, $logoFolder, $logofilename, $validTypes, $validExtensions, $maximumUploadedSize);
			}
			if (! is_null($promise1) && $promise1->isPromising())	{
				$login1->setPhoto($uploadedFileName);
				$enableUpdate = true;
			}
			if ($enableUpdate)	{
				try {
					$login1->commitUpdate();
					$profile1->commitUpdate();
				} catch (Exception $e)	{ die($e->getMessage()); }
			}
		}
		//Step 2: Handling Data for this Page 
		//$website = "";
		$loginName = "";
		$email = "";
		//Password Display NONE 
		$firstSecurityQuestionId = -1;
		$firstSecurityAnswer = "";
		$secondSecurityQuestionId = -1;
		$secondSecurityAnswer = "";
		if ($pagenumber < $storedSafePage)	{
			//$website = $profile1->getWebsite()
			$loginName = $login1->getLoginName();
			$email = $login1->getEmail();
			$firstSecurityQuestionId = $login1->getFirstSecurityQuestion()->getQuestionId();
			$firstSecurityAnswer = $login1->getFirstSecurityAnswer();
			$secondSecurityQuestionId = $login1->getSecondSecurityQuestion()->getQuestionId();
			$secondSecurityAnswer = $login1->getSecondSecurityAnswer();
		}
		//Beginning UI 
?>
		<div class="ui-sys-panel-container mobile-collapse">
			<div class="ui-sys-panel ui-sys-data-capture">
				<div class="ui-sys-panel-header">Security</div>
				<div class="ui-sys-panel-body">
					<div>
<?php 
	if (is_null($promise1) || $promise1->isPromising())	{
?>
						<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $_SERVER['PHP_SELF'] ?>">
							<input type="hidden" name="prevpagenumber" value="<?= $pagenumber ?>"/>
							<input type="hidden" name="pagenumber" value="<?= $pagenumber + 1 ?>"/>
							<fieldset>
								<div class="pure-control-group">
									<label for="loginName">Administrator Username </label>
									<input type="text" name="loginName" id="loginName" size="32" value="<?= $loginName ?>" required pattern="<?= $expr32Name ?>" validate="true" validate_control="text" validate_expression="<?= $expr32Name ?>" validate_message="Username : <?= $msg32Name ?>"/>
								</div>
								<div class="pure-control-group">
									<label for="email">Administrator Email </label>
									<input type="text" name="email" id="email" size="32" value="<?= $email ?>" required pattern="<?= $exprEmail ?>" validate="true" validate_control="text" validate_expression="<?= $exprEmail ?>%<?= $exprL32Name ?>" validate_message="Email Format : <?= $msgEmail ?>%Email Length: <?= $msgL32Name ?>"/>
								</div>
								<div class="pure-control-group">
										<label for="password">Administrator's Password </label>
										<input type="password" name="password" id="password" size="32" required pattern="<?= $exprL48Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprL48Name ?>" validate_message="Password : <?= $msgL48Name ?>"/>
								</div>
								<div class="pure-control-group">
										<label for="password">Confirm Password </label>
										<input type="password" id="cpassword" size="32" required pattern="<?= $exprL48Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprL48Name ?>" validate_message="Password(2) : <?= $msgL48Name ?>"/>
								</div>
								<div class="pure-control-group">
									<label for="firstSecurityQuestionId">First Security Question </label>
									<select id="firstSecurityQuestionId" name="firstSecurityQuestionId" validate="true" validate_control="select" validate_expression="select" validate_message="Kindly Select First Security Question">
											<option value="_@32767@_">--select--</option>
<?php 
	$list1 = SecurityQuestion::loadAllData($database, $conn);
	foreach ($list1 as $alist1)	{
		$selected = "";
		if (! is_null($login1->getFirstSecurityQuestion()) && ($alist1['id'] == $login1->getFirstSecurityQuestion()->getQuestionId())) $selected="selected";
?>
			<option <?= $selected ?> value="<?= $alist1['id'] ?>"><?= $alist1['val'] ?></option>
<?php
	}
?>
										</select>
								</div>
								<div class="pure-control-group">
										<label for="firstSecurityAnswer">First Security Answer </label>
										<input type="text" name="firstSecurityAnswer" id="firstSecurityAnswer" size="32" value="<?= $firstSecurityAnswer ?>" required pattern="<?= $exprL32Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprL32Name ?>" validate_message="First Security Answer : <?= $msgL32Name ?>"/>
								</div>
								<div class="pure-control-group">
										<label for="secondSecurityQuestionId">Second Security Question </label>
										<select id="secondSecurityQuestionId" name="secondSecurityQuestionId" validate="true" validate_control="select" validate_expression="select" validate_message="Kindly Select Second Security Question">
											<option value="_@32767@_">--select--</option>
<?php 
	$list1 = SecurityQuestion::loadAllData($database, $conn);
	foreach ($list1 as $alist1)	{
		$selected = "";
		if (! is_null($login1->getSecondSecurityQuestion()) && ($alist1['id'] == $login1->getSecondSecurityQuestion()->getQuestionId())) $selected="selected";
?>
			<option <?= $selected ?> value="<?= $alist1['id'] ?>"><?= $alist1['val'] ?></option>
<?php
	}
?>
										</select>
								</div>
								<div class="pure-control-group">
									<label for="secondSecurityAnswer">Second Security Answer </label>
									<input type="text" name="secondSecurityAnswer" id="secondSecurityAnswer" size="32" value="<?= $secondSecurityAnswer ?>" required pattern="<?= $exprL32Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprL32Name ?>" validate_message="Second Security Answer : <?= $msgL32Name ?>"/>
								</div>
								<div class="pure-controls">
										<span id="perror" class="ui-sys-error-message"></span>
								</div>
								<div class="pure-controls">
									<span>
										<a class="button-link" href="<?= $thispage ?>?prevpagenumber=<?= $pagenumber ?>&pagenumber=<?= $pagenumber - 1 ?>">&lt;&lt; Previous</a>&nbsp;&nbsp;
								<input id="cmd1" type="button" value="Next &gt;&gt;"/>
									</span>
								</div>
							</fieldset>
						</form>
<?php 
	} else {
?>
		There were problems in Uploading the photo 
		<div class="ui-sys-controls-right">
			<a class="button-link" href="<?= $thispage ?>?prevpagenumber=<?= $pagenumber ?>&pagenumber=<?= $pagenumber - 1 ?>">&lt;&lt; Previous</a>
		</div>
<?php
	}
?>
					</div>
				</div>
				<div class="ui-sys-panel-footer"></div>
			</div>
		</div>
		<div class="ui-sys-clear-both"></div>
<script type="text/javascript">
(function($)	{
	$('#cmd1').on('click', function(event)	{
		event.preventDefault();
		if ($('#password').val() == $('#cpassword').val())	{
			generalFormSubmission(this, 'form1', 'perror');
		} else {
			$('#perror').html('Password Mismatch');
		}
	});
})(jQuery);
</script>
<?php
		mysql_close($conn);
	}  else if (isset($prevpagenumber) && isset($pagenumber) && $pagenumber == 16)	{
		$storedSafePage = intval($profile1->getApplicationCounter());
		$enableUpdate = false;
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$profile1 = null;
		$login1 = null;
		try {
			$profile1 = new Profile($database, $__profileId, $conn);
			$profile1->loadXMLFolder("../data/profile");
			$login1 = new Login($database, Login::getStartUpLoginId($database, $conn), $conn);
		} catch (Exception $e)	{
			die($e->getMessage());
		}
		//Step 1: Handling Data From the Previous Page 
		if ($prevpagenumber < $pagenumber)	{
			//$telephone = mysql_real_escape_string($_REQUEST['telephoneList']);
			$jobId = mysql_real_escape_string($_REQUEST['jobId']);
			$groupId = mysql_real_escape_string($_REQUEST['groupId']);
			if ($pagenumber -1 < $storedSafePage)	{
				//UPDATE WINDOW
				if (is_null($login1->getJobTitle()) || ($login1->getJobTitle()->getJobId() != $jobId))	{
					$login1->setJobTitle($jobId); $enableUpdate = true;
				}
				if (is_null($login1->getGroup()) || ($login1->getGroup()->getGroupId() != $groupId))	{
					$login1->setGroup($groupId); $enableUpdate = true;
				}
			} else {
				//INSERT WINDOW
				$login1->setJobTitle($jobId);
				$login1->setGroup($groupId);
				$profile1->setApplicationCounter($pagenumber);
				$enableUpdate = true;
			}
			if ($enableUpdate)	{
				try {
					$login1->commitUpdate();
					$profile1->commitUpdate();
				} catch (Exception $e)	{ die($e->getMessage()); }
			}
		}
		//Step 2: Handling Data for this Page 
		//$website = "";
		$photo = "../".$dataFolder."/users/photo/default.png";
		$trackchange = 0;
		if ($pagenumber < $storedSafePage)	{
			//$website = $profile1->getWebsite()
			$photo = "../".$dataFolder."/users/photo/".$login1->getPhoto();
			$trackchange = 1;
		}
		//Beginning UI 
?>
		<div class="ui-sys-panel-container mobile-collapse">
			<div class="ui-sys-panel ui-sys-data-capture">
				<div class="ui-sys-panel-header">Administrator's Photo</div>
				<div class="ui-sys-panel-body">
<!--Beginning a photo container-->	
<div class="photocontainer">
	<div class="photodisplay">
		<img id="__id_image_photo_container" alt="PIC" src="<?= $photo ?>"/>
	</div>
	<div class="photodata">
		<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $_SERVER['PHP_SELF'] ?>" enctype="multipart/form-data">
			<input type="hidden" name="prevpagenumber" value="<?= $pagenumber ?>"/>
			<input type="hidden" name="pagenumber" value="<?= $pagenumber + 1 ?>"/>
			<fieldset>
				<div class="pure-control-group">
					<label for="__id_image_photo_control">Select Photo </label>
					<input  id="__id_image_photo_control" type="file" class="ui-sys-file-upload" name="logo" data-trackchange="<?= $trackchange ?>" value="<?= $photoname ?>" accept="image/*"/>
				</div>
				<div class="pure-controls">
					<span id="perror" class="ui-sys-inline-controls-center ui-sys-error-message"></span>
				</div>
				<div class="pure-controls">
					<span> 
					<a class="button-link" href="<?= $thispage ?>?prevpagenumber=<?= $pagenumber ?>&pagenumber=<?= $pagenumber - 1 ?>">&lt;&lt; Previous</a>
					<input type="submit" value="Upload"/></span>
				</div>
			</fieldset>
		</form>
	</div>
	<div class="ui-sys-clear-both">&nbsp;</div>
</div>
<!--Ending a photo container-->
				</div>
				<div class="ui-sys-panel-footer"></div>
			</div>
		</div>
		<div class="ui-sys-clear-both"></div>
 <script type="text/javascript">
					function showImage(_fileSource, _imageDestination)	{
						var fileSource1 = document.getElementById(_fileSource);
						if (! fileSource1) return;
						var imageDestination1 = document.getElementById(_imageDestination);
						if (! imageDestination1) return;
						var fileReader1 = new FileReader();
						$(fileReader1).on('load', function(event)	{
							imageDestination1.src = this.result;
						});
						$(fileSource1).on('change', function(event)	{
							fileReader1.readAsDataURL(fileSource1.files[0]);
						});
					}
					showImage('__id_image_photo_control', '__id_image_photo_container');
					(function($)	{
						$('#__id_image_photo_control').on('change', function(event)	{
							$('#__id_image_photo_control').data('trackchange', '1');
							showImage('__id_image_photo_control', '__id_image_photo_container');
						});
						$('#form1').on('submit', function(event)	{
							event.preventDefault();
							//Make use of track changes to validate further
							var fileControl1 = document.getElementById('__id_image_photo_control');
							if (! fileControl1) return;
							if (parseInt($(fileControl1).data('trackchange')) == 1)	{
								generalFormSubmission(this, 'form1', 'perror');
							} else {
								$('#perror').html("No Update were done");
							} //end-if-else
						});
					})(jQuery);
</script>
<?php
		mysql_close($conn);
	} else if (isset($prevpagenumber) && isset($pagenumber) && $pagenumber == 15)	{
		$storedSafePage = intval($profile1->getApplicationCounter());
		$enableUpdate = false;
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$profile1 = null;
		$login1 = null;
		try {
			$profile1 = new Profile($database, $__profileId, $conn);
			$profile1->loadXMLFolder("../data/profile");
			$login1 = new Login($database, Login::getStartUpLoginId($database, $conn), $conn);
		} catch (Exception $e)	{
			die($e->getMessage());
		}
		//Step 1: Handling Data From the Previous Page 
		if ($prevpagenumber < $pagenumber)	{
			//$telephone = mysql_real_escape_string($_REQUEST['telephoneList']);
			$dob = mysql_real_escape_string($_REQUEST['dob']);
			$dob = DateAndTime::convertFromGUIDateFormatToSystemDateAndTimeFormat($dob);
			$sexId = mysql_real_escape_string($_REQUEST['sexId']);
			$maritalId = mysql_real_escape_string($_REQUEST['maritalId']);
			if ($pagenumber -1 < $storedSafePage)	{
				//UPDATE WINDOW
				if (is_null($login1->getDOB()) || ($dob != $login1->getDOB()->getDateAndTimeString()))	{
					$login1->setDOB($dob); $enableUpdate = true;
				}
				if (is_null($login1->getSex()) || ($sexId != $login1->getSex()->getSexId()))	{
					$login1->setSex($sexId); $enableUpdate = true;
				}
				if (is_null($login1->getMarital()) || ($maritalId != $login1->getMarital()->getMaritalId()))	{
					$login1->setMarital($maritalId); $enableUpdate = true;
				}
			} else {
				//INSERT WINDOW
				$login1->setDOB($dob);
				$login1->setSex($sexId);
				$login1->setMarital($maritalId);
				$profile1->setApplicationCounter($pagenumber);
				$enableUpdate = true;
			}
			if ($enableUpdate)	{
				try {
					$login1->commitUpdate();
					$profile1->commitUpdate();
				} catch (Exception $e)	{ die($e->getMessage()); }
			}
		}
		//Step 2: Handling Data for this Page 
		//$website = "";
		$jobId = -1;
		$groupId = -1;
		if ($pagenumber < $storedSafePage)	{
			//$website = $profile1->getWebsite()
			$jobId = $login1->getJobTitle()->getJobId();
			$groupId = $login1->getGroup()->getGroupId();
		}
		//Beginning UI 
?>
		<div class="ui-sys-panel-container mobile-collapse">
			<div class="ui-sys-panel ui-sys-data-capture">
				<div class="ui-sys-panel-header">Administrator's Group & JobTitles</div>
				<div class="ui-sys-panel-body">
					<div>
						<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $_SERVER['PHP_SELF'] ?>">
							<input type="hidden" name="prevpagenumber" value="<?= $pagenumber ?>"/>
							<input type="hidden" name="pagenumber" value="<?= $pagenumber + 1 ?>"/>
							<fieldset>
								<div class="pure-control-group">
									<label for="jobId">Administrator's Job Title </label>
									<select id="jobId" name="jobId" validate="true" validate_control="select" validate_expression="select" validate_message="Kindly Select Job Title">
										<option value="_@32767@_">--select--</option>
<?php 
	$list1 = JobTitle::loadAllData($database, $conn);
	foreach ($list1 as $alist1)	{
		$selected = "";
		if (! is_null($login1->getJobTitle()) && ($alist1['id'] == $login1->getJobTitle()->getJobId())) $selected="selected";
?>
			<option <?= $selected ?> value="<?= $alist1['id'] ?>"><?= $alist1['val'] ?></option>
<?php
	}
?>
									</select>
								</div>
								<div class="pure-control-group">
									<label for="groupId">Administrator's Group </label>
									<select id="groupId" name="groupId" validate="true" validate_control="select" validate_expression="select" validate_message="Kindly Select Group">
										<option value="_@32767@_">--select--</option>
<?php 
	$list1 = Group::loadAllData($database, $conn);
	foreach ($list1 as $alist1)	{
		$selected = "";
		if (! is_null($login1->getGroup()) && ($alist1['id'] == $login1->getGroup()->getGroupId())) $selected="selected";
?>
			<option <?= $selected ?> value="<?= $alist1['id'] ?>"><?= $alist1['val'] ?></option>
<?php
	}
?>
									</select>
								</div>
								<div class="pure-controls">
									<span id="perror" class="ui-sys-error-message"></span>
								</div>
								<div class="pure-controls">
									<span>
										<a class="button-link" href="<?= $thispage ?>?prevpagenumber=<?= $pagenumber ?>&pagenumber=<?= $pagenumber - 1 ?>">&lt;&lt; Previous</a>&nbsp;&nbsp;
								<input id="cmd1" type="button" value="Next &gt;&gt;"/>
									</span>
								</div>
							</fieldset>
						</form>
					</div>
				</div>
				<div class="ui-sys-panel-footer"></div>
			</div>
		</div>
		<div class="ui-sys-clear-both"></div>
<script type="text/javascript">
(function($)	{
	$('#cmd1').on('click', function(event)	{
		event.preventDefault();
		generalFormSubmission(this, 'form1', 'perror');
	});
})(jQuery);
</script>
<?php
		mysql_close($conn);
	} else if (isset($prevpagenumber) && isset($pagenumber) && $pagenumber == 14)	{
		$storedSafePage = intval($profile1->getApplicationCounter());
		$enableUpdate = false;
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$profile1 = null;
		$login1 = null;
		try {
			$profile1 = new Profile($database, $__profileId, $conn);
			$profile1->loadXMLFolder("../data/profile");
			$login1 = new Login($database, Login::getStartUpLoginId($database, $conn), $conn);
		} catch (Exception $e)	{
			die($e->getMessage());
		}
		//Step 1: Handling Data From the Previous Page 
		if ($prevpagenumber < $pagenumber)	{
			//$telephone = mysql_real_escape_string($_REQUEST['telephoneList']);
			$firstname = mysql_real_escape_string($_REQUEST['firstname']);
			$middlename = mysql_real_escape_string($_REQUEST['middlename']);
			$lastname = mysql_real_escape_string($_REQUEST['lastname']);
			$fullname = mysql_real_escape_string($_REQUEST['fullname']);
			if ($pagenumber -1 < $storedSafePage)	{
				//UPDATE WINDOW
				if ($firstname != $login1->getFirstname())	{
					$login1->setFirstname($firstname); $enableUpdate = true;
				}
				if ($middlename != $login1->getMiddlename())	{
					$login1->setMiddlename($middlename); $enableUpdate = true;
				}
				if ($lastname != $login1->getLastname())	{
					$login1->setLastname($lastname); $enableUpdate = true;
				}
				if ($fullname != $login1->getFullname())	{
					$login1->setFullname($fullname); $enableUpdate = true;
				}
			} else {
				//INSERT WINDOW
				$login1->setFirstname($firstname);
				$login1->setMiddlename($middlename);
				$login1->setLastname($lastname);
				$login1->setFullname($fullname);
				$profile1->setApplicationCounter($pagenumber);
				$enableUpdate = true;
			}
			if ($enableUpdate)	{
				/*
				There are possibilities that the profile were not touched 
				That is not the case , because every object is checking internally if 
				there exists update list prior to actual commiting update 
				*/
				try {
					$login1->commitUpdate();
					$profile1->commitUpdate();
				} catch (Exception $e)	{ die($e->getMessage()); }
			}
		}
		//Step 2: Handling Data for this Page 
		//$website = "";
		$dob = "";
		$sexId = -1;
		$maritalId = -1;
		if ($pagenumber < $storedSafePage)	{
			//$website = $profile1->getWebsite()
			$dob = DateAndTime::convertFromDateTimeObjectToGUIDateFormat($login1->getDOB());
			$sexId = $login1->getSex()->getSexId();
			$maritalId = $login1->getMarital()->getMaritalId();
		}
		//Beginning UI 
?>
		<div class="ui-sys-panel-container mobile-collapse">
			<div class="ui-sys-panel ui-sys-data-capture">
				<div class="ui-sys-panel-header">Administrato's Bio</div>
				<div class="ui-sys-panel-body">
					<div>
						<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $_SERVER['PHP_SELF'] ?>">
							<input type="hidden" name="prevpagenumber" value="<?= $pagenumber ?>"/>
							<input type="hidden" name="pagenumber" value="<?= $pagenumber + 1 ?>"/>
							<fieldset>
								<div class="pure-control-group">
									<label for="dob">Administrator's Date of Birth </label>
									<input class="datepicker" type="text" name="dob" id="dob" size="32" value="<?= $dob ?>" required pattern="<?= $exprDate ?>" validate="true" validate_control="text" validate_expression="<?= $exprDate ?>" validate_message="Date : <?= $msgDate ?>"/>
								</div>
								<div class="pure-control-group">
									<label for="sexId">Administrator's Sex </label>
									<select id="sexId" name="sexId" validate="true" validate_control="select" validate_expression="select" validate_message="Kindly Select Sex">
										<option value="_@32767@_">--select--</option>
<?php 
	$list1 = Sex::loadAllData($database, $conn);
	foreach ($list1 as $alist1)	{
		$selected = "";
		if (! is_null($login1->getSex()) && ($alist1['id'] == $login1->getSex()->getSexId())) $selected="selected";
?>
			<option <?= $selected ?> value="<?= $alist1['id'] ?>"><?= $alist1['val'] ?></option>
<?php
	}
?>
									</select>
								</div>
								<div class="pure-control-group">
									<label for="maritalId">Administrator's Marital </label>
									<select id="maritalId" name="maritalId" validate="true" validate_control="select" validate_expression="select" validate_message="Kindly Select Marital">
										<option value="_@32767@_">--select--</option>
<?php 
	$list1 = Marital::loadAllData($database, $conn);
	foreach ($list1 as $alist1)	{
		$selected = "";
		if (! is_null($login1->getMarital()) && ($alist1['id'] == $login1->getMarital()->getMaritalId())) $selected="selected";
?>
			<option <?= $selected ?> value="<?= $alist1['id'] ?>"><?= $alist1['val'] ?></option>
<?php
	}
?>
									</select>
								</div>
								<div class="pure-controls">
									<span id="perror" class="ui-sys-error-message"></span>
								</div>
								<div class="pure-controls">
									<span>
										<a class="button-link" href="<?= $thispage ?>?prevpagenumber=<?= $pagenumber ?>&pagenumber=<?= $pagenumber - 1 ?>">&lt;&lt; Previous</a>&nbsp;&nbsp;
								<input id="cmd1" type="button" value="Next &gt;&gt;"/>
									</span>
								</div>
							</fieldset>
						</form>
					</div>
				</div>
				<div class="ui-sys-panel-footer"></div>
			</div>
		</div>
		<div class="ui-sys-clear-both"></div>
<script type="text/javascript">
(function($)	{
	$('#cmd1').on('click', function(event)	{
		event.preventDefault();
		generalFormSubmission(this, 'form1', 'perror');
	});
})(jQuery);
</script>
<?php
		mysql_close($conn);
	} else if (isset($prevpagenumber) && isset($pagenumber) && $pagenumber == 13)	{
		$storedSafePage = intval($profile1->getApplicationCounter());
		$enableUpdate = false;
		//Step 1: Handling Data From the Previous Page 
		if ($prevpagenumber < $pagenumber)	{
			$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
			$profile1 = null;
			try {
				$profile1 = new Profile($database, $__profileId, $conn);
				$profile1->loadXMLFolder("../data/profile");
			} catch (Exception $e)	{
				die($e->getMessage());
			}
			//$telephone = mysql_real_escape_string($_REQUEST['telephoneList']);
			$themeId = mysql_real_escape_string($_REQUEST['themeId']);
			if ($pagenumber -1 < $storedSafePage)	{
				//UPDATE WINDOW
				if (is_null($profile1->getTheme()) || ($themeId != $profile1->getTheme()->getThemeId()))	{
					$profile1->setTheme($themeId); $enableUpdate = true;
				}
			} else {
				//INSERT WINDOW
				$profile1->setTheme($themeId);
				$profile1->setApplicationCounter($pagenumber);
				$enableUpdate = true;
			}
			if ($enableUpdate)	{
				try {
					$profile1->commitUpdate();
				} catch (Exception $e)	{ die($e->getMessage()); }
			}
			mysql_close($conn);
		}
		//Step 2: Handling Data for this Page 
		//$website = "";
		$firstname = "";
		$middlename = "";
		$lastname = "";
		$fullname = "";
		if ($pagenumber < $storedSafePage)	{
			//$website = $profile1->getWebsite()
			$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
			$login1 = null;
			try {
				$login1 = new Login($database, Login::getStartUpLoginId($database, $conn) ,$conn);
			} catch (Exception $e)	{ die($e->getMessage()); }
			mysql_close($conn);	
			$firstname = $login1->getFirstname();
			$middlename = $login1->getMiddlename();
			$lastname = $login1->getLastname();
			$fullname = $login1->getFullname();
		}
		//Beginning UI 
?>
		<div class="ui-sys-panel-container mobile-collapse">
			<div class="ui-sys-panel ui-sys-data-capture">
				<div class="ui-sys-panel-header">Administrator's Bio</div>
				<div class="ui-sys-panel-body">
					<div>
						<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $_SERVER['PHP_SELF'] ?>">
							<input type="hidden" name="prevpagenumber" value="<?= $pagenumber ?>"/>
							<input type="hidden" name="pagenumber" value="<?= $pagenumber + 1 ?>"/>
							<fieldset>
								<div class="pure-control-group">
									<td><label for="firstname">Administrator's Firstname </label>
									<td><input type="text" name="firstname" id="firstname" size="32" value="<?= $firstname ?>" required pattern="<?= $exprA32Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprA32Name ?>" validate_message="Firstname : <?= $msgA32Name ?>"/>
								</div>
								<div class="pure-control-group">
									<label for="middlename">Administrator's Middlename </label>
									<input type="text" name="middlename" id="middlename" size="32" value="<?= $middlename ?>" required pattern="<?= $exprA32Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprA32Name ?>" validate_message="Middlename : <?= $msgA32Name ?>"/>
								</div>
								<div class="pure-control-group">
									<label for="lastname">Administrator's Lastname </label>
									<input type="text" name="lastname" id="lastname" size="32" value="<?= $lastname ?>" required pattern="<?= $exprA32Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprA32Name ?>" validate_message="Lastname : <?= $msgA32Name ?>"/>
								</div>
								<div class="pure-control-group">
									<label for="fullname">Administrator's Fullname </label>
									<input type="text" name="fullname" id="fullname" size="32" value="<?= $fullname ?>" required pattern="<?= $exprA64Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprA64Name ?>" validate_message="Fullname : <?= $msgA64Name ?>"/>
								</div>
								<div class="pure-controls">
									<span id="perror" class="ui-sys-error-message"></span>
								</div>
								<div class="pure-controls">
									<span>
										<a class="button-link" href="<?= $thispage ?>?prevpagenumber=<?= $pagenumber ?>&pagenumber=<?= $pagenumber - 1 ?>">&lt;&lt; Previous</a>&nbsp;&nbsp;
								<input id="cmd1" type="button" value="Next &gt;&gt;"/>
									</span>
								</div>
							</fieldset>
						</form>
					</div>
				</div>
				<div class="ui-sys-panel-footer"></div>
			</div>
		</div>
		<div class="ui-sys-clear-both"></div>
<script type="text/javascript">
(function($)	{
	$('#cmd1').on('click', function(event)	{
		event.preventDefault();
		generalFormSubmission(this, 'form1', 'perror');
	});
})(jQuery);
</script>
<?php
	} else if (isset($prevpagenumber) && isset($pagenumber) && $pagenumber == 12)	{
		$storedSafePage = intval($profile1->getApplicationCounter());
		$enableUpdate = false;
		$promise1 = null;
		//Step 1: Handling Data From the Previous Page 
		if ($prevpagenumber < $pagenumber)	{
			$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
			$profile1 = null;
			try {
				$profile1 = new Profile($database, $__profileId, $conn);
				$profile1->loadXMLFolder("../data/profile");
			} catch (Exception $e)	{
				die($e->getMessage());
			}
			//$telephone = mysql_real_escape_string($_REQUEST['telephoneList']);
			$vision = mysql_real_escape_string($_REQUEST['vision']);
			$mission = mysql_real_escape_string($_REQUEST['mission']);
			$missionAndVision1 = array();
			$missionAndVision1['vision'] = $vision;
			$missionAndVision1['mission'] = $mission;
			if ($pagenumber -1 < $storedSafePage)	{
				//UPDATE WINDOW
			} else {
				//INSERT WINDOW
				$profile1->setApplicationCounter($pagenumber);
				$enableUpdate = true;
			}
			$promise1 = $profile1->backupXMLFile();
			$promise1 = $profile1->clearMissionAndVisions();
			$promise1 = $profile1->addMissionAndVision($missionAndVision1);
			if ($enableUpdate)	{
				try	{
					$profile1->commitUpdate();
				} catch (Exception $e)	{ die($e->getMessage()); }
			}
			mysql_close($conn);
		}
		//Step 2: Handling Data for this Page 
		//$website = "";
		$themeId = -1;
		if ($pagenumber < $storedSafePage)	{
			//$website = $profile1->getWebsite()
			$themeId = $profile1->getTheme()->getThemeId();
		}
		//Beginning UI 
?>
		<div class="ui-sys-panel-container mobile-collapse">
			<div class="ui-sys-panel ui-sys-data-capture">
				<div class="ui-sys-panel-header">Theme Settings</div>
				<div class="ui-sys-panel-body">
					<div>
						<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $_SERVER['PHP_SELF'] ?>">
							<input type="hidden" name="prevpagenumber" value="<?= $pagenumber ?>"/>
							<input type="hidden" name="pagenumber" value="<?= $pagenumber + 1 ?>"/>
							<fieldset>
								<div class="pure-control-group">
									<label for="themeId">Application Theme </label>
									<select id="themeId" name="themeId" validate="true" validate_control="select" validate_expression="select" validate_message="Kindly Select A Theme">
										<option value="_@32767@_">--select--</option>
<?php 
	$conn = mysql_connect($hostname, $user, $pass) or die("<option value='_@32767@_'>Q.failed</option>");
	$list1 = Theme::loadAllData($database, $conn);
	foreach ($list1 as $alist1)	{
		$selected = "";
		if (! is_null($profile1->getTheme()) && ($alist1['id'] == $profile1->getTheme()->getThemeId())) $selected = "selected";
?>
			<option <?= $selected ?> value="<?= $alist1['id'] ?>"><?= $alist1['val'] ?></option>
<?php
	}
	mysql_close($conn);
?>
									</select>
								</div>
								<div class="pure-controls">
									<span id="perror" class="ui-sys-error-message"></span>
								</div>
								<div class="pure-controls">
									<span>
										<a class="button-link" href="<?= $thispage ?>?prevpagenumber=<?= $pagenumber ?>&pagenumber=<?= $pagenumber - 1 ?>">&lt;&lt; Previous</a>&nbsp;&nbsp;
								<input id="cmd1" type="button" value="Next &gt;&gt;"/>
									</span>
								</div>
							</fieldset>
						</form>
					</div>
				</div>
				<div class="ui-sys-panel-footer"></div>
			</div>
		</div>
		<div class="ui-sys-clear-both"></div>
<script type="text/javascript">
(function($)	{
	$('#cmd1').on('click', function(event)	{
		event.preventDefault();
		generalFormSubmission(this, 'form1', 'perror');
	});
})(jQuery);
</script>
<?php
	}  else if (isset($prevpagenumber) && isset($pagenumber) && $pagenumber == 11)	{
		$storedSafePage = intval($profile1->getApplicationCounter());
		$promise1 = null;
		$enableUpdate = false;
		//Step 1: Handling Data From the Previous Page 
		if ($prevpagenumber < $pagenumber)	{
			$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
			$profile1 = null;
			try {
				$profile1 = new Profile($database, $__profileId, $conn);
				$profile1->loadXMLFolder("../data/profile");
			} catch (Exception $e)	{
				die($e->getMessage());
			}
			//$telephone = mysql_real_escape_string($_REQUEST['telephoneList']);
			$accountUsername = mysql_real_escape_string($_REQUEST['accountUsername']);
			$accountMobileNumber = mysql_real_escape_string($_REQUEST['accountMobileNumber']);
			$accountPassword = mysql_real_escape_string($_REQUEST['accountPassword']);
			$accountKey = mysql_real_escape_string($_REQUEST['accountKey']);
			$accountEncryptionKey = mysql_real_escape_string($_REQUEST['accountEncryptionKey']);
			$accountExtraData = mysql_real_escape_string($_REQUEST['accountExtraData']);
			//Just Do A blindly add 
			$smsAccount1 = array();
			$smsAccount1['accountUsername'] = $accountUsername;
			$smsAccount1['accountMobileNumber'] = $accountMobileNumber;
			$smsAccount1['accountPassword'] = $accountPassword;
			$smsAccount1['accountKey'] = $accountKey;
			$smsAccount1['accountEncryptionKey'] = $accountEncryptionKey;
			$smsAccount1['accountExtraData'] = $accountExtraData;
			if ($pagenumber -1 < $storedSafePage)	{
				//UPDATE WINDOW -- Do nothing 
			} else {
				//INSERT WINDOW
				$profile1->setApplicationCounter($pagenumber);
				$enableUpdate = true;
			}
			$promise1 = $profile1->backupXMLFile();
			$promise1 = $profile1->clearSMSAccounts();
			$promise1 = $profile1->addSMSAccount($smsAccount1);
			if ($enableUpdate)	{
				try {
					$profile1->commitUpdate();
				} catch (Exception $e)	{ die($e->getMessage()); }
			}
			mysql_close($conn);
		}
		//Step 2: Handling Data for this Page 
		//$website = "";
		$vision = "";
		$mission = "";
		if ($pagenumber < $storedSafePage)	{
			//$website = $profile1->getWebsite()
			$missionAndVision1 = $profile1->getMissionAndVisionList();
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
		}
		//Beginning UI 
?>
		<div class="ui-sys-panel-container mobile-collapse">
			<div class="ui-sys-panel ui-sys-data-capture">
				<div class="ui-sys-panel-header">Vision & Mission</div>
				<div class="ui-sys-panel-body">
					<div>
						<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $_SERVER['PHP_SELF'] ?>">
							<input type="hidden" name="prevpagenumber" value="<?= $pagenumber ?>"/>
							<input type="hidden" name="pagenumber" value="<?= $pagenumber + 1 ?>"/>
							<fieldset>
								<div class="pure-control-group">
									<label for="vision">Vision </label>
									<textarea name="vision" id="vision" rows="4" cols="80" required pattern="<?= $exprL512Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprL512Name ?>" validate_message="Vision: <?= $msgL512Name ?>"><?= $vision ?></textarea>
								</div>
								<div class="pure-control-group">
									<label for="mission">Mission </label>
									<textarea name="mission" id="mission" rows="4" cols="80" required pattern="<?= $exprL512Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprL512Name ?>" validate_message="Mission : <?= $msgL512Name ?>"><?= $mission ?></textarea>
								</div>
								<div class="pure-controls">
									<span id="perror" class="ui-sys-error-message"></span>
								</div>
								 <div class="pure-controls">
									<span>
										<a class="button-link" href="<?= $thispage ?>?prevpagenumber=<?= $pagenumber ?>&pagenumber=<?= $pagenumber - 1 ?>">&lt;&lt; Previous</a>&nbsp;&nbsp;
								<input id="cmd1" type="button" value="Next &gt;&gt;"/>
									</span>
								</div>
							</fieldset>
						</form>
					</div>
				</div>
				<div class="ui-sys-panel-footer"></div>
			</div>
		</div>
		<div class="ui-sys-clear-both"></div>
<script type="text/javascript">
(function($)	{
	$('#cmd1').on('click', function(event)	{
		event.preventDefault();
		generalFormSubmission(this, 'form1', 'perror');
	});
})(jQuery);
</script>
<?php
	} else if (isset($prevpagenumber) && isset($pagenumber) && $pagenumber == 10)	{
		$storedSafePage = intval($profile1->getApplicationCounter());
		$enableUpdate = false;
		$promise1 = null;
		//Step 1: Handling Data From the Previous Page 
		if ($prevpagenumber < $pagenumber)	{
			$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
			$profile1 = null;
			try {
				$profile1 = new Profile($database, $__profileId, $conn);
				$profile1->loadXMLFolder("../data/profile");
			} catch (Exception $e)	{
				die($e->getMessage());
			}
			//$telephone = mysql_real_escape_string($_REQUEST['telephoneList']);
			$accountName = mysql_real_escape_string($_REQUEST['accountName']);
			$bankAccount = mysql_real_escape_string($_REQUEST['bankAccount']);
			$bankName = mysql_real_escape_string($_REQUEST['bankName']);
			$branchName = mysql_real_escape_string($_REQUEST['branchName']);
			$bankCode = mysql_real_escape_string($_REQUEST['bankCode']);
			$swiftCode = mysql_real_escape_string($_REQUEST['swiftCode']);
			//Just Do A blindly add 
			$bankAccount1 = array();
			$bankAccount1['accountName'] = $accountName;
			$bankAccount1['bankAccount'] = $bankAccount;
			$bankAccount1['bankName'] = $bankName;
			$bankAccount1['branchName'] = $branchName;
			$bankAccount1['bankCode'] = $bankCode;
			$bankAccount1['swiftCode'] = $swiftCode;
			if ($pagenumber -1 < $storedSafePage)	{
				//UPDATE WINDOW -- do nothing
			} else {
				//INSERT WINDOW
				$profile1->setApplicationCounter($pagenumber);
				$enableUpdate = true;
			}
			$promise1 = $profile1->backupXMLFile();
			$promise1 = $profile1->clearBankAccounts();
			$promise1 = $profile1->addBankAccount($bankAccount1);
			if ($enableUpdate)	{
				try {
					$profile1->commitUpdate();
				} catch (Exception $e)	{ die($e->getMessage()); }
			}
			mysql_close($conn);
		}
		//Step 2: Handling Data for this Page 
		//$website = "";
		$accountUsername = "";
		$accountMobileNumber = "";
		$accountPassword = "";
		$accountPassword = "";
		$accountKey = "";
		$accountEncryptionKey = ""; //For encrypting passpord
		$accountExtraData = "";
		if ($pagenumber < $storedSafePage)	{
			//$website = $profile1->getWebsite()
			$smsAccount1 = $profile1->getSMSAccountList();
			if (! is_null($smsAccount1))	{
				$smsAccount1 = $smsAccount1[0];
				$accountUsername = $smsAccount1->getElementsByTagName('accountUsername')->item(0)->nodeValue;
				$accountMobileNumber = $smsAccount1->getElementsByTagName('accountMobileNumber')->item(0)->nodeValue;
				$accountPassword = $smsAccount1->getElementsByTagName('accountPassword')->item(0)->nodeValue;
				$accountKey = $smsAccount1->getElementsByTagName('accountKey')->item(0)->nodeValue;
				$accountEncryptionKey = $smsAccount1->getElementsByTagName('accountEncryptionKey')->item(0)->nodeValue;
				$accountExtraData = $smsAccount1->getElementsByTagName('accountExtraData')->item(0)->nodeValue;
			}
		}
		//Beginning UI 
?>
		<div class="ui-sys-panel-container mobile-collapse">
			<div class="ui-sys-panel ui-sys-data-capture">
				<div class="ui-sys-panel-header">SMS Account</div>
				<div class="ui-sys-panel-body">
					<div>
<?php 
	if (is_null($promise1) || $promise1->isPromising())	{
?>
						<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $_SERVER['PHP_SELF'] ?>">
							<input type="hidden" name="prevpagenumber" value="<?= $pagenumber ?>"/>
							<input type="hidden" name="pagenumber" value="<?= $pagenumber + 1 ?>"/>
							<fieldset>
								<div class="pure-control-group">
									<label for="accountUsername">Account Username </label>
									<input type="text" name="accountUsername" id="accountUsername" size="32" value="<?= $accountUsername ?>" required pattern="<?= $exprA48Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprA48Name ?>" validate_message="Account Username : <?= $msgA48Name ?>"/>
								</div>
								<div class="pure-control-group">
									<label for="accountMobileNumber">Account Mobile Number </label>
									<input type="text" name="accountMobileNumber" id="accountMobileNumber" size="32" value="<?= $accountMobileNumber ?>" required pattern="<?= $exprPhone ?>" validate="true" validate_control="text" validate_expression="<?= $exprPhone ?>" validate_message="Account Mobile Number : <?= $msgPhone ?>"/>
								</div>
								<div class="pure-control-group">
									<label for="accountPassword">Account Password </label>
									<input type="password" name="accountPassword" id="accountPassword" size="32" value="<?= $accountPassword ?>" required pattern="<?= $exprL48Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprL48Name ?>" validate_message="Account Password : <?= $msgL48Name ?>"/>
								</div>
								<div class="pure-control-group">
									<label for="accountKey">Account Key </label>
									<input type="text" name="accountKey" id="accountKey" size="32" value="<?= $accountKey ?>" required pattern="<?= $exprL48Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprL48Name ?>" validate_message="Account Key : <?= $msgL48Name ?>"/>
								</div>
								<div class="pure-control-group">
									<label for="accountEncryptionKey">Encryption Key </label>
									<input type="text" name="accountEncryptionKey" id="accountEncryptionKey" size="32" value="<?= $accountEncryptionKey ?>" required pattern="<?= $expr8Number ?>" validate="true" validate_control="text" validate_expression="<?= $expr8Number ?>" validate_message="Encryption Key : <?= $msg8Number ?>"/>
								</div>
								<div class="pure-control-group">
									<label for="accountExtraData">Extra Data </label>
									<input type="text" name="accountExtraData" id="accountExtraData" size="32" value="<?= $accountExtraData ?>" notrequired="true" pattern="<?= $exprL48Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprL48Name ?>" validate_message="Extra Data : <?= $msgL48Name ?>"/>
								</div>
								<div class="pure-controls">
									<span id="perror" class="ui-sys-error-message"></span>
								</div>
								<div class="pure-controls">
									<span>
										<a class="button-link" href="<?= $thispage ?>?prevpagenumber=<?= $pagenumber ?>&pagenumber=<?= $pagenumber - 1 ?>">&lt;&lt; Previous</a>&nbsp;&nbsp;
								<input id="cmd1" type="button" value="Next &gt;&gt;"/>
									</span>
								</div>
							</fieldset>
						</form>
<?php 
	} else {
?>
		There were problems with saving the Bank Account Details 
		<div class="ui-sys-inline-controls-right">
			<a class="button-link" href="<?= $thispage ?>?prevpagenumber=<?= $pagenumber ?>&pagenumber=<?= $pagenumber - 1 ?>">&lt;&lt; Previous</a>
		</div>
<?php
	}
?>
					</div>
				</div>
				<div class="ui-sys-panel-footer"></div>
			</div>
		</div>
		<div class="ui-sys-clear-both"></div>
<script type="text/javascript">
(function($)	{
	$('#cmd1').on('click', function(event)	{
		event.preventDefault();
		generalFormSubmission(this, 'form1', 'perror');
	});
})(jQuery);
</script>
<?php
	} else if (isset($prevpagenumber) && isset($pagenumber) && $pagenumber == 9)	{
		$storedSafePage = intval($profile1->getApplicationCounter());
		$enableUpdate = false;
		$promise1 = null;
		//Step 1: Handling Data From the Previous Page 
		if ($prevpagenumber < $pagenumber)	{
			$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
			$profile1 = null;
			try {
				$profile1 = new Profile($database, $__profileId, $conn);
				$profile1->loadXMLFolder("../data/profile");
			} catch (Exception $e)	{
				die($e->getMessage());
			}
			//$telephone = mysql_real_escape_string($_REQUEST['telephoneList']);
			$serverIpAddress = mysql_real_escape_string($_REQUEST['serverIpAddress']);
			$localAreaNetworkMask = mysql_real_escape_string($_REQUEST['localAreaNetworkMask']);
			if ($pagenumber -1 < $storedSafePage)	{
				//UPDATE WINDOW
				if ($serverIpAddress != $profile1->getServerIpAddress())	{
					$profile1->setServerIpAddress($serverIpAddress); $enableUpdate=true;
				}
				if ($localAreaNetworkMask != $profile1->getLocalAreaNetworkMask())	{
					$profile1->setLocalAreaNetworkMask($localAreaNetworkMask); $enableUpdate = true;
				}
			} else {
				//INSERT WINDOW
				$profile1->setServerIpAddress($serverIpAddress);
				$profile1->setLocalAreaNetworkMask($localAreaNetworkMask);
				$profile1->setApplicationCounter($pagenumber);
				$enableUpdate = true;
			}
			if ($enableUpdate)	{
				//Perform Verify First 
				$promise1 = new Promise();
				$promise1->setPromise(true);
				//Simulate promise
				//Return promise1
				if (! is_null($promise1) && $promise1->isPromising())	{
					try {
						$network1 = new Network($serverIpAddress, $localAreaNetworkMask);
						$profile1->commitUpdate();
					} catch (Exception $e)	{ 
						$promise1->setReason($e->getMessage());
						$promise1->setPromise(false);
					}
				}
			}
			mysql_close($conn);
		}
		//Step 2: Handling Data for this Page 
		//$website = "";
		$accountName = "";
		$bankAccount = "";
		$bankName = "";
		$branchName = "";
		$bankCode = "";
		$swiftCode = "";
		if ($pagenumber < $storedSafePage)	{
			//$website = $profile1->getWebsite()
			$bankAccount1 = $profile1->getBankAccountList();
			if (! is_null($bankAccount1))	{
				$bankAccount1 = $bankAccount1[0];
				$accountName = $bankAccount1->getElementsByTagName('accountName')->item(0)->nodeValue;
				$bankAccount = $bankAccount1->getElementsByTagName('bankAccount')->item(0)->nodeValue;
				$bankName = $bankAccount1->getElementsByTagName('bankName')->item(0)->nodeValue;
				$branchName = $bankAccount1->getElementsByTagName('branchName')->item(0)->nodeValue;
				$bankCode = $bankAccount1->getElementsByTagName('bankCode')->item(0)->nodeValue;
				$swiftCode = $bankAccount1->getElementsByTagName('swiftCode')->item(0)->nodeValue;
			}
		}
		//Beginning UI 
		//We must make sure the promise is set and has return valid response
?>
		<div class="ui-sys-panel-container mobile-collapse">
			<div class="ui-sys-panel ui-sys-data-capture">
				<div class="ui-sys-panel-header">Bank Information</div>
				<div class="ui-sys-panel-body">
					<div>
<?php 
	if (is_null($promise1) ||  $promise1->isPromising())	{
?>
						<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $_SERVER['PHP_SELF'] ?>">
							<input type="hidden" name="prevpagenumber" value="<?= $pagenumber ?>"/>
							<input type="hidden" name="pagenumber" value="<?= $pagenumber + 1 ?>"/>
							<fieldset>
								<div class="pure-control-group">
									<label for="accountName">Account Name </label>
									<input type="text" name="accountName" id="accountName" size="32" value="<?= $accountName ?>" required pattern="<?= $exprA48Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprA48Name ?>" validate_message="Account Name : <?= $msgA48Name ?>"/>
								</div>
								<div class="pure-control-group">
									<label for="bankAccount">Bank Acount </label>
									<input type="text" name="bankAccount" id="bankAccount" size="32" value="<?= $bankAccount ?>" required pattern="<?= $exprL32Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprL32Name ?>" validate_message="Bank Account : <?= $msgL32Name ?>"/>
								</div>
								<div class="pure-control-group">
									<label for="bankName">Bank Name </label>
									<input type="text" name="bankName" id="bankName" size="32" value="<?= $bankName ?>" required pattern="<?= $exprA48Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprA48Name ?>" validate_message="Bank Name : <?= $msgA48Name ?>"/>
								</div>
								<div class="pure-control-group">
									<label for="branchName">Branch Name </label>
									<input type="text" name="branchName" id="branchName" size="32" value="<?= $branchName ?>" required pattern="<?= $exprA48Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprA48Name ?>" validate_message="Branch Name : <?= $msgA48Name ?>"/>
								</div>
								<div class="pure-control-group">
									<label for="bankCode">Bank Code </label>
									<input type="text" name="bankCode" id="bankCode" size="32" value="<?= $bankCode ?>" notrequired="true" pattern="<?= $exprL16Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprL16Name ?>" validate_message="Bank Code : <?= $msgL16Name ?>"/>
								</div>
								<div class="pure-control-group">
									<label for="swiftCode">Swift Code </label>
									<input type="text" name="swiftCode" id="swiftCode" size="32" value="<?= $swiftCode ?>" notrequired="true" pattern="<?= $exprL16Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprL16Name ?>" validate_message="Swift Code : <?= $msgL16Name ?>"/>
								</div>
								<div class="pure-controls">
									<span id="perror" class="ui-sys-error-message"></span>
								</div>
								<div class="pure-controls">
									<span>
										<a class="button-link" href="<?= $thispage ?>?prevpagenumber=<?= $pagenumber ?>&pagenumber=<?= $pagenumber - 1 ?>">&lt;&lt; Previous</a>&nbsp;&nbsp;
								<input id="cmd1" type="button" value="Next &gt;&gt;"/>
									</span>
								</div>
							</fieldset>
						</form>
<?php 
	} else {
		//Not Promising 
?>
		<div class="ui-state-error">
		There were problems in your Network Settings <br />
		Details : <?= $promise1->getReason() ?>
		</div>
		<div class="ui-sys-inline-controls-right">
			<a class="button-link" href="<?= $thispage ?>?prevpagenumber=<?= $pagenumber ?>&pagenumber=<?= $pagenumber - 1 ?>">&lt;&lt; Previous</a>
		</div>
<?php
	} 
?>
					</div>
				</div>
				<div class="ui-sys-panel-footer"></div>
			</div>
		</div>
		<div class="ui-sys-clear-both"></div>
<script type="text/javascript">
(function($)	{
	$('#cmd1').on('click', function(event)	{
		event.preventDefault();
		generalFormSubmission(this, 'form1', 'perror');
	});
})(jQuery);
</script>
<?php
	} else if (isset($prevpagenumber) && isset($pagenumber) && $pagenumber == 8)	{
		$storedSafePage = intval($profile1->getApplicationCounter());
		$enableUpdate = false;
		//Step 1: Handling Data From the Previous Page 
		if ($prevpagenumber < $pagenumber)	{
			$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
			$profile1 = null;
			try {
				$profile1 = new Profile($database, $__profileId, $conn);
				$profile1->loadXMLFolder("../data/profile");
			} catch (Exception $e)	{
				die($e->getMessage());
			}
			//$telephone = mysql_real_escape_string($_REQUEST['telephoneList']);
			$accademicYearId = mysql_real_escape_string($_REQUEST['accademicYearId']);
			$tempDt = mysql_real_escape_string($_REQUEST['beginOfTheYearDate']);
			$tempDt = $tempDt."/*";
			$beginOfTheYearDate = DateAndTime::convertFromGUIDateFormatToSystemDateAndTimeFormat($tempDt); 
			$nextGraduationYear = mysql_real_escape_string($_REQUEST['nextGraduationYear']);
			$nextSemester = mysql_real_escape_string($_REQUEST['nextSemester']);
			$numberOfSemestersPerYear = mysql_real_escape_string($_REQUEST['numberOfSemestersPerYear']);
			if ($pagenumber -1 < $storedSafePage)	{
				//UPDATE WINDOW
				if (is_null($profile1->getCurrentAccademicYear()) || ($accademicYearId != $profile1->getCurrentAccademicYear()->getAccademicYearId()))	{
					$profile1->setCurrentAccademicYear($accademicYearId); $enableUpdate = true;
				}
				if (is_null($profile1->getBeginOfTheYearDate()) || ($beginOfTheYearDate != $profile1->getBeginOfTheYearDate()->getDateAndTimeString()))	{
					$profile1->setBeginOfTheYearDate($beginOfTheYearDate); $enableUpdate = true;
				}
				if ($nextGraduationYear != $profile1->getNextGraduationYear())	{
					$profile1->setNextGraduationYear($nextGraduationYear); $enableUpdate = true;
				}
				if ($nextSemester != $profile1->getNextSemester())	{
					$profile1->setNextSemester($nextSemester); $enableUpdate = true;
				}
				if ($numberOfSemestersPerYear != $profile1->getNumberOfSemestersPerYear())	{
					$profile1->setNumberOfSemestersPerYear($numberOfSemestersPerYear); $enableUpdate = true;
				}
			} else {
				//INSERT WINDOW
				$profile1->setCurrentAccademicYear($accademicYearId);
				$profile1->setBeginOfTheYearDate($beginOfTheYearDate);
				$profile1->setNextGraduationYear($nextGraduationYear);
				$profile1->setNextSemester($nextSemester);
				$profile1->setNumberOfSemestersPerYear($numberOfSemestersPerYear);
				$profile1->setApplicationCounter($pagenumber);
				$enableUpdate = true;
			}
			if ($enableUpdate)	{
				try {
					$profile1->commitUpdate();
				} catch (Exception $e)	{ die($e->getMessage()); }
			}
			mysql_close($conn);
		}
		//Step 2: Handling Data for this Page 
		$serverIpAddress = "";
		$localAreaNetworkMask = "";
		if ($pagenumber < $storedSafePage)	{
			$serverIpAddress = $profile1->getServerIpAddress();
			$localAreaNetworkMask = $profile1->getLocalAreaNetworkMask();
		}
		//Beginning UI 
?>
		<div class="ui-sys-panel-container mobile-collapse">
			<div class="ui-sys-panel ui-sys-data-capture">
				<div class="ui-sys-panel-header">Local Area Network Settings</div>
				<div class="ui-sys-panel-body">
					<div>
						<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $_SERVER['PHP_SELF'] ?>">
							<input type="hidden" name="prevpagenumber" value="<?= $pagenumber ?>"/>
							<input type="hidden" name="pagenumber" value="<?= $pagenumber + 1 ?>"/>
							<fieldset>
								<div class="pure-control-group">
									<label for="serverIpAddress">Server IP Address </label>
									<input type="text" name="serverIpAddress" id="serverIpAddress" size="32" value="<?= $serverIpAddress ?>" required pattern="<?= $exprIpAddress ?>" validate="true" validate_control="text" validate_expression="<?= $exprIpAddress ?>" validate_message="Server IP Address : <?= $msgIpAddress ?>"/>
								</div>
								<div class="pure-control-group">
									<label for="localAreaNetworkMask">Local Area Network Mask </label>
									<input type="text" name="localAreaNetworkMask" id="localAreaNetworkMask" size="32" value="<?= $localAreaNetworkMask ?>" required pattern="<?= $exprIpAddress ?>" validate="true" validate_control="text" validate_expression="<?= $exprIpAddress ?>" validate_message="Local Area Network Mask : <?= $msgIpAddress ?>"/>
								</div>
								<div class="pure-controls">
									<span id="perror" class="ui-sys-error-message"></span>
								</div>
								 <div class="pure-controls">
									<span>
										<a class="button-link" href="<?= $thispage ?>?prevpagenumber=<?= $pagenumber ?>&pagenumber=<?= $pagenumber - 1 ?>">&lt;&lt; Previous</a>&nbsp;&nbsp;
								<input id="cmd1" type="button" value="Next &gt;&gt;"/>
									</span>
								</div>
							</fieldset>
						</form>
					</div>
				</div>
				<div class="ui-sys-panel-footer"></div>
			</div>
		</div>
		<div class="ui-sys-clear-both"></div>
<script type="text/javascript">
(function($)	{
	$('#cmd1').on('click', function(event)	{
		event.preventDefault();
		generalFormSubmission(this, 'form1', 'perror');
	});
})(jQuery);
</script>
<?php
	} else if (isset($prevpagenumber) && isset($pagenumber) && $pagenumber == 7)	{
		$storedSafePage = intval($profile1->getApplicationCounter());
		$enableUpdate = false;
		//Step 1: Handling Data From the Previous Page 
		if ($prevpagenumber < $pagenumber)	{
			$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
			$profile1 = null;
			try {
				$profile1 = new Profile($database, $__profileId, $conn);
				$profile1->loadXMLFolder("../data/profile");
			} catch (Exception $e)	{
				die($e->getMessage());
			}
			$city = mysql_real_escape_string($_REQUEST['city']);
			$countryId = mysql_real_escape_string($_REQUEST['countryId']);
			$dob = DateAndTime::convertFromGUIDateFormatToSystemDateAndTimeFormat(mysql_real_escape_string($_REQUEST['dob']));
			if ($pagenumber -1 < $storedSafePage)	{
				//UPDATE WINDOW
				if ($city != $profile1->getCity())	{
					$profile1->setCity($city); $enableUpdate = true;
				}
				if (is_null($profile1->getCountry()) || ($countryId != $profile1->getCountry()->getCountryId()))	{
					$profile1->setCountry($countryId); $enableUpdate = true;
				}
				if (is_null($profile1->getDOB()) || ($dob != $profile1->getDOB()->getDateAndTimeString()))	{
					$profile1->setDOB($dob); $enableUpdate = true;
				}
			} else {
				//INSERT WINDOW
				$profile1->setCity($city);  
				$profile1->setCountry($countryId); 
				$profile1->setDOB($dob); 
				$profile1->setApplicationCounter($pagenumber);
				$enableUpdate = true;
			}
			if ($enableUpdate)	{
				try {
					$profile1->commitUpdate();
				} catch (Exception $e)	{ die($e->getMessage()); }
			}
			mysql_close($conn);
		}
		//Step 2: Handling Data for this Page 
		//$website = "";
		$accademicYearId = "";
		$beginOfTheYearDate = "";
		$nextGraduationYear = "";
		$nextSemester = "";
		$numberOfSemestersPerYear = "";
		if ($pagenumber < $storedSafePage)	{
			//$website = $profile1->getWebsite()
			$accademicYearId = $profile1->getCurrentAccademicYear()->getAccademicYearId();
			$tempDt1 = $profile1->getBeginOfTheYearDate();
			$beginOfTheYearDate = System::numberWidthCorrection($tempDt1->getDay(), 2)."/".System::numberWidthCorrection($tempDt1->getMonth(), 2);
			$nextGraduationYear = $profile1->getNextGraduationYear();
			$nextSemester = $profile1->getNextSemester();
			$numberOfSemestersPerYear = $profile1->getNumberOfSemestersPerYear();
		}
		//Beginning UI 
?>
		<div class="ui-sys-panel-container mobile-collapse">
			<div class="ui-sys-panel ui-sys-data-capture">
				<div class="ui-sys-panel-header"></div>
				<div class="ui-sys-panel-body">
					<div>
						<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $_SERVER['PHP_SELF'] ?>">
							<input type="hidden" name="prevpagenumber" value="<?= $pagenumber ?>"/>
							<input type="hidden" name="pagenumber" value="<?= $pagenumber + 1 ?>"/>
							<fieldset>
								<div class="pure-control-group">
									<label for="accademicYearId">Current Accademic Year </label>
									<select id="accademicYearId" name="accademicYearId" validate="true" validate_control="select" validate_expression="select" validate_message="Kindly Select the Current Accademic Year">
										<option value="_@32767@_">--select--</option>
<?php 
	$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
	$list1 = AccademicYear::loadAllData($database, $conn);
	foreach ($list1 as $alist1)	{
		$selected = "";
		if (! is_null($profile1->getCurrentAccademicYear()) && ($alist1['id'] == $profile1->getCurrentAccademicYear()->getAccademicYearId())) $selected="selected";
?>
			<option <?= $selected ?> value="<?= $alist1['id'] ?>"><?= $alist1['val'] ?></option>
<?php
	}
	mysql_close($conn);
?>
									</select>
								</div>
								<div class="pure-control-group">
									<label for="beginOfTheYearDate">Begin Of The Year Date </label>
									<input type="text" name="beginOfTheYearDate" id="beginOfTheYearDate" size="32" value="<?= $beginOfTheYearDate ?>" required pattern="<?= $exprDateNoYear ?>" validate="true" validate_control="text" validate_expression="<?= $exprDateNoYear ?>" validate_message="Begin of the Year Date : <?= $msgDateNoYear ?>"/>
								</div>
								<div class="pure-control-group">
									<label for="nextGraduationYear">Next Year of Graduation </label>
									<input type="text" name="nextGraduationYear" id="nextGraduationYear" size="32" value="<?= $nextGraduationYear ?>" required pattern="<?= $exprDateYearOnly ?>" validate="true" validate_control="text" validate_expression="<?= $exprDateYearOnly ?>" validate_message="Next Year Of Graduation: <?= $msgDateYearOnly ?>"/>
								</div>
								<div class="pure-control-group">
									<label for="nextSemester">Next Semester </label>
									<input type="text" name="nextSemester" id="nextSemester" size="32" value="<?= $nextSemester ?>" required pattern="<?= $expr2Number ?>" validate="true" validate_control="text" validate_expression="<?= $expr2Number ?>" validate_message="Next Semester: <?= $msg2Number ?>"/>
								</div>
								<div class="pure-control-group">
									<label for="numberOfSemestersPerYear">Number Of Semesters Per Year </label>
									<input type="text" name="numberOfSemestersPerYear" id="numberOfSemestersPerYear" size="32" value="<?= $numberOfSemestersPerYear ?>" required pattern="<?= $expr2Number ?>" validate="true" validate_control="text" validate_expression="<?= $expr2Number ?>" validate_message="Semesters per Year : <?= $msg2Number ?>"/>
								</div>
								<div class="pure-controls">
									<span id="perror" class="ui-sys-error-message"></span>
								</div>
								 <div class="pure-controls">
									<span>
										<a class="button-link" href="<?= $thispage ?>?prevpagenumber=<?= $pagenumber ?>&pagenumber=<?= $pagenumber - 1 ?>">&lt;&lt; Previous</a>&nbsp;&nbsp;
								<input id="cmd1" type="button" value="Next &gt;&gt;"/>
									</span>
								</div>
							</fieldset>
						</form>
					</div>
				</div>
				<div class="ui-sys-panel-footer"></div>
			</div>
		</div>
		<div class="ui-sys-clear-both"></div>
<script type="text/javascript">
(function($)	{
	$('#cmd1').on('click', function(event)	{
		event.preventDefault();
		if ((parseInt($('#nextSemester').val()) != 0) && (parseInt($('#nextSemester').val()) <= parseInt($('#numberOfSemestersPerYear').val()))) {
			generalFormSubmission(this, 'form1', 'perror');
		} else {
			$('#perror').html('Next Semester Can not Exceed Number of Semesters Per Year, And It can not be equal to ZERO');
		}
	});
})(jQuery);
</script>
<?php
	} else if (isset($prevpagenumber) && isset($pagenumber) && $pagenumber == 6)	{
		$storedSafePage = intval($profile1->getApplicationCounter());
		$enableUpdate = false;
		//Step 1: Handling Data From the Previous Page 
		if ($prevpagenumber < $pagenumber)	{
			$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
			$profile1 = null;
			try {
				$profile1 = new Profile($database, $__profileId, $conn);
				$profile1->loadXMLFolder("../data/profile");
			} catch (Exception $e)	{
				die($e->getMessage());
			}
			//$telephone = mysql_real_escape_string($_REQUEST['telephoneList']);
			$firstDayOfAWeekId = mysql_real_escape_string($_REQUEST['firstDayOfAWeekId']);
			if ($pagenumber -1 < $storedSafePage)	{
				//UPDATE WINDOW
				if (is_null($profile1->getFirstDayOfAWeek()) || ($firstDayOfAWeekId != $profile1->getFirstDayOfAWeek()->getDayId()))	{
					$profile1->setFirstDayOfAWeek($firstDayOfAWeekId); $enableUpdate=true;
				}
			} else {
				//INSERT WINDOW
				$profile1->setFirstDayOfAWeek($firstDayOfAWeekId);
				$profile1->setApplicationCounter($pagenumber);
				$enableUpdate = true;
			}
			if ($enableUpdate)	{
				try {
					$profile1->commitUpdate();
				} catch (Exception $e) { die($e->getMessage()); }
			}
			mysql_close($conn);
		}
		//Step 2: Handling Data for this Page 
		//$website = "";
		$city = "";
		$countryId = -1;
		$dob = "";
		if ($pagenumber < $storedSafePage)	{
			//$website = $profile1->getWebsite()
			$city = $profile1->getCity();
			$countryId = $profile1->getCountry()->getCountryId();
			$dob = DateAndTime::convertFromDateTimeObjectToGUIDateFormat($profile1->getDOB());
		}
		//Beginning UI 
?>
		<div class="ui-sys-panel-container mobile-collapse">
			<div class="ui-sys-panel ui-sys-data-capture">
				<div class="ui-sys-panel-header">City, Country & DOB</div>
				<div class="ui-sys-panel-body">
					<div>
						<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $_SERVER['PHP_SELF'] ?>">
							<input type="hidden" name="prevpagenumber" value="<?= $pagenumber ?>"/>
							<input type="hidden" name="pagenumber" value="<?= $pagenumber + 1 ?>"/>
							<fieldset>
								<div class="pure-control-group">
									<label for="city">City </label>
									<input type="text" name="city" id="city" size="32" value="<?= $city ?>" required pattern="<?= $exprA32Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprA32Name ?>" validate_message="City: <?= $msgA32Name ?>"/>
								</div>
								<div class="pure-control-group">
									<label for="countryId">Country </label>
									<select id="countryId" name="countryId" validate="true" validate_control="select" validate_expression="select" validate_message="Kindly select the Country">
										<option value="_@32767@_">--select--</option>
<?php 
	$conn = mysql_connect($hostname, $user, $pass) or die("<option value='_@32767@_'>Query Err</option>");
	$list1 = Country::loadAllData($database, $conn);
	foreach ($list1 as $alist1)	{
		$selected = "";
		if ($alist1['id'] == $countryId) $selected="selected";
?>
		<option <?= $selected ?> value="<?= $alist1['id'] ?>"><?= $alist1['val'] ?></option>
<?php
	}
	mysql_close($conn);
?>
									</select>
								</div>
								<div class="pure-control-group">
									<label for="dob">Date of Birth </label>
									<input class="datepicker" type="text" name="dob" id="dob" size="32" value="<?= $dob ?>" required pattern="<?= $exprDate ?>" validate="true" validate_control="text" validate_expression="<?= $exprDate ?>" validate_message="Date: <?= $msgDate ?>"/>
								</div>
								<div class="pure-controls">
									<span id="perror" class="ui-sys-error-message"></span>
								</div>
								<div class="pure-controls">
									<span>
										<a class="button-link" href="<?= $thispage ?>?prevpagenumber=<?= $pagenumber ?>&pagenumber=<?= $pagenumber - 1 ?>">&lt;&lt; Previous</a>&nbsp;&nbsp;
								<input id="cmd1" type="button" value="Next &gt;&gt;"/>
									</span>
								</div>
							</fieldset>
						</form>
					</div>
				</div>
				<div class="ui-sys-panel-footer"></div>
			</div>
		</div>
		<div class="ui-sys-clear-both"></div>
<script type="text/javascript">
<?php 
	//We need to do the first day of a week Adjustment
	//The data submitted from the previous page still 
	//have not taken the effect 
	if (isset($_REQUEST['firstDayOfAWeekId']))	{
		$firstDayOfAWeek1 = null;
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database service");
		try {
			$firstDayOfAWeek1 = new DaysOfAWeek($database, $_REQUEST['firstDayOfAWeekId'], $conn);
			$__systemDayOffset = $firstDayOfAWeek1->getOffsetValue();
		} catch (Exception $e)	{ die($e->getMessage()); }
		mysql_close($conn);
	}
?>
(function($)	{
	$('.datepicker').removeClass('datepicker').datepicker({
		dateFormat: 'dd/mm/yy',
		firstDay: <?= $__systemDayOffset ?>,
		changeYear: true,
		yearRange:'1961:2099'
	});
	//$('.datepicker').datepicker('option', 'firstDay', <?=  $__systemDayOffset ?>);
	$('#cmd1').on('click', function(event)	{
		event.preventDefault();
		generalFormSubmission(this, 'form1', 'perror');
	});
})(jQuery);
</script>
<?php
	} else if (isset($prevpagenumber) && isset($pagenumber) && $pagenumber == 5)	{
		$storedSafePage = intval($profile1->getApplicationCounter());
		$enableUpdate = false;
		//Step 1: Handling Data From the Previous Page 
		if ($prevpagenumber < $pagenumber)	{
			$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
			$profile1 = null;
			try {
				$profile1 = new Profile($database, $__profileId, $conn);
				$profile1->loadXMLFolder("../data/profile");
			} catch (Exception $e)	{
				die($e->getMessage());
			}
			//$telephone = mysql_real_escape_string($_REQUEST['telephoneList']);
			$website = mysql_real_escape_string($_REQUEST['website']);
			if ($pagenumber -1 < $storedSafePage)	{
				//UPDATE WINDOW
				if ($website != $profile1->getWebsite())	{
					$profile1->setWebsite($website); $enableUpdate = true;
				}
			} else {
				//INSERT WINDOW
				$profile1->setWebsite($website); 
				$profile1->setApplicationCounter($pagenumber);
				$enableUpdate = true;
			}
			if ($enableUpdate)	{
				try {
					$profile1->commitUpdate();
				} catch (Exception $e)	{ die($e->getMessage()); }
			}
			mysql_close($conn);
		}
		//Step 2: Handling Data for this Page 
		//$website = "";
		$firstDayOfAWeekId = -1;
		if ($pagenumber < $storedSafePage)	{
			//$website = $profile1->getWebsite()
			$firstDayOfAWeekId = $profile1->getFirstDayOfAWeek()->getDayId();
		}
		//Beginning UI 
?>
		<div class="ui-sys-panel-container mobile-collapse">
			<div class="ui-sys-panel ui-sys-data-capture">
				<div class="ui-sys-panel-header">First Day Of A Week</div>
				<div class="ui-sys-panel-body">
					<div>
						<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $_SERVER['PHP_SELF'] ?>">
							<input type="hidden" name="prevpagenumber" value="<?= $pagenumber ?>"/>
							<input type="hidden" name="pagenumber" value="<?= $pagenumber + 1 ?>"/>
							<fieldset>
								<div class="pure-control-group">
									<label for="firstDayOfAWeekId">First Day Of A Week </label>
									<select id="firstDayOfAWeekId" name="firstDayOfAWeekId" validate="true" validate_control="select" validate_expression="select" validate_message="Please, Select First Day of A week">
										<option value="_@32767@_">--select--</option>
<?php 
	$conn = mysql_connect($hostname, $user, $pass) or die("<option value='_@32767@_'>Failed</option>");
	$list1 = DaysOfAWeek::loadAllData($database, $conn);
	foreach ($list1 as $alist1)	{
		$selected = "";
		if ($alist1['id'] == $firstDayOfAWeekId)	$selected = "selected";
?>
		<option <?= $selected ?> value="<?= $alist1['id'] ?>"><?= $alist1['val'] ?></option>
<?php
	}
	mysql_close($conn);
?>
									</select>
								</div>
								<div class="pure-controls">
									<span id="perror" class="ui-sys-error-message"></span>
								</div>
								 <div class="pure-controls">
									<span>
										<a class="button-link" href="<?= $thispage ?>?prevpagenumber=<?= $pagenumber ?>&pagenumber=<?= $pagenumber - 1 ?>">&lt;&lt; Previous</a>&nbsp;&nbsp;
								<input id="cmd1" type="button" value="Next &gt;&gt;"/>
									</span>
								</div>
							</fieldset>
						</form>
					</div>
				</div>
				<div class="ui-sys-panel-footer"></div>
			</div>
		</div>
		<div class="ui-sys-clear-both"></div>
<script type="text/javascript">
(function($)	{
	$('#cmd1').on('click', function(event)	{
		event.preventDefault();
		generalFormSubmission(this, 'form1', 'perror');
	});
})(jQuery);
</script>
<?php
	} else if (isset($prevpagenumber) && isset($pagenumber) && $pagenumber == 4)	{
		$storedSafePage = intval($profile1->getApplicationCounter());
		$enableUpdate = false;
		$enableDOMUpdate = false;
		$promise1 = null;
		//Step1 Handling Data From the Previous Page 
		if ($prevpagenumber < $pagenumber)	{
			$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
			$profile1 = null;
			try {
				$profile1 = new Profile($database, $__profileId, $conn);
				$profile1->loadXMLFolder("../data/profile");
			} catch (Exception $e)	{
				die($e->getMessage());
			}
			$telephone = mysql_real_escape_string($_REQUEST['telephoneList']);
			$email = mysql_real_escape_string($_REQUEST['emailList']);
			$postalAddress = mysql_real_escape_string($_REQUEST['postalAddress']);
			$physicalAddress = $_REQUEST['physicalAddressList'];
			$fax = mysql_real_escape_string($_REQUEST['faxList']);
			$otherCommunication = mysql_real_escape_string($_REQUEST['otherCommunication']);
			if ($pagenumber - 1 < $storedSafePage)	{
				if (isset($telephone) && ($telephone != $profile1->getTelephoneList()[0]))	{
					$profile1->setTelephoneList($telephone); $enableUpdate = true;
				}
				if (isset($email) && ($email != $profile1->getEmailList()[0]))	{
					$profile1->setEmailList($email); $enableUpdate = true;
				}
				if (isset($postalAddress) && ($postalAddress != $profile1->getPostalAddress()))	{
					$profile1->setPostalAddress($postalAddress); $enableUpdate = true;
				}
				if (isset($fax) && ($fax != $profile1->getFaxList()[0]))	{
					$profile1->setFaxList($fax); $enableUpdate = true;
				}
				if (isset($otherCommunication) && ($otherCommunication != $profile1->getOtherCommunication()[0]))	{
					$profile1->setOtherCommunication($otherCommunication); $enableUpdate = true;
				}
				if (isset($physicalAddress))	{
					$nodeCollection = FileFactory::getListOfNodesWithValueFromCollection($profile1->getPhysicalAddressList(), 0, $physicalAddress, false);
					if (sizeof($nodeCollection) == 0)	{
						//It is not there
						$enableDOMUpdate = true;
					}
				}
			} else {
				$profile1->setTelephoneList($telephone);
				$profile1->setEmailList($email); 
				$profile1->setFaxList($fax);
				$profile1->setOtherCommunication($otherCommunication);
				$profile1->setApplicationCounter($pagenumber);
				$profile1->setPostalAddress($postalAddress);
				$enableUpdate = true;
				$enableDOMUpdate = true;
			}
			if ($enableDOMUpdate)	{
				$promise1 = $profile1->backupXMLFile();
				$promise1 = $profile1->clearPhysicalAddresses();
				$promise1 = $profile1->addPhysicalAddress($physicalAddress);
			}
			if ($enableUpdate && (is_null($promise1) || $promise1->isPromising()))	{
				try {
					$profile1->commitUpdate();
				} catch (Exception $e)	{
					die($e->getMessage());
				}
			}
			mysql_close($conn);
		}
		//Step 2
		$website = "";
		if ($pagenumber < $storedSafePage)	{
			$website = $profile1->getWebsite();
		}
		//Beginning UI 
?>
		<div class="ui-sys-panel-container mobile-collapse">
			<div class="ui-sys-data-capture ui-sys-panel">
				<div class="ui-sys-panel-header">Location</div>
				<div class="ui-sys-panel-body">
					<div>
<?php 
	if (is_null($promise1) || $promise1->isPromising()) {
?>
						<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $_SERVER['PHP_SELF'] ?>">
							<input type="hidden" name="prevpagenumber" value="<?= $pagenumber ?>"/>
							<input type="hidden" name="pagenumber" value="<?= $pagenumber + 1 ?>"/>
							<fieldset>
								<div class="pure-control-group">
									<label for="website">Website </label></td>
									<input type="text" name="website" id="website" size="32" value="<?= $website ?>" required pattern="<?= $exprWebsite ?>" validate="true" validate_control="text" validate_expression="<?= $exprWebsite ?>%<?= $exprA64Name ?>" validate_message="Website Format: <?= $msgWebsite ?>%Website Length: <?= $msgA64Name ?>"/>
								</div>
								<div class="pure-controls">
									<span id="perror" class="ui-sys-error-message"></span>
								</div>
								 <div class="pure-controls">
									<span class="ui-sys-inline-controls-right">
										<a class="button-link" href="<?= $thispage ?>?prevpagenumber=<?= $pagenumber ?>&pagenumber=<?= $pagenumber - 1 ?>">&lt;&lt; Previous</a>&nbsp;&nbsp;
								<input id="cmd1" type="button" value="Next &gt;&gt;"/>
									</span>
								</div>
							</fieldset>
						</form>
<?php 
	} else {
?>
		There were problem in uploading the physical File <br/>
		Reason : <?= $promise1->getReason() ?>
<?php	
	}
?>
					</div>
				</div>
				<div class="ui-sys-panel-footer"></div>
			</div>
		</div>
		<div class="ui-sys-clear-both"></div>
<script type="text/javascript">
(function($)	{
	$('#cmd1').on('click', function(event)	{
		event.preventDefault();
		generalFormSubmission(this, 'form1', 'perror');
	});
})(jQuery);
</script>
<?php
	} else if (isset($prevpagenumber) && isset($pagenumber) && $pagenumber == 3)	{
		$storedSafePage = intval($profile1->getApplicationCounter());
		$enableUpdate = false;
		$continueToStepTwo = true;
		$promise1 = null;
		//Step 1: Data From the previous page
		if ($prevpagenumber < $pagenumber)	{
			$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
			$profile1 = null;
			try {
				$profile1 = new Profile($database, $__profileId, $conn);
				$profile1->loadXMLFolder("../data/profile");
			} catch (Exception $e)	{
				die($e->getMessage());
			}
			$logoArray1 = $_FILES['logo'];
			$enablePhotoUpdate = false;
			if ($pagenumber - 1 < $storedSafePage)	{
				if (Object::isThereAnyFileReceivedFromTheClient($logoArray1))	{
					$enablePhotoUpdate = true;
				}
			} else {
				if (Object::isThereAnyFileReceivedFromTheClient($logoArray1))	{
					$enablePhotoUpdate = true;
				}
				$enableUpdate = true;
				$profile1->setApplicationCounter($pagenumber);
			}
			$logoFolder = "../data/profile/logo";
			$logofilename = "uploaded_sys_logo";
			$fileextension = Object::getUploadedFileExtension($logoArray1);
			$uploadedFileName = $logofilename.".".$fileextension;
			if ($enablePhotoUpdate)	{
				//We will just overwrite 
				$validTypes = array("image/jpeg", "image/png", "image/jpg");
				$validExtensions = array("jpeg", "png", "jpg");
				$maximumUploadedSize = 2097153;
				$promise1 = Object::saveUploadedFile($logoArray1, $logoFolder, $logofilename, $validTypes, $validExtensions, $maximumUploadedSize);
				$continueToStepTwo = false;
			}
			if (! is_null($promise1) && $promise1->isPromising())	{
				$profile1->setLogo($uploadedFileName);
				$continueToStepTwo = true;
				$enableUpdate = true;
			}
			if ($enableUpdate)	{
				try {
					$profile1->commitUpdate();
				} catch (Exception $e)	{ die($e->getMessage()); }
			}
			mysql_close($conn);
		}
		//Step 2: Handling This page
		//Note During Installation, Only One in the List 
		//We are creating a basic setup Only 
		$telephoneList = "";
		$emailList = "";
		$postalAddress = "";
		$physicalAddressList = "";
		$faxList = "";
		$otherCommunication = "";
		if ($pagenumber < $storedSafePage)	{
			//Assign All These Are Arrays
			$telephoneList = $profile1->getTelephoneList();
			$emailList = $profile1->getEmailList();
			$postalAddress = $profile1->getPostalAddress();
			$physicalAddressList = $profile1->getPhysicalAddressList();
			$faxList = $profile1->getFaxList();
			$otherCommunication = $profile1->getOtherCommunication();
			/* Converting to strings */
			$telephoneList = $telephoneList[0];
			$emailList = $emailList[0];
			if (! is_null($physicalAddressList))	{
				$physicalAddressList = $physicalAddressList[0];
				$physicalAddressList = $physicalAddressList->nodeValue;
			}
			$faxList = $faxList[0];
			$otherCommunication = $otherCommunication[0];
		}
?>
		<div class="ui-sys-panel-container mobile-collapse">
			<div class="ui-sys-panel ui-sys-data-capture">
				<div class="ui-sys-panel-header">Addresses</div>
				<div class="ui-sys-panel-body">
					<div>
<?php 
	if (is_null($promise1) || $promise1->isPromising())	{
?>
						<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $_SERVER['PHP_SELF'] ?>">
							<input type="hidden" name="prevpagenumber" value="<?= $pagenumber ?>"/>
							<input type="hidden" name="pagenumber" value="<?= $pagenumber + 1 ?>"/>
							<fieldset>
								<div class="pure-control-group">
									<label for="telephoneList">Telephone </label>
									<input type="text" name="telephoneList" id="telephoneList" size="32" value="<?= $telephoneList ?>" required pattern="<?= $exprPhone ?>" validate="true" validate_control="text" validate_expression="<?= $exprPhone ?>" validate_message="Telephone: <?= $msgPhone ?>"/>
								</div>
								<div class="pure-control-group">
									<label for="emailList">Email </label>
									<input type="text" name="emailList" id="emailList" size="32" value="<?= $emailList ?>" required pattern="<?= $exprEmail ?>" validate="true" validate_control="text" validate_expression="<?= $exprEmail ?>%<?= $exprA32Name ?>" validate_message="Email Format: <?= $msgEmail ?>%Email Length: <?= $msgA32Name ?>"/>
								</div>
								<div class="pure-control-group">
									<label for="postalAddress">Postal Address </label>
									<input type="text" name="postalAddress" id="postalAddress" size="32" value="<?= $postalAddress ?>" required pattern="<?= $exprA64Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprA64Name ?>" validate_message="Postal Address : <?= $msgA64Name ?>"/>
								</div>
								<div class="pure-control-group">
									<label for="physicalAddressList">Physical Address </label>
									<input type="text" name="physicalAddressList" id="physicalAddressList" size="32" value="<?= $physicalAddressList ?>" required pattern="<?= $exprA64Name ?>" validate="true" validate_control="text" validate_expression="<?= $exprA64Name ?>" validate_message="Physical Address : <?= $msgA64Name ?>"/>
								</div>
								<div class="pure-control-group">
									<label for="faxList">Fax </label>
									<input type="text" name="faxList" id="faxList" size="32" value="<?= $faxList ?>" notrequired="true" validate="true" validate_control="text" validate_expression="<?= $exprPhone ?>" validate_message="Fax: <?= $msgPhone ?>"/>
								</div>
								<div class="pure-control-group">
									<label for="otherCommunication">Other Communication </label>
									<input type="text" name="otherCommunication" id="otherCommunication" size="32" value="<?= $otherCommunication ?>" notrequired="true" validate="true" validate_control="text" validate_expression="<?= $exprA64Name ?>" validate_message="Other Communication: <?= $msgA64Name ?>"/>
								</div>
								<div class="pure-controls">
									<span id="perror" class="ui-sys-error-message"></span>
								</div>
								 <div class="pure-controls">
										<a class="button-link" href="<?= $thispage ?>?prevpagenumber=<?= $pagenumber ?>&pagenumber=<?= $pagenumber - 1 ?>">&lt;&lt; Previous</a>&nbsp;&nbsp;
								<input id="cmd1" type="button" value="Next &gt;&gt;"/>
								</div>
							</fieldset>
						</form>	
<?php 
	} else {
?>
		There were problems in Uploading your Photo <br />
		Problem Details: <i><?= $promise1->getReason() ?></i>
		<div class="ui-sys-inline-controls-right">
			<a class="button-link" href="<?= $thispage ?>?prevpagenumber=<?= $pagenumber ?>&pagenumber=<?= $pagenumber - 1 ?>">&lt;&lt; Previous</a>
		</div>
<?php
	}
?>
					</div>
				</div>
				<div class="ui-sys-panel-footer"></div>
			</div>
		</div>
		<div class="ui-sys-clear-both">&nbsp;</div>
<script type="text/javascript">
(function($)	{
	$('#cmd1').on('click', function(event)	{
		event.preventDefault();
		generalFormSubmission(this, 'form1', 'perror');
	});
})(jQuery);
</script>
<?php
	} else if (isset($prevpagenumber) && isset($pagenumber) && $pagenumber == 2)	{
		$storedSafePage = intval($profile1->getApplicationCounter());
		$enableUpdate = false;
		//Step 1: Data From Previous Page 
		if ($prevpagenumber < $pagenumber)	{
			//Need to recreate Profile Object since we have lost the conn string 
			$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database service");
			$profile1 = null;
			try {
				$profile1 = new Profile($database, $__profileId, $conn);
			} catch (Exception $e)	{ die($e->getMessage()); }
			$profileName = mysql_real_escape_string($_REQUEST['profileName']);
			if ($pagenumber - 1 < $storedSafePage)	{
				//We have data this is an editing 
				if (strcmp($profileName, $profile1->getProfileName()) != 0)	{
					$profile1->setProfileName($profileName); $enableUpdate = true;
				}
			} else {
				//Update All 
				$profile1->setProfileName($profileName);
				$enableUpdate = true;
				//We need to Update ApplicationCounter 
				$profile1->setApplicationCounter($pagenumber);
			}
			//Entering Saving Mode 
			if ($enableUpdate)	{
				try {
					$profile1->commitUpdate();
				} catch (Exception $e)	{ die($e->getMessage()); }
			}
			mysql_close($conn);
		} //end--step 1
		//Step 2: Data Intended for this page only 
		$logo = $logo = "../".$dataFolder."/".$profile1->getDataFolder()."/logo/default.jpg";
		$photoname = "default.jpg";
		$trackchange = 0;
		if ($pagenumber < $storedSafePage)	{
			$logo = "../".$dataFolder."/".$profile1->getDataFolder()."/logo/".$profile1->getLogo();
			$photoname = $profile1->getLogo();
			$trackchange = 1;
		}
		//Beginning UI 
?>
		<div class="ui-sys-panel-container mobile-collapse">
			<div class="ui-sys-panel ui-sys-data-capture">
				<div class="ui-sys-panel-header">Institution Logo</div>
				<div class="ui-sys-panel-body">
<!--Beginning a photo container-->	
<div class="photocontainer">
	<div class="photodisplay">
		<img id="__id_image_photo_container" alt="PIC" src="<?= $logo ?>"/>
	</div>
	<div class="photodata">
		<form id="form1" method="POST" action="<?= $_SERVER['PHP_SELF'] ?>" enctype="multipart/form-data">
			<input type="hidden" name="prevpagenumber" value="<?= $pagenumber ?>"/>
			<input type="hidden" name="pagenumber" value="<?= $pagenumber + 1 ?>"/>
			<table>
				<tr>
					<td><label for="__id_image_photo_control">Select Logo</label></td>
					<td><input  id="__id_image_photo_control" type="file" class="ui-sys-file-upload" name="logo" data-trackchange="<?= $trackchange ?>" value="<?= $photoname ?>" accept="image/*"/></td>
				</tr>
				<tr>
					<td colspan="2" id="perror" class="ui-sys-inline-controls-center ui-sys-error-message"></td>
				</tr>
				<tr>
					<td colspan="2" class="ui-sys-inline-controls-right"> 
					<a class="button-link" href="<?= $thispage ?>?prevpagenumber=<?= $pagenumber ?>&pagenumber=<?= $pagenumber - 1 ?>">&lt;&lt; Previous</a>
					<input type="submit" value="Upload"/></td>
				</tr>
			</table>
		</form>
	</div>
	<div class="ui-sys-clear-both">&nbsp;</div>
</div>
<!--Ending a photo container-->
				</div>
				<div class="ui-sys-panel-footer"></div>
			</div>
		</div>
		<div class="ui-sys-clear-both">&nbsp;</div>
<script type="text/javascript">
					function showImage(_fileSource, _imageDestination)	{
						var fileSource1 = document.getElementById(_fileSource);
						if (! fileSource1) return;
						var imageDestination1 = document.getElementById(_imageDestination);
						if (! imageDestination1) return;
						var fileReader1 = new FileReader();
						$(fileReader1).on('load', function(event)	{
							imageDestination1.src = this.result;
						});
						$(fileSource1).on('change', function(event)	{
							fileReader1.readAsDataURL(fileSource1.files[0]);
						});
					}
					showImage('__id_image_photo_control', '__id_image_photo_container');
					(function($)	{
						$('#__id_image_photo_control').on('change', function(event)	{
							$('#__id_image_photo_control').data('trackchange', '1');
							showImage('__id_image_photo_control', '__id_image_photo_container');
						});
						$('#form1').on('submit', function(event)	{
							event.preventDefault();
							//Make use of track changes to validate further
							var fileControl1 = document.getElementById('__id_image_photo_control');
							if (! fileControl1) return;
							generalFormSubmission(this, 'form1', 'perror');
							if (parseInt($(fileControl1).data('trackchange')) == 1)	{
								generalFormSubmission(this, 'form1', 'perror');
							} else {
								$('#perror').html("No Update were done");
							} //end-if-else
						});
					})(jQuery);
</script>
<?php
	} else if (isset($prevpagenumber) && isset($pagenumber) && $pagenumber == 1)	{
		$storedSafePage = intval($profile1->getApplicationCounter());
		$enableUpdate = false;
		//Step 2: Handling Data Intended just for this page 
		$profileName = "";
		if ($pagenumber < $storedSafePage)	{
			$profileName = $profile1->getProfileName();
		}
?>	
		<div class="mobile-collapse ui-sys-panel-container">
			<div class="ui-sys-panel ui-sys-data-capture">
				<div class="ui-sys-panel-header">Institution Name</div>
				<div class="ui-sys-panel-body">
					<div>
						<form class="pure-form pure-form-aligned" id="form1" method="POST" action="<?= $_SERVER['PHP_SELF'] ?>">
							<input type="hidden" name="prevpagenumber" value="<?= $pagenumber ?>"/>
							<input type="hidden" name="pagenumber" value="<?= $pagenumber + 1 ?>"/>
							<fieldset>
								<div class="pure-control-group">
									<label for="profileName">Name of the Institution </label>
									<input type="text" name="profileName" id="profileName" size="32" value="<?= $profileName ?>" required pattern="<?= $expr96Name ?>" validate="true" validate_control="text" validate_expression="<?= $expr96Name ?>" validate_message="Name Of Institution: <?= $msg96Name ?>"/>
								</div>
								<div class="pure-controls">
									<span id="perror" class="ui-sys-error-message" ></span>
								</div>
								<div class="pure-controls">
									<span class="ui-sys-inline-controls-right">
										<a class="button-link" href="<?= $thispage ?>?prevpagenumber=<?= $pagenumber ?>&pagenumber=<?= $pagenumber - 1 ?>">&lt;&lt; Previous</a>&nbsp;&nbsp;
								<input id="cmd1" type="button" value="Next &gt;&gt;"/>
									</span>
								</div>
							</fieldset>
						</form>
					</div>
				</div>
				<div class="ui-sys-panel-footer ui-sys-right-text-in-block">
				</div>
			</div>
		</div>
		<div class="ui-sys-clear-both">&nbsp;</div>
<script type="text/javascript">
(function($)	{
	$('#cmd1').on('click', function(event)	{
		event.preventDefault();
		generalFormSubmission(this, 'form1', 'perror');
	});
})(jQuery);
</script>
<?php
	} else {
		//This should be done once 
		$conn = mysql_connect($hostname, $user, $pass) or die("Could not connect to a database services");
		$profile1 = null;
		try {
			$profile1 = new Profile($database, $__profileId, $conn);
			$profile1->setRevisionNumber("1");
			$profile1->setRevisionTime($systemDate1->getDateAndTimeString());
			$profile1->commitUpdate();
		} catch (Exception $e)	{
			die($e->getMessage());
		}
		mysql_close($conn);
?>
		<div class="ui-sys-oi-left-fill mobile-collapse">
			<div class="ui-sys-panel">
				<div class="ui-sys-panel-header">Welcome</div>
				<div class="ui-sys-panel-body ui-sys-center-text">
					<div class="ui-widget ui-widget-content">
				Welcome to the Installation Page of Student Management Information System <br/>
			This wizard will guide you step by step on the Installation of this System
					</div>
				</div>
				<div class="ui-sys-panel-footer">
					<div class="ui-sys-right-text-in-block">
						<a class="button-link" href="<?= $thispage ?>?prevpagenumber=0&pagenumber=1">Next &gt;&gt;</a>
					</div>
				</div>
			</div>
		</div>
		<div class="ui-sys-clear-both">&nbsp;</div>
<?php
	}
?>
</div>
<!--END OF PUTTING CODE-->
	<div class="ui-sys-footer mobile-collapse">
<?php   
	//You must have a DateAndTime Object carrying date, we are interested with only year 
	//So any default_timezone is okay with us at this point 
	include("../template/footer.php");
?>
	</div>
</div>

</body>
</html>