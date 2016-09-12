<?php

class Application_Form_Login extends Zend_Form 
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
		$username->setLabel('Username: *')
				->setRequired(true) 
			   ->addFilter('StripTags') 
			   ->addFilter('StringTrim') 
			   ->addValidator('NotEmpty')
			   ->removeDecorator('HtmlTag')
			   ->setAttribs(array('style' => 'margin-bottom:20px;'));
			   
		$password = new Zend_Form_Element_Password('password');
		$password->setLabel('Password: *')
				->setRequired(true) 
			   ->addFilter('StripTags') 
			   ->addFilter('StringTrim') 
			   ->addValidator('NotEmpty')
			   ->removeDecorator('HtmlTag')
			   ->setAttribs(array('style' => 'margin-bottom:20px;'));
		
		$remember = new Zend_Form_Element_Checkbox('rememberme');
		$remember->setLabel('Remember me')
				->setRequired(false)
			  	->removeDecorator('HtmlTag')
			   	->setCheckedValue(1)
			   	->setDescription('<br>')
				->setDecorators(array(
						'Label',
						array('Description', array('escape' => false)),
    					'ViewHelper',
    					'Errors',
					    'HtmlTag',
				))
				->setAttribs(array('style' => 'margin-top:-20px;'));
		      
		//Submit button
		$submit = new Zend_Form_Element_Submit('submit'); 
		$submit->setLabel('Sign In')
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
				->setAttribs(array('style' => 'margin-top:-15px;'));
		
		$this->addElements(array($username, $password, $remember, $submit));
	}
	
}