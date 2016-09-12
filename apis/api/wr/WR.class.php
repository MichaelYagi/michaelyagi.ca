<?php

class WebRequest {

	private $pdo_statement;
	private $sp_name;
	private $param_array;
	private $pdo;

	public function __construct() {

		try {
			$this->pdo = new PDO('mysql:host=localhost;port=3306;dbname=myagi', 'root','raspberry', array( PDO::ATTR_PERSISTENT => false));
		} catch (PDOException $e) {
			echo "Error!: " . $e->getMessage() . "<br/>";
			die();
		}
	}
	
	// Stored Procedure name
	public function setMethod($spName) {
		$this->sp_name = $spName;
		$this->param_array = array();
		
		return $this;
	}
	
	//Set parameters for stored procedure - return $this to chain methods together
	public function setParam($sp_param,$sp_value) {
		$this->param_array[":".$sp_param] = $sp_value;
		return $this;
	}
	
	//Execute stored procedure
	public function getData() {
		$sp_string = $this->setSpString();
		
		$this->pdo_statement = $this->pdo->prepare($sp_string);
		
		foreach($this->param_array as $key => &$val) {
			$this->pdo_statement->bindParam($key,$val);
		}
		
		try {
			$this->pdo_statement->execute();
			
			//Only fetch associative key/values
			$data = $this->pdo_statement->fetchAll(PDO::FETCH_ASSOC);
			
			if (sizeof($data) == 1) {
				$data = $data[0];
			}
		} catch (Exception $e) {
			$data[0] = 'Exception when executing stored procedure: '.$e->getMessage();
		}
		
		return $data;
	}
	
	//Build string for stored procedure call
	private function setSpString() {
		$sp_string = "CALL ".$this->sp_name."(";
		
		foreach(array_keys($this->param_array) as $key) {
			$sp_string .= $key.",";
		}
		
		$sp_string = rtrim($sp_string,",");
		$sp_string .= ")";
		
		return $sp_string;
	}
}
