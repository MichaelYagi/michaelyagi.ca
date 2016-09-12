<?php

class Log {
	
	private $LogDir;
	private $LogFile;
	
	public function __construct($type) {
		$this->LogDir = "/var/www/michaelyagi.ca/application/logs/";
		
		switch ($type) {
			case "foodapilogin":
				$this->LogFile = $type.".log";
				break;
			default:
				$this->LogFile = "foodapilogin.log";
				break;
		}
		
	}
	
	public function write($message) {
		$filedirectory = $this->LogDir;
		if (!file_exists($filedirectory)) {
			if (!mkdir($filedirectory, 0777)) {
				//die('Failed to create directory:'.$filedirectory);
				$retval = 0;
				return $retval;
			}
		}
		
		$filename = $filedirectory."/".$this->LogFile;
		
		$message .= "\n\n".$message;
		
		try {
			file_put_contents($filename, $message, FILE_APPEND | LOCK_EX);
			$retval = 1;
		} catch(Exception $e) {
			$retval = 0;
		}
		
		return $retval;
		
	}
	
	// Function to get the client IP address
	public function get_client_ip() {
		$ipaddress = '';
		if (getenv('HTTP_CLIENT_IP'))
			$ipaddress = getenv('HTTP_CLIENT_IP');
		else if(getenv('HTTP_X_FORWARDED_FOR'))
			$ipaddress = getenv('HTTP_X_FORWARDED_FOR');
		else if(getenv('HTTP_X_FORWARDED'))
			$ipaddress = getenv('HTTP_X_FORWARDED');
		else if(getenv('HTTP_FORWARDED_FOR'))
			$ipaddress = getenv('HTTP_FORWARDED_FOR');
		else if(getenv('HTTP_FORWARDED'))
		   $ipaddress = getenv('HTTP_FORWARDED');
		else if(getenv('REMOTE_ADDR'))
			$ipaddress = getenv('REMOTE_ADDR');
		else
			$ipaddress = 'UNKNOWN';
		return $ipaddress;
	}
}
