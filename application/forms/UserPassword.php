<?php

class Application_Form_UserPassword extends Zend_Form {

	private $userid;
		
	public function __construct(array $params = array()) {
		parent::__construct();
		$this->userid = $params['userid'];
	}
	
	public function init() {
	
		//Name of the form
		$this->setName('password')
			 ->setAction('/food/account')
             ->setMethod('post');

		$password = new Zend_Form_Element_Password('password');
		$password->setLabel('New Password: *')
				->setRequired(true) 
				->addFilter('StripTags') 
				->addFilter('StringTrim') 
				->addValidator('NotEmpty')
				->removeDecorator('HtmlTag')
				->setAttribs(array('style' => 'margin-bottom:20px;'));
			   
		$password_confirm = new Zend_Form_Element_Password('password_confirm');
		$password_confirm->setLabel('Re-enter New Password: *')
						->setRequired(true) 
						->addFilter('StripTags') 
						->addFilter('StringTrim') 
						->addValidator('NotEmpty')
						->removeDecorator('HtmlTag')
						->setAttribs(array('style' => 'margin-bottom:20px;'));
						
		//Submit button
		$submit = new Zend_Form_Element_Submit('submit_password'); 
		$submit->setLabel('Change Password')
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
		
		$this->addElements(array($password, $password_confirm, $submit));
	}
}