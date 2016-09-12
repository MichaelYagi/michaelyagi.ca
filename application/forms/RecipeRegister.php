<?php

class Application_Form_RecipeRegister extends Zend_Form 
{
		
	public function __construct(array $params = array())
	{
		parent::__construct();
	}
	
	public function init() 
	{
	
		//Name of the form
		$this->setName('register')
			 ->setAction('/food/register')
             ->setMethod('post');
		
		$username = new Zend_Form_Element_Text('username');
		$username->setLabel('Username: *')
				->setRequired(true) 
				->addFilter('StripTags') 
				->addFilter('StringTrim') 
				->addValidator('NotEmpty')
				->removeDecorator('HtmlTag')
				->setAttribs(array('style' => 'margin-bottom:20px;'));
				
		$email = new Zend_Form_Element_Text('email');
		$email->setLabel('Email: *')
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
			   
		$password_confirm = new Zend_Form_Element_Password('password_confirm');
		$password_confirm->setLabel('Re-enter Password: *')
						->setRequired(true) 
						->addFilter('StripTags') 
						->addFilter('StringTrim') 
						->addValidator('NotEmpty')
						->removeDecorator('HtmlTag')
						->setAttribs(array('style' => 'margin-bottom:20px;'));
		
		//Submit button
		$submit = new Zend_Form_Element_Submit('submit'); 
		$submit->setLabel('Register')
				->removeDecorator('HtmlTag')
				->setAttrib('id', 'submitregistration')
				->setDescription('<br>')
				->setDecorators(array(
						'HtmlTag',
						array('Description', array('escape' => false)),
    					'ViewHelper',
    					'Errors',
    					array('Description', array('escape' => false)),
					    
				))
				->setAttribs(array('style' => 'margin-top:-15px;'));
		
		$this->addElements(array($username, $email, $password, $password_confirm, $submit));
	}
	
}