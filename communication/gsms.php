<?php
class infoBipSMS {
/*************** http://www.infobip.com/messaging/connectivity/apis ************************/
 // InfoBip account credentials
	private $infobip_username = '';
	private $infobip_password = '';

 /**
 * @var string InfoBip server URI
 *
 */
	var $nx_uri = 'http://api.infobip.com/api/v3/sendsms/xml';
 
	/*function infoBipSMS ($infobip_username, $infobip_password) {
		$this->infobip_username = $infobip_username;
		$this->infobip_password = $infobip_password;
	}*/
	public function __construct($infobip_username, $infobip_password) {
		$this->infobip_username = $infobip_username;
		$this->infobip_password = $infobip_password;
	}
 
 /**
 * @return: 
  Status    Vale Description
  AUTH_FAILED  - 1  Invalid username or password
  XML_ERROR   - 2  Incorrect XML format
  NOT_ENOUGH_CREDITS - 3  Not enough credits in user account
  NO_RECIPIENTS  - 4  No good recipients
  GENERAL_ERROR  - 5  Error in processing your request
  SEND_OK   > 0  Number of recipients to which message was sent
 *
 */
	function send_sms_infobip($sender_no_with_plus, $recipient_no_with_plus, $sms_body, $msg_id="")
	{
	if(substr($sender_no_with_plus,0,1)!="+")
		$sender_no_with_plus="+".$sender_no_with_plus;
	if(substr($recipient_no_with_plus,0,1)!="+")
		$recipient_no_with_plus="+".$recipient_no_with_plus;
	$msg_id=($msg_id=="")?time():$msg_id;
  //  BUSYBEE's POST URL 
  //$postUrl  = "http://193.105.74.59/api/sendsms/xml";
	$postUrl  = "http://api.infobip.com/api/v3/sendsms/xml";
  
  //  XML-formatted data 
	$infobip_username=$this->infobip_username;
	$infobip_password=$this->infobip_password;
	$no_of_sms_will_send=ceil(strlen($sms_body)/160);
	for($i=0;$i<$no_of_sms_will_send;$i++)
	{
		$sms_part=substr($sms_body,0,160);
		if(strlen($sms_body)>160)
			$sms_body=substr($sms_body,160);
		$xmlString = "
   <SMS> 
    <authentification> 
    <username>$infobip_username</username> 
    <password>$infobip_password</password> 
    </authentification> 
    <message> 
    <sender>$sender_no_with_plus</sender> 
    <text>$sms_part</text> 
    <flash></flash>
    <type></type> 
    <wapurl></wapurl> 
    <binary></binary> 
    <datacoding></datacoding> 
    <esmclass></esmclass> 
    <srcton></srcton> 
    <srcnpi></srcnpi> 
    <destton></destton> 
    <destnpi></destnpi> 
    <ValidityPeriod>23:59</ValidityPeriod> 
    </message> 
    <recipients> 
    <gsm messageId=\"$msg_id\">$recipient_no_with_plus</gsm> 
    </recipients> 
   </SMS>";
   
   //echo $xmlString;
    
   //  previously formatted XML data becomes value of "XML" POST variable 
		$fields  = "XML=" . urlencode($xmlString); 
    
   //  in this example, POST request was made using PHP's CURL 
		$ch  = curl_init(); 
		curl_setopt($ch,  CURLOPT_URL, $postUrl); 
		curl_setopt($ch, CURLOPT_POST, 1); 
		curl_setopt($ch, CURLOPT_POSTFIELDS,  $fields); 
    
   //  response of the POST request 
		$response  = curl_exec($ch);
		curl_close($ch); 
	}
  
  //  write out the response 
	return $response;
 
	}
}
 if (isset($_REQUEST['btnsubmit']))	{
	 $obj1 = new infoBipSMS($_REQUEST['username'], $_REQUEST['password']);
	 $obj1->send_sms_infobip($_REQUEST['sender_no'], $_REQUEST['recipient_no'], $_REQUEST['sms_body'], "18");
 } else {
?>

<form method="post" action="<?= $_SERVER['PHP_SELF'] ?>">
  Infobip Username: <input name="username" type="text" value="user-name"><br/>
  Infobip Password: <input name="password" type="text" value="password"><br/><br /><br />

  Sender No: <input name="sender_no" type="text" value="+my-sender-no"><br/>
  Recipient No: <input type="text" name="recipient_no" value="+recipient-no"><br/>
  SMS Text: <input type="text" name="sms_body">
  <input type="submit" name="btnsubmit" value="Send SMS">

</form>
<?php 
 }
?>


