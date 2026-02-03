<?php

/**
* Send a POST requst using cURL
* @param string $url to request
* @param array $post values to send
* @param array $options for cURL
* @return string
*/
function curl_post($url, array $post = NULL, array $options = array())
{
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
    curl_setopt_array($ch, ($options + $defaults));
    if( ! $result = curl_exec($ch))
    {
        trigger_error(curl_error($ch));
    }
    curl_close($ch);
    return $result;
} 
$url = "https://api.infobip.com/sms/1/text/single";
/*$headers = array(
  'accept' => 'application/json',
  'content-type' => 'application/json',
  'authorization' => 'Basic bmdveWE6UmVjeWNsZSM3Qmlu'
);*/
//$options = array('CURLOPT_HEADER'=>array('accept'=>'application/json'));
$message = curl_post($url, array('from'=>'PHP Code','to'=>'255787101808','text'=>'Hapa tuko pouwa')) or die("Could not complete connection");
echo $message;
?>