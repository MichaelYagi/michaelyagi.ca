<?php

class Application_Form_RecipeLogin extends Zend_Form 
{
		
	public function __construct(array $params = array())
	{
		parent::__construct();
	}
	
	public function init() 
	{
	
		//Name of the form
		$this->setName('login')
			 ->setAction('/food/login')
             ->setMethod('post');
		
		$username = new Zend_Form_Element_Text('username');
		$username->setLabel('Username: *')
				->setRequired(true) 
				->setOrder(1)
				->addFilter('StripTags') 
				->addFilter('StringTrim') 
				->addValidator('NotEmpty')
				->removeDecorator('HtmlTag')
				->setAttribs(array('style' => 'margin-bottom:20px;'));
			   
		$password = new Zend_Form_Element_Password('password');
		$password->setLabel('Password: *')
				->setRequired(true) 
				->setOrder(2)
				->addFilter('StripTags') 
				->addFilter('StringTrim') 
				->addValidator('NotEmpty')
				->removeDecorator('HtmlTag')
				->setAttribs(array('style' => 'margin-bottom:5px;'));
			   
		$link = $this->addElement(
									'hidden',
									'dummy',
									array(
										'order' => 3,
										'required' => false,
										'ignore' => true,
										'autoInsertNotEmptyValidator' => false,
										'description' => '<br><a href="/food/recover">I forgot my login!</a>',
										'decorators' => array(
											array(
												'Description', array('escape' => false, 'tag' => false)
											)
										)
									)
								);
		$link->dummy->clearValidators();
		
		$remember = new Zend_Form_Element_Checkbox('rememberme');
		$remember->setLabel('Remember me')
				->setRequired(false)
				->setOrder(4)
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
				->setOrder(5)
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
		
		$this->addElements(array($username, $password, $link, $remember, $submit));
	}
	
}