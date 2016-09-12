<?php

class Application_Form_Install extends Zend_Form 
{
		
	public function __construct(array $params = array())
	{
		parent::__construct();
	}
	
	public function init() 
	{
	
		//Name of the form
		$this->setName('login')
			 ->setAction('/admin/login')
             ->setMethod('post');
		
		$username = new Zend_Form_Element_Text('username');
		$username->setLabel('Admin Username: *')
				->setRequired(true) 
			   ->addFilter('StripTags') 
			   ->addFilter('StringTrim') 
			   ->addValidator('NotEmpty')
			   ->removeDecorator('HtmlTag')
			   ->setAttribs(array('style' => 'margin-bottom:20px;'));
			   
		$password = new Zend_Form_Element_Password('password');
		$password->setLabel('Admin Password: *')
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
				->setAttribs(array('style' => 'margin-top:-75px;'));
		      
		//Submit button
		$submit = new Zend_Form_Element_Submit('submit'); 
		$submit->setLabel('Save')
				->removeDecorator('HtmlTag')
				->setAttrib('id', 'submitlogin')
				->setDescription('<br>')
				->setDecorators(array(
						'HtmlTag',
						array('Description', array('escape' => false)),
    					'ViewHelper',
    					'Errors',
    					array('Description', array('escape' => false)),
					    
				))
				->setAttribs(array('style' => 'margin-top:-40px;'));
		
		$this->addElements(array($username, $password, $confirmpassword, $submit));
	}
	
}