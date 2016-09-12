<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	protected function _initRestRoute()
	{
    	$this->bootstrap('frontController');
    	$frontController = Zend_Controller_Front::getInstance();
    	$restRoute = new Zend_Rest_Route($frontController, array() , array('default' => array('rest')));
    	$frontController->getRouter()->addRoute('rest', $restRoute);
	}

	protected function _initDoctype()
	{
		$this->bootstrap('view');
		$view = $this->getResource('view');
		$view->doctype('HTML5');
	}
	
	protected function _initConfig() 
    { 
        $config = new Zend_Config($this->getOptions(), true); 
        Zend_Registry::set('config', $config); 
    } 
    
    protected function _initTranslate() 
    {
    	$locale = new Zend_Locale();
    	$langSess = new Zend_Session_Namespace('language');

    	$request =  new Zend_Controller_Request_Http();                 
        $reqUri = $request->getRequestUri();            
    	$reqUriPieces = explode('/', $reqUri); 

		$language = 'en_CA';
		//Set the language for this controller with lang paramater
		if (array_key_exists(2, $reqUriPieces)&&$reqUriPieces[2] == "jrc"&&array_key_exists(3, $reqUriPieces)&&$reqUriPieces[3] == "lang")
		{ 
            if (array_key_exists(4, $reqUriPieces)&&$reqUriPieces[4] == "ja")  
            {
        		$language = 'ja'; 
        	}
        } 
        else if (isset($langSess->langview))
        {
        	$language = $langSess->langview;
        }
        
        $translate = new Zend_Translate('tmx', APPLICATION_PATH.'/xml/translation.xml', $language);    	
	
		
		$locale->setLocale($language);
    	Zend_Registry::set('locale', $locale);
    	Zend_Registry::set('language', $translate);
    	
        $langSess->translate = $translate;
        $langSess->langview = $language;
	}
	
}

