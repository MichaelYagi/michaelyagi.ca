<?php

class Application_Form_ViewLog extends Zend_Form 
{
	private $_type;
		
	public function __construct(array $params = array())
	{
		$this->_type = $params['type'];
		parent::__construct();
	}
	
	public function init() 
	{
	
		//Name of the form
		$this->setName('view'.$this->_type.'Log')
			 ->setAction('/admin/logs')
             ->setMethod('post');
			   			   
		$report = new Zend_Form_Element_Textarea($this->_type.'Log');
		$report->setLabel($this->_type.' Log: ')
				->setAttribs(array('style' => 'margin-left:-40px;',
									'disabled' => 'disabled'));

		//Clear button
		$clear = new Zend_Form_Element_Submit('clear'.$this->_type.'Log'); 
		$clear->setLabel('Clear '.$this->_type.' Log')
				->removeDecorator('HtmlTag')
				->setAttrib('id', 'clear'.$this->_type.'Button')
				->setAttribs(array('style' => 'margin-left:-40px;'));
		
		$this->addElements(array($report, $clear));
	}
	
}

