<?php 
$doc=new DOMDocument("1.0");
$doc->formatOutput = true;
if (! isset($argv[1])) die("Command Syntax: \"php exec.php outputfile\"");
$outputfile = $argv[1];
/**************************************BEGIN INSERT CODE HERE****************************************/

$profile=$doc->createElement('profile');
$physicalAddresses=$doc->createElement('physicalAddresses');
$profile->appendChild($physicalAddresses);;
$vision=$doc->createElement('vision');
$profile->appendChild($vision);;
$mission=$doc->createElement('mission');
$profile->appendChild($mission);;
$banks=$doc->createElement('banks');
$profile->appendChild($banks);;
$smsAccount=$doc->createElement('smsAccount');
$profile->appendChild($smsAccount);;
$doc->appendChild($profile);

/**************************************END  INSERT CODE HERE****************************************/
if (! $doc->save($outputfile)) die("Perhaps no permission to write on the server");
?>