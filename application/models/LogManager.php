<?php

class Application_Model_LogManager extends Zend_Db_Table_Abstract 
{ 
        
    public function getApplicationLog() 
    {             
        try 
        {   
        	$filename = APPLICATION_PATH.'/logs/applicationexception.log';
			$logStr = "";
			if (file_exists($filename) && is_readable ($filename)) 
			{
				$fh = fopen($filename, "r");
				while (!feof($fh)) 
				{
   					$line = fgets($fh);
   					$logStr .= $line;
				}
				fclose($fh);
			}
        	
			
               
        } catch (Exception $ex) { 
                throw $ex; 
        }       
        
        return $logStr; 
    }
    
    public function getLoginLog() 
    {              
        try 
        {   
        	$filename = APPLICATION_PATH.'/logs/adminactivity.log';
			$logStr = "";
			if (file_exists($filename) && is_readable ($filename)) 
			{
				$fh = fopen($filename, "r");
				while (!feof($fh)) 
				{
   					$line = fgets($fh);
   					$logStr .= $line;
				}
				fclose($fh);
			}
        	
			
               
        } catch (Exception $ex) { 
                throw $ex; 
        }       
        
        return $logStr; 
    }
    
    public function getFoodLoginLog() 
    {              
        try 
        {   
        	$filename = APPLICATION_PATH.'/logs/foodactivity.log';
			$logStr = "";
			if (file_exists($filename) && is_readable ($filename)) 
			{
				$fh = fopen($filename, "r");
				while (!feof($fh)) 
				{
   					$line = fgets($fh);
   					$logStr .= $line;
				}
				fclose($fh);
			}
        	
			
               
        } catch (Exception $ex) { 
                throw $ex; 
        }       
        
        return $logStr; 
    }
    
    public function getApiLoginLog() 
    {              
        try 
        {   
        	$filename = APPLICATION_PATH.'/logs/foodapilogin.log';
			$logStr = "";
			if (file_exists($filename) && is_readable ($filename)) 
			{
				$fh = fopen($filename, "r");
				while (!feof($fh)) 
				{
   					$line = fgets($fh);
   					$logStr .= $line;
				}
				fclose($fh);
			}
        	
			
               
        } catch (Exception $ex) { 
                throw $ex; 
        }       
        
        return $logStr; 
    }
    
    public function setClearApplicationLog()
    {
    	try 
        {   
        	$filename = APPLICATION_PATH.'/logs/applicationexception.log';
			if (file_exists($filename) && is_readable ($filename)) 
			{
				file_put_contents($filename,"");
				$logStr = "";
			}
        } catch (Exception $ex) { 
                throw $ex; 
        }       
        
        return $logStr; 
    }
    
    public function setClearLoginLog()
    {
    	try 
        {   
        	$filename = APPLICATION_PATH.'/logs/adminactivity.log';
			$logStr = "";
			if (file_exists($filename) && is_readable ($filename)) 
			{
				file_put_contents($filename,"");
				$logStr = "";
			}
        } catch (Exception $ex) { 
                throw $ex; 
        }       
        
        return $logStr; 
    }
    
    public function setClearFoodLoginLog()
    {
    	try 
        {   
        	$filename = APPLICATION_PATH.'/logs/foodactivity.log';
			$logStr = "";
			if (file_exists($filename) && is_readable ($filename)) 
			{
				file_put_contents($filename,"");
				$logStr = "";
			}
        } catch (Exception $ex) { 
                throw $ex; 
        }       
        
        return $logStr; 
    }
    
    public function setClearApiLoginLog()
    {
    	try 
        {   
        	$filename = APPLICATION_PATH.'/logs/foodapilogin.log';
			$logStr = "";
			if (file_exists($filename) && is_readable ($filename)) 
			{
				file_put_contents($filename,"");
				$logStr = "";
			}
        } catch (Exception $ex) { 
                throw $ex; 
        }       
        
        return $logStr; 
    }
}