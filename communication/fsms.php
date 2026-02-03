<?php 
$postUrl = "https://api.infobip.com/sms/1/text/advanced";
//$postUrl="http://10.115.0.3/collegeplus/server/freeresponse.php";
// creating an object for sending SMS
$messageId="HK-Z009";
$to="255787101808";
$destination = array("messageId" => $messageId, 
            "to" => $to);
$from="255784808101";
$text="From Command Line Interface 2";
$message = array("from" => $from,
        "destinations" => array($destination),
        "text" => $text /*,
        "notifyUrl" => $notifyUrl,
        "notifyContentType" => $notifyContentType,
        "callbackData" => $callbackData*/);
$postData = array("messages" => array($message));
// encoding object
$postDataJson = json_encode($postData);
//Step One is Over
//Loading Step Two 
$ch = curl_init();
$header = array("Content-Type:application/json", "Accept:application/json", "Authorization: Basic bmdveWE6UmVjeWNsZSM3Qmlu");
//$header = array("Content-Type:application/json", "Accept:application/json");
// setting options
$username="ngoya";
$password="Recycle#7Bin";
curl_setopt($ch, CURLOPT_URL, $postUrl);
curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
curl_setopt($ch, CURLOPT_MAXREDIRS, 2);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postDataJson);
// response of the POST request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$responseBody = json_decode($response);
curl_close($ch);
if ($httpCode >= 200 && $httpCode < 300) {
		echo "<table>";
        $messages = $responseBody->messages;        
        foreach ($messages as $message) {
            echo "<tr>";
            echo "<td>" . $message->messageId . "</td>";
            echo "<td>" . $message->to . "</td>";
            echo "<td>" . $message->status->groupId . "</td>";
            echo "<td>" . $message->status->groupName . "</td>";
            echo "<td>" . $message->status->id . "</td>";
            echo "<td>" . $message->status->name . "</td>";
            echo "<td>" . $message->status->description . "</td>";
            echo "<td>" . $message->smsCount . "</td>";
            echo "</tr>";
        }
		echo "</table>";
}
echo "Returned http code is $httpCode";
?>