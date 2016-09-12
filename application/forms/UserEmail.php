<?php

class Application_Form_UserEmail extends Zend_Form {

	private $userid;
		
	public function __construct(array $params = array()) {
		parent::__construct();
		$this->userid = $params['userid'];
	}
	
	public function init() {
	
		//Name of the form
		$this->setName('email')
			 ->setAction('/food/account')
             ->setMethod('post');
				
		$email = new Zend_Form_Element_Text('email');
		$email->setLabel('Email: *')
				->setRequired(true) 
				->setAttrib('size', '50')
				->addFilter('StripTags') 
				->addFilter('StringTrim') 
				->addValidator('NotEmpty')
				->removeDecorator('HtmlTag')
				->setAttribs(array('style' => 'margin-bottom:20px;'));
		
		//Submit button
		$submit = new Zend_Form_Element_Submit('submit_email'); 
		$submit->setLabel('Change Email')
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
		
		$this->addElements(array($email, $submit));
	}
	
}