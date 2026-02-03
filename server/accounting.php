<?php
/* class Date need to be included 
  class ContextPosition need to be included
  include on the calling application
*/ 
class Accounting extends Object	{
	private $logId;
	private $date;
	private $username;
	private $contextPosition;
	private $target;
	private $database;
	private $conn;
	public function __construct($database, $logId, $conn)	{
		$this->setMe($database, $logId, $conn);
	}
	public function setMe($database, $logId, $conn)	{
		$query="SELECT logId, logName FROM systemlogs WHERE logId='$logId'";
		$result = mysql_db_query($database, $logId, $conn) or $this->throwMe("Can not fetch log Information");
		if (mysql_num_rows($result) != 1) $this->throwMe("Duplicate or No record found");
		list($logId, $logString)=mysql_fetch_row($result);
		$this->logId=$logId;
		/* logString 2014:11:26:08:11:56%ndimangwa%9%{[system|valentina}  note 9 in this example is the ContextPosition id*/
		$str1 = explode("%", $logString);
		$this->date = new Date($str1[0]);
		$this->username = $str1[1];
		$this->contextPosition = new ContextPosition($database, $str1[2], $conn);
		$this->target = $str1[3];
		$this->database = $database;
		$this->conn = $conn;
	}
	final public static function addLog($config, $datestring, $username, $op, $target)	{
		include($config);
		$conn=mysql_connect($hostname, $user, $pass) or die("Could not connect to a database server");
		$contextPositionId = ContextPosition::getContextIdFromName($database, $op, $conn);
		/* op example system_login */
		$insertString = $datestring."%".$username."%".$contextPositionId."%".$target;
		$insertString = mysql_real_escape_string($insertString);
		$query="INSERT INTO systemlogs (logName) VALUES ('$insertString')";
		$result = mysql_db_query($database, $query, $conn) or die("Could not add log ".mysql_error());
		mysql_close($conn);
	}
	public function getLogId()	{ return $this->logId; }
	public function getUsername()	{ return $this->username; }
	public function getContextPosition()	{ return $this->contextPosition; }
	public function getTarget()	{ return $this->target; }
	public function reload()	{ $this->setMe($this->database, $this->logId, $this->conn); }
	protected function getProperties()	{}
	public function searchText($text)	{}
	public function searchMatrix($matrix1)	{ return $matrix1; }
	public function commitUpdate()	{}
	public function commitDelete()	{}
	public function getClassName() { return "System Logs"; }
	protected function getAdvancedProperties()	{}
	public function processCSV($csvProcessor1)	{}
	public function cloneMe($updateDataArray)	{}
	public function debug()	{}
}
?>
