<?php

class Application_Model_User extends Zend_Db_Table_Abstract { 

	public function getAllUsers($order="user") {              
        try 
        {   
        	$db = Zend_Db_Table::getDefaultAdapter();
        	$data = $db->prepare("CALL commonGetAllUserInfoSP(?)");
        	$data->bindParam(1, $order);
        	$data->execute();
        	$result = $data->fetchAll();
        	$data->closeCursor();  
        	
        	/*
        	//Call stored procedure with pdo set in application config file
        	$data = $this->getDefaultAdapter()->prepare("CALL spGetBlogPost(?,?)");
        	$data->bindParam(1, $id);
        	$data->bindParam(2, $showActive);
        	$data->execute();
        	$result = $data->fetch();
        	$data->closeCursor();  
        	*/
               
        } catch (Exception $ex) { 
                throw $ex; 
        }       
                
        return $result; 
    }
    
    public function setUserStatus($id_list,$status) { 
    
    	try {   
        	if (!empty($id_list)) {
        		$db = Zend_Db_Table::getDefaultAdapter();
				$data = $db->prepare("CALL commonSetUserStatusSP(?,?)");
				$data->bindParam(1, $id_list);
				$data->bindParam(2, $status);
				$data->execute();
				$result = $data->fetchAll(PDO::FETCH_ASSOC);
				$data->closeCursor();  
				
				$retVal = $result[0]["ret_val"];
				
        	} else {
        		$retVal = -1;
        	}
               
               
        } catch (Exception $ex) { 
                throw $ex; 
        }       
        
        return $retVal;
    
    }
	
	public function setNewUser($user,$email,$password) { 
    
    	try {   
			$hash = $this->generateHash($password);
			$db = Zend_Db_Table::getDefaultAdapter();
			$data = $db->prepare("CALL commonCreateUserSP(?,?,?)");
			$data->bindParam(1, $user);
			$data->bindParam(2, $hash);
			$data->bindParam(3, $email);
			$data->execute();
			$result = $data->fetchAll(PDO::FETCH_ASSOC);
			$data->closeCursor();  
			
			$retVal = $result[0]["ret_val"];
               
        } catch (Exception $ex) { 
                throw $ex; 
        }       
        
        return $retVal;
    
    }
    
    public function setUserHash($id,$password) {
    	try {   
        	if (!empty($id)) {
        		$hash = $this->generateHash($password);
        		$db = Zend_Db_Table::getDefaultAdapter();
				$data = $db->prepare("CALL commonSetUserHashSP(?,?)");
				$data->bindParam(1, $id);
				$data->bindParam(2, $hash);
				$data->execute();
				$result = $data->fetchAll(PDO::FETCH_ASSOC);
				$data->closeCursor();  
				
				$retEmail = $result[0]["email"];
				
        	} else {
        		$retEmail = "";
        	}
               
               
        } catch (Exception $ex) { 
                throw $ex; 
        }       
        
        return $retEmail;
    }
    
    public function setUserEmail($uid,$password) {
    
    	$db = Zend_Db_Table::getDefaultAdapter();
		$data = $db->prepare("CALL commonSetUserEmailSP(?,?)");
		$data->bindParam(1, $uid);
		$data->bindParam(2, $password);
		$data->execute();
		$result = $data->fetchAll(PDO::FETCH_ASSOC);
		$data->closeCursor();  
		
		$ret_val = $result[0];
		
		return $ret_val;
		
	}
    
    public function getUserInfo($username) {
    
    	$db = Zend_Db_Table::getDefaultAdapter();
		$data = $db->prepare("CALL commonGetUserInfoByUserSP(?)");
		$data->bindParam(1, $username);
		$data->execute();
		$result = $data->fetchAll(PDO::FETCH_ASSOC);
		$data->closeCursor();  
		
		$user_info = $result[0];
		
		return $user_info;
		
	}
	
	public function getUserInfoById($uid) {
    
    	$db = Zend_Db_Table::getDefaultAdapter();
		$data = $db->prepare("CALL commonGetUserInfoByIdSP(?)");
		$data->bindParam(1, $uid);
		$data->execute();
		$result = $data->fetchAll(PDO::FETCH_ASSOC);
		$data->closeCursor();  
		
		$user_info = $result[0];
		
		return $user_info;
		
	}
	
	public function getUserId($user_email) {
		$db = Zend_Db_Table::getDefaultAdapter();
		$data = $db->prepare("CALL commonGetUserIdSP(?)");
		$data->bindParam(1, $user_email);
		$data->execute();
		$result = $data->fetchAll(PDO::FETCH_ASSOC);
		$data->closeCursor(); 
		$user_id = $result[0]["userid"];
		
		return $user_id;
	}
	
	public function resetPassword($email) {
		$retval = 0;
	
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$user_id = $this->getUserId($email);
		
		if ($user_id > 0) {
			$password = $this->generatePassword();
			$hash = $this->generateHash($password);
		
			$data = $db->prepare("CALL commonSetUserHashSP(?,?)");
			$data->bindParam(1, $user_id);
			$data->bindParam(2, $hash);
			$data->execute();
			$result = $data->fetchAll(PDO::FETCH_ASSOC);
			$data->closeCursor();  
			
			try {
                $mail = new Zend_Mail();
                $mail->setBodyText('Your password is: '.$password)
                        ->setFrom('myagi.developer@gmail.com', 'Michael Yagi')
                        ->addTo($email)
                        ->setSubject("Password reset for Yagi's Recipe Book")
                        ->send();
                $retval = 1;
			} catch(Exception $e) {
				$retval = 0;
			}

		} else {
			$retval = 0;
		}
		
		return $retval;
	}
	
	private function generatePassword($length = 5) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}
    
    private function generateHash($password) {
		// A higher "cost" is more secure but consumes more processing power
		$cost = 10;
		
		// Create a random salt
		$salt = strtr(base64_encode(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM)), '+', '.');
		
		// Prefix information about the hash so PHP knows how to verify it later.
		// "$2a$" Means we're using the Blowfish algorithm. The following two digits are the cost parameter.
		$salt = sprintf("$2a$%02d$", $cost) . $salt;
		
		// Hash the password with the salt
		$hash = crypt($password, $salt);
		
		return $hash;
	}

}