<?php

class Application_Form_Validate_PasswordValidator extends Zend_Validate_Abstract {
    const NOT_VALID = 'notValid';

    protected $_messageTemplates = array(
        self::NOT_VALID => 'Password does not match.'
    );

    public function isValid($value, $context = null) {
    	$value = (string)$value;
    	
    	if( !isset($context['newpassword']) )
    	{
    		return false;
    	}
    	if(!isset($context['confirmpassword']))
    	{
    		return false;
    	}
    	
    	if($value=="")
    	{
    		return false;
    	}
        
		$password = $context['newpassword'];
		$confirm = $context['confirmpassword'];
		
        if ($password != $confirm) 
        {
           	$this->_error(self::NOT_VALID);
           	return false;
        }
        else
        {
        	return true;
        }

        return false;
    }
}
