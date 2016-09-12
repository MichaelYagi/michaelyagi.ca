<?php

class Application_Form_PostComment extends Zend_Form 
{

	private $_blogid;
		
	public function __construct(array $params = array())
	{
		$this->_blogid = $params['blogid'];
		parent::__construct();
	}
	
	public function init() 
	{
		$view = Zend_Layout::getMvcInstance()->getView();
	
		//Name of the form
		$this->setName('postcomment')
				->setAttrib('id','CommentForm_form')
			 ->setAction('/news/article/id/'.$this->_blogid)
             ->setMethod('post');
		
		$name = new Zend_Form_Element_Text('name');
		$name->setLabel('Name: *')
				->setRequired(true) 
			   	->addFilter('StripTags') 
			   	->addFilter('StringTrim') 
			   	->addValidator('NotEmpty')
			   	->removeDecorator('HtmlTag')
            	->setAttrib('size', '50')
			   	->setAttribs(array('style' => 'margin-bottom:20px;'));
			   
		$email = new Zend_Form_Element_Text('email');
		$email->setLabel('Email: *')
				->setRequired(true) 
			   	->addFilter('StripTags') 
			   	->addFilter('StringTrim') 
			   	->addValidator('NotEmpty')
			   	->removeDecorator('HtmlTag')
            	->setAttrib('size', '50')
			   	->setAttribs(array('style' => 'margin-bottom:20px;'))
			   	->addValidator('regex', false, array(
			   					"pattern" => "/^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+.[a-zA-Z0-9-.]+$/i", 
			   					"messages" => array(
			   									"regexInvalid" => "Invalid type given, value should be string, integer or float",
			   									"regexNotMatch" => "'%value%' is not a valid email address", 
			   									"regexErrorous"  => "There was an internal error while using the pattern '%pattern%'")));
		
		$comment = new Zend_Form_Element_Textarea('comment');
		$comment->setLabel('Comment: *')
				->setRequired(true) 
			   	->addFilter('StripTags') 
			   	->addFilter('StringTrim') 
			   	->addValidator('NotEmpty')
			   	->removeDecorator('HtmlTag')
			   	->setAttribs(array('style' => 'margin-bottom:20px;'));
		      
		$captcha = new Zend_Form_Element_Captcha('captcha', array(
                        'label' => "Verify: *",  
                        'captcha' => 'image',
                        'captchaOptions' => array(  
                                'captcha' => 'image',  
                                'font'=> APPLICATION_PATH.'/../public/css/fonts/prociono/Prociono.ttf',
                                'imgDir'=> APPLICATION_PATH.'/../public/img/captcha/',
                                'imgUrl'=> $view->baseUrl().'/img/captcha/',
                        'wordLen' => 5,
                        'dotNoiseLevel' => 40,
						'lineNoiseLevel' => 3,
						'required' => true,
                        'fontsize'=>20,
                        'height'=>45,
                        'width'=>143,
                        'timeout' => 300,)
                        ));        
        $captcha->setDecorators(array(
    					array('HtmlTag', array('id' => 'captcha-div')),
    					array('Label', array('class' => 'captcha-label')),
						'Captcha',   
						array('Errors', array('id' => 'captcha-error')),		
        				));
        				
		//Submit button
		$submit = new Zend_Form_Element_Submit('submit'); 
		$submit->setLabel('Submit')
				->setAttrib('id', 'submitbutton')
				->removeDecorator('HtmlTag')
				->removeDecorator('DtDdWrapper');
		
		$active = new Zend_Form_Element_Hidden('active');
		$active->setRequired(true) 
				->addFilter('StripTags') 
				->addFilter('StringTrim') 
				->addValidator('NotEmpty')
				->setValue('1')
				->removeDecorator('HtmlTag')
				->removeDecorator('DtDdWrapper');
		
		$this->addElements(array($name, $email, $comment, $captcha));
		
		$this->addDisplayGroup(array('captcha'), 'captcha-group');
		$captchaGroup = $this->getDisplayGroup('captcha-group');
		$captchaGroup->setDecorators(array(
							'FormElements',
							array('HtmlTag', array('tag'=>'div','id'=>'captcha')),
							));
		
		$this->addElements(array($active, $submit));
	}
	
}



