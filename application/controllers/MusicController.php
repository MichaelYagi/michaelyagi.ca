<?php

class MusicController extends Zend_Controller_Action
{

	private $_language;
	private $_langSess;

    public function init()
    {
    	$this->_langSess = new Zend_Session_Namespace('language');
    	
    	$this->view->lang = $this->_langSess->langview;
    }

    public function indexAction()
    {
        // action body
		//$this->_helper->redirector('en', 'jrc');
		$this->_helper->redirector('jrc', 'music');
    }

	public function jrcAction()
    {
        // action body
        $mediaFiles = new Application_Model_FileManager();
        $trackArray = $mediaFiles->getJrcMedia();
        $translate = $this->_langSess->translate;
        
        $this->view->tracks = $trackArray;
        $this->view->langswitch = $translate->_('lang_switch');
        $this->view->heading_1 = $translate->_('jrc_page_title_1');
        $this->view->heading_2 = $translate->_('jrc_page_title_2');
        
        if($this->getRequest()->getParam('lang') != "")
        {
        	$this->_helper->redirector('jrc', 'music');
        }
    }
    
    public function cc2012Action()
    {
        // action body
        $ytLinkArray = array("http://www.youtube.com/watch?v=1Wxu2VEbpCw");
        $this->view->media = $ytLinkArray;
    }
}

