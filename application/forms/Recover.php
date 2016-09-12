<?php

class Application_Form_Recover extends Zend_Form {
		
	public function __construct(array $params = array()) {
		parent::__construct();
	}
	
	public function init() {
	
		//Name of the form
		$this->setName('recover')
			 ->setAction('/food/recover')
             ->setMethod('post');
             
        $email = new Zend_Form_Element_Text('email');
		$email->setLabel('Email')
				->setRequired(true) 
				->setAttrib('size', '50')
				->setOrder(1)
				->addFilter('StripTags') 
				->addFilter('StringTrim') 
				->addValidator('NotEmpty')
				->removeDecorator('HtmlTag')
				->setAttribs(array('style' => 'margin-bottom:20px;'));
				
		//Submit button
		$submit = new Zend_Form_Element_Submit('submit'); 
		$submit->setLabel('Submit')
				->removeDecorator('HtmlTag')
				->setOrder(405)
				->setAttrib('id', 'submitbutton')
				->setAttribs(array('style' => 'margin-left:-40px'));
	
		$this->addElements(array($email, $submit));
	}
	
}