<?php

class Application_Form_ChangePassword extends Zend_Form 
{
		
	public function __construct(array $params = array())
	{
		parent::__construct();
	}
	
	public function init() 
	{
	
		//Name of the form
		$this->setName('changepassword')
			 ->setAction('/admin/account')
             ->setMethod('post');
		
		$oldpassword = new Zend_Form_Element_Password('oldpassword');
		$oldpassword->setLabel('Old Password: *')
				->setRequired(true) 
			   	->addFilter('StripTags') 
			   	->addFilter('StringTrim') 
			   	->addValidator('NotEmpty')
			   	->removeDecorator('HtmlTag')
			   	->setAttribs(array('style' => 'margin-bottom:20px;'));
			   
		$newpassword = new Zend_Form_Element_Password('newpassword');
		$newpassword->setLabel('New Password: *')
				->setRequired(true) 
			   	->addFilter('StripTags') 
			   	->addFilter('StringTrim') 
			   	->addValidator('NotEmpty')
			   	->removeDecorator('HtmlTag')
			   	->setAttribs(array('style' => 'margin-bottom:20px;'));
		
		$confirmpassword = new Zend_Form_Element_Password('confirmpassword');
		$confirmpassword->setLabel('Confirm Password: *')
				->setRequired(true) 
				->addFilter('StripTags') 
			   	->addFilter('StringTrim')
			   	->addValidator('NotEmpty')
			   	->removeDecorator('HtmlTag')
			   	->removeDecorator('DtDdWrapper')
			   	->setDescription('<br>')
				->setDecorators(array(
						'Label',
						array('Description', array('escape' => false)),
    					'ViewHelper',
    					'Errors',
					    'HtmlTag',
				))
				->setAttribs(array('style' => 'margin-top:-35px;'));
		      
		//Submit button
		$submit = new Zend_Form_Element_Submit('submit'); 
		$submit->setLabel('Change Password')
				->removeDecorator('HtmlTag')
				->removeDecorator('DtDdWrapper')
				->setAttrib('id', 'submitlogin')
				->setDescription('<br>')
				->setDecorators(array(
						'HtmlTag',
						array('Description', array('escape' => false)),
    					'ViewHelper',
    					'Errors',
    					array('Description', array('escape' => false)),
					    
				))
				->setAttribs(array('style' => 'margin-top:-15px;'));
		
		$this->addElements(array($oldpassword, $newpassword, $confirmpassword, $submit));
	}
	
}