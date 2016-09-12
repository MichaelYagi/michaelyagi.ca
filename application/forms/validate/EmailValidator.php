<?php

class Application_Form_Validate_EmailValidator extends Zend_Validate_Abstract {
    const NOT_VALID = 'notValid';
    const LAST_EMPTY = 'lastempty';
    const FIRST_EMPTY = 'firstempty';
    const NAME_EMPTY = 'nameempty';

    protected $_messageTemplates = array(
        self::NOT_VALID => 'Email not valid.',
        self::LAST_EMPTY => 'Last Name empty.',
        self::FIRST_EMPTY => 'First Name empty.',
        self::NAME_EMPTY => '.'
    );

    public function isValid($value, $context = null) {
    	$value = (string)$value;
    	
    	if( !isset($context['first']) )
    	{
    		return false;
    	}
    	if(!isset($context['last']))
    	{
    		return false;
    	}
    	
    	if($value=="")
    	{
    		return false;
    	}
    	
    	$email = (string) $value;
        $emailArr = explode('@',$email);
        $localpart = $emailArr[0];
        $domain = $emailArr[1];
    	
    	$first = $context['first'];
    	$first = trim($first);
        $first = str_replace(" ","",$first);
        	
        $last = $context['last'];
    	$last = trim($last);
        $last = str_replace(" ","",$last);
        	
        $name = $first.".".$last;
        	        	
        if ($localpart != $name || $domain != $context['emailDomain']) 
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
