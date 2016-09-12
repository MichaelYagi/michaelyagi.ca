<?php

class Application_Form_NewPost extends Zend_Form 
{
		
	public function __construct(array $params = array())
	{
		parent::__construct();
	}
	
	public function init() 
	{
	
		//Name of the form
		$this->setName('newpost')
			 ->setAction('/admin/index')
             ->setMethod('post');
		
		$title = new Zend_Form_Element_Text('title');
		$title->setLabel('Title: *')
				->setRequired(true) 
			   ->addFilter('StripTags') 
			   ->addFilter('StringTrim') 
			   ->addValidator('NotEmpty')
            ->setAttrib('size', '50')
			   ->removeDecorator('HtmlTag')
			   ->setAttribs(array('style' => 'margin-bottom:20px;'));
			   
		$content = new Zend_Form_Element_Textarea('content');
		$content->setLabel('Content: *')
				->setRequired(true) 
			   ->addFilter('StripTags') 
			   ->addFilter('StringTrim') 
			   ->addValidator('NotEmpty')
			   ->removeDecorator('HtmlTag')
			   ->setAttribs(array('style' => 'margin-bottom:20px;'));
		
		$active = new Zend_Form_Element_Radio('active');
		$active->setRequired(true)
				->setLabel('Active: *')
				->removeDecorator('HtmlTag')
				->setMultiOptions(array('1'=>' Yes', '0'=>' No'))
				->setValue('1');
		      
		//Submit button
		$submit = new Zend_Form_Element_Submit('submit'); 
		$submit->setLabel('Post')
				->removeDecorator('HtmlTag')
				->setAttrib('id', 'submitbutton')
				->setAttribs(array('style' => 'margin-left:-40px;'));
		
		$this->addElements(array($title, $content, $active, $submit));
	}
	
}

