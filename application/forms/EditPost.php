<?php

class Application_Form_EditPost extends Zend_Form 
{
		
	private $_blogid;
		
	public function __construct(array $params = array())
	{
		$this->_blogid = $params['blogid'];
		parent::__construct();
	}
	
	public function init() 
	{
	
		//Name of the form
		$this->setName('editpost')
			 ->setAction('/admin/editarticle/id/'.$this->_blogid)
             ->setMethod('post');
		
		$title = new Zend_Form_Element_Text('title');
		$title->setLabel('Title: *')
				->setRequired(true) 
			   ->addValidator('NotEmpty')
			   ->removeDecorator('HtmlTag')
            ->setAttrib('size', '50')
			   ->setAttribs(array('style' => 'margin-bottom:20px;'));
			   
		$content = new Zend_Form_Element_Textarea('content');
		$content->setLabel('Content: *')
				->setRequired(true)  
			   ->addValidator('NotEmpty')
			   ->removeDecorator('HtmlTag')
			   ->setAttribs(array('style' => 'margin-bottom:20px;'));
		
		$active = new Zend_Form_Element_Radio('active');
		$active->setRequired(true)
				->setLabel('Active: *')
				->removeDecorator('HtmlTag')
				->setMultiOptions(array('1'=>' Yes', '0'=>' No'));
		      
		//Submit button
		$submit = new Zend_Form_Element_Submit('submit'); 
		$submit->setLabel('Post')
				->removeDecorator('HtmlTag')
				->setAttrib('id', 'submitbutton')
				->setAttribs(array('style' => 'margin-left:-40px'));
		
		$this->addElements(array($title, $content, $active, $submit));
	}
	
}

