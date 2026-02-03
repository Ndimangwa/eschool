<?php 
	/*Expects __uname, __upwd, if code == 0 then it is okay, optional message
		return <root><code>0</code><message></message></root>
	*/
	require_once("../class/system.php");
	if (! (isset($_POST['__uname']) && isset($_POST['__upwd']))) die('<root><code>1</code><message>Required Parameters were not supplied</message></root>');
	echo "<root><code>0</code><message>Successful Logged In</message></root>";
?>