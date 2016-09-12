<?php

class SitedownController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
       	$this->view->headTitle(':Site Down');
    }

    public function indexAction()
    {
    	$siteoptions = new Application_Model_SiteOptions();
    	$sitemessage = $siteoptions->getSiteMessage();
    	$this->view->sitemessage = $sitemessage;
    }
    


}

