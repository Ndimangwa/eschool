<?php 
$message = "Message Is Not Set";
if (isset($_REQUEST['sms'])) $message="We have received your message";
echo "Hello";
return $message;
?>