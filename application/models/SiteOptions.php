<?php

class Application_Model_SiteOptions extends Zend_Db_Table_Abstract 
{ 
    
    public function getSiteStatus() 
    {              
        try 
        {   
        	$config = new Zend_Config_Xml(APPLICATION_PATH.'/xml/siteoptions.xml', 'siteoptions');
        	$result = $config->sitestatus->active;
               
        } catch (Exception $ex) { 
                throw $ex; 
        }       

        return $result; 
    }
    
    public function setSiteStatus($value=1) 
    {              
        try 
        {  
        	$config = new Zend_Config_Xml(APPLICATION_PATH.'/xml/siteoptions.xml', 'siteoptions', array('skipExtends'=>true,'allowModifications'=>true));
			$config->sitestatus->active = $value;

        	$writer = new Zend_Config_Writer_Xml(array('config'=>$config,'filename'=>APPLICATION_PATH.'/xml/siteoptions.xml'));
  			$writer->write();
        
        	$retVal = 1;
               
        } catch (Exception $ex) { 
                $retVal = -1;
        }       
        
        return $retVal;
    }
    
    public function getSiteMessage() 
    {              
        try 
        {   
        	$config = new Zend_Config_Xml(APPLICATION_PATH.'/xml/siteoptions.xml', 'siteoptions');
        	$result = $config->sitemessage->message;
               
        } catch (Exception $ex) { 
                throw $ex; 
        }       
        
        return $result; 
    }
    
    public function setSiteMessage($message=null) 
    {              
        try 
        {  
        	$config = new Zend_Config_Xml(APPLICATION_PATH.'/xml/siteoptions.xml', 'siteoptions', array('skipExtends'=>true,'allowModifications'=>true));
			$config->sitemessage->message = $message;

        	$writer = new Zend_Config_Writer_Xml(array('config'=>$config,'filename'=>APPLICATION_PATH.'/xml/siteoptions.xml'));
  			$writer->write();
        
        	$retVal = 1;
               
        } catch (Exception $ex) { 
                $retVal = -1;
        }       
        
        return $retVal;
    }
    
    public function getSiteBackgrounds()
	{
		$objBackgrounds = null;
		try
		{
			$objBackgrounds = new Zend_Config_Xml(APPLICATION_PATH.'/xml/background.xml', 'bodybackground');
        } catch (Exception $ex) { 
                //exception, keep $background string empty;
        }      
        
        return $objBackgrounds;
	}
    
    public function getSiteBackground()
	{
		$objBackground = null;
		try
		{
			$config = new Zend_Config_Xml(APPLICATION_PATH.'/xml/background.xml', 'bodybackground');
	  		foreach($config as $value)
	  		{
  				if($value->active)
  				{
  					$objBackground = $value;
  					break;
  				}
  			}
  		} catch (Exception $ex) { 
                //exception, keep $background string empty;
        }      
        
        return $objBackground;
	}
	
	public function setSiteBackground($bgname=null)
	{
		try 
        {  
        	$config = new Zend_Config_Xml(APPLICATION_PATH.'/xml/background.xml', 'bodybackground', array('skipExtends'=>true,'allowModifications'=>true));
  			foreach($config as $value)
  			{
  				if ($value->name == $bgname)
  				{
  					$value->active = 1;
  				}
  				else
  				{
  					$value->active = 0;
  				}
  			}

        	$writer = new Zend_Config_Writer_Xml(array('config'=>$config,'filename'=>APPLICATION_PATH.'/xml/background.xml'));
  			$writer->write();
        
        	$retVal = 1;
               
        } catch (Exception $ex) { 
                $retVal = -1;
        }       
        
        return $retVal;
	}
    
}
