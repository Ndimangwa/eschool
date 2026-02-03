<?php

/**
* Send a POST requst using cURL
* @param string $url to request
* @param array $post values to send
* @param array $options for cURL
* @return string
*/
function performSending($url, $postfields)	{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('accept' => 'application/json',
						'content-type' => 'application/json',
						'authorization' => 'Basic bmdveWE6UmVjeWNsZSM3Qmlu'));
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 4);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postfields));
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	if (! $result = curl_exec($ch))	{
		trigger_error(curl_error($ch));
	}
	curl_close($ch);
	return $result;
}


$url = "https://api.infobip.com/sms/1/text/single";
//$url="http://10.115.0.3/collegeplus/server/freeresponse.php";
$message = performSending($url, array('from'=>'PHPCode','to'=>'255787101808','text'=>'Hapa tuko pouwa')) or die("Could not complete connection");
//$message = performSending($url, array('from'=>'PHP Code','to'=>['255787101809','255784808110'],'text'=>'Hapa tuko pouwa')) or die("Could not complete connection");
echo $message;
?>