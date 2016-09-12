<?php

class Application_Model_AccountOptions extends Zend_Db_Table_Abstract 
{ 

	public function verifyInstallation()
	{
	
		//Check if database adapter exists
    	try
    	{
			$db = $this->getDefaultAdapter(); //throws exception
       		if ($db == null) return FALSE;
       		
    	} catch(Exception $e) {
       		return FALSE;
    	}
    	    	
    	//Check if admin table exists
    	try
    	{
    		$result = $db->describeTable('admin'); //throws exception
       		if (empty($result)) {
       			return FALSE;
       		}
    	} catch(Exception $e) {
       		return FALSE;
    	}
    	
    	$result = $db->fetchRow('SELECT COUNT(*) as count FROM admin');
    	if ($result['count'] == null || $result['count'] == 0) 
    	{
    		return FALSE;
    	} else {
    		return TRUE;
    	}
    	    	
    	return TRUE;
	
	}
	
	public function dbAdapterSet() 
	{
	
		try
    	{
			$db = $this->getDefaultAdapter(); //throws exception
       		if ($db == null) return FALSE;
    	} catch(Exception $e) {
       		return FALSE;
    	}
    	
    	return TRUE;
	}
	
	public function adminTableExists() 
	{	
		try
    	{
    		$db = $this->getDefaultAdapter();
    		$result = $db->describeTable('admin'); //throws exception
       		if (empty($result)) return FALSE;
    	} catch(Exception $e) {
       		return FALSE;
    	}
    	
    	return TRUE;
	}
	
	public function setupTables()
	{
		$retVal = FALSE;

        try {
            $sqlDir = APPLICATION_PATH . '/MySQL/';
            $sqlFiles = scandir($sqlDir,1);
            if (count($sqlFiles) > 0) {
                $mostRecent = $sqlFiles[0];
                $db = $this->getDefaultAdapter();
                $db->beginTransaction();
                $sql = file_get_contents($sqlDir.$mostRecent);
                $db->query($sql);
                $db->commit();
                $retVal = TRUE;
            }
		} catch(Exception $e) {
			$bootstrap = Zend_Controller_Front::getInstance()->getParam('bootstrap');
        	$phpSetting = $bootstrap->getOption('phpSettings');
        	$displayErrors = $phpSetting['display_errors'];
        	if ($displayErrors) {
        		echo $e->getMessage();
            }
        	$retVal = FALSE;
        }

        return $retVal;
	}
	
	public function createAdmin($user=null,$password=null)
	{
		try
		{
			$tools = new Application_Model_EncryptUtility();
        	
        	$db = Zend_Db_Table::getDefaultAdapter();
		
			if (!empty($user)&&!empty($password))
        	{
		
				$encryptedPassword = trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $tools->getStaticSalt(), $password, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))));
        		        		
        		$success = $db->insert("admin", array("username"=>$user,"password"=>SHA1($password.$encryptedPassword)));
        		
        		$retVal = $success;
        		
        	}
        } catch (Exception $ex) { 
                //throw $ex; 
                $retVal = -1;
        }       
        
        return $retVal;
	}
	
	public function setChangePassword($user=null,$oldpassword=null,$newpassword=null) 
    {              
        try 
        {  
        	$tools = new Application_Model_EncryptUtility();
        	
        	$db = Zend_Db_Table::getDefaultAdapter();
        	
        	if (!empty($user)&&!empty($oldpassword)&&!empty($newpassword))
        	{
        		$encryptedOldPass =  trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $tools->getStaticSalt(), $oldpassword, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))));
				$encryptedNewPass =  trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $tools->getStaticSalt(), $newpassword, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))));
        		
        		$select = $db->select()
        						->from('admin',
        								array('count' => "COUNT('password')"))
        						->where('username = ?', $user)
        						->where('password = ?', SHA1($oldpassword.$encryptedOldPass));	
        		
        		$result = $db->fetchRow($select);

        		if ($result['count'] == 1)
        		{												
        			$success = $db->update("admin", array("password"=>SHA1($newpassword.$encryptedNewPass)), $db->quoteInto(" username = ? " ,$user));
        			if ($success == 1 || $success == 0)
        			{
        				$retVal = 1;
        			}
        			else
        			{
        				$retVal = -1;
        			}
        		}
        		else
        		{
        			//incorrect old password
        			$retVal = -2;
        		}
        	} else {
        		$retVal = -1;
        	}
            
        } catch (Exception $ex) { 
                throw $ex; 
        }       
        
        return $retVal;
    } 
    
}
