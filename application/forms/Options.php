<?php

class Application_Form_Options extends Zend_Form 
{
		
	public function __construct(array $params = array())
	{
		parent::__construct();
	}
	
	public function init() 
	{
		$siteOptions = new Application_Model_SiteOptions();
		$siteBackgrounds = $siteOptions->getSiteBackgrounds();
	
		$view = Zend_Layout::getMvcInstance()->getView();
  		$arrRadio = array();
  		foreach($siteBackgrounds as $value)
  		{
  			$arrRadio[$value->name]='&nbsp;&nbsp;<img class="style1" width="50" height="50" src="'.$view->baseUrl().$value->url.'" />&nbsp;&nbsp;&nbsp;';
  		}
			
		//Name of the form
		$this->setName('newpost')
			 ->setAction('/admin/options')
             ->setMethod('post');
             
		$sitestatus = new Zend_Form_Element_Radio('sitestatus');
		$sitestatus->setRequired(true)
				->setLabel('Site Up: *')
				->removeDecorator('HtmlTag')
				->setMultiOptions(array('1'=>' Yes', '0'=>' No'))
				->setValue('1')
				->setSeparator(' ')
				->setDescription('<br>')
				->setDecorators(array(
    					'ViewHelper',
					    array('Description', array('escape' => false)),
    					'Errors',
					    'HtmlTag',
    					'Label',
				));
				
		$sitemessage = new Zend_Form_Element_Text('sitemessage');
		$sitemessage->setLabel('Site Message: ')
			   ->addFilter('StripTags') 
			   ->addFilter('StringTrim') 
			   ->addValidator('NotEmpty')
			   ->removeDecorator('HtmlTag')
            ->setAttrib('size', '50')
			   ->setAttribs(array('style' => 'margin-bottom:20px;'));
		
		$cssbg = new Zend_Form_Element_Radio('cssbg');
		$cssbg->setRequired(true)
				->setLabel('Site Background: *')
				->removeDecorator('HtmlTag')
				->setMultiOptions($arrRadio)
				->setValue('1')
				->setAttrib('escape', false)
				->setSeparator(' ')
				->setDescription('<br>')
				->setDecorators(array(
					    array('Description', array('escape' => false)),
					    'Label',
					    'ViewHelper',
    					'Errors',
					    'HtmlTag'
				))
				->setAttribs(array('style' => 'margin-bottom:40px;padding-top:10px;'));
		      
		//Submit button
		$submit = new Zend_Form_Element_Submit('submit'); 
		$submit->setLabel('Save')
				->removeDecorator('HtmlTag')
				->setAttrib('id', 'submitbutton')
				->setAttribs(array('style' => 'margin-left:-40px;'));
		
		$this->addElements(array($sitestatus, $sitemessage, $cssbg, $submit));
	}
	
}

