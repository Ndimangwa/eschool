<?php

/**
* Send a POST requst using cURL
* @param string $url to request
* @param array $post values to send
* @param array $options for cURL
* @return string
*/
function dopost($url, array $post = NULL, array $options = array())	{
	$defaults = array(
        CURLOPT_POST => 1,
        CURLOPT_HEADER => 0,
        CURLOPT_URL => $url,
        CURLOPT_FRESH_CONNECT => 1,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_FORBID_REUSE => 1,
        CURLOPT_TIMEOUT => 4,
        CURLOPT_POSTFIELDS => http_build_query($post)
    );
	$ch = curl_init();
	$options = $options + $defaults;
	foreach ($options as $key => $val)	{
		echo "$key\n";
		$key = intval($key);
		curl_setopt($ch, $key, $val);
	}
	if (! $result = curl_exec($ch))	{
		trigger_error(curl_error($ch));
	}
	curl_close($ch);
	return $result;
}
public function performSending($url)	{
	
}
$url = "https://api.infobip.com/sms/1/text/single";
$headers = array(
  'accept' => 'application/json',
  'content-type' => 'application/json',
  'authorization' => 'Basic bmdveWE6UmVjeWNsZSM3Qmlu'
);
//$options = array(CURLOPT_HTTPHEADER=>$headers,CURLOPT_SSL_VERIFYPEER=>false,CURLOPT_VERIFYHOST=>0);
$options= array('CURLOPT_HTTPHEADER'=>$headers);
$message = dopost($url, array('from'=>'PHP Code','to'=>'255787101808','text'=>'Hapa tuko pouwa'), $options) or die("Could not complete connection");
echo $message;
?>