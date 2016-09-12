<?php
class Plugins_ConditionalRoute extends Zend_Controller_Plugin_Abstract
{
 
	public function routeShutdown(Zend_Controller_Request_Abstract $request)
    {
    	
		$verify = new Application_Model_AccountOptions();
        $siteVerified = $verify->verifyInstallation();
                
        //Verify that all the database tables are in place
        if (!$siteVerified) 
        {	
        	$request->setActionName('index');
        	$request->setModuleName('default');
        	$request->setControllerName('install');
        } else {
        	$site = new Application_Model_SiteOptions;
    		$sitestatus = $site->getSiteStatus();

        	if($sitestatus==0&&($request->getControllerName()!='install'&&$request->getControllerName()!='admin'&&$request->getControllerName()!='feed'&&$request->getControllerName()!='music')) {
    			
    			//If the site is down and not an admin, show the maintenance page
    			if (!Zend_Auth::getInstance()->hasIdentity())
        		{

        			$request->setActionName('index');
        			$request->setModuleName('default');
        			$request->setControllerName('sitedown');
        		}
        	} else {
        		//Prevent from going to install page if the site is verified
        		if ($sitestatus==1&&$request->getControllerName()=='install') {
        			$request->setActionName('about');
        			$request->setModuleName('default');
        			$request->setControllerName('index');
        		}

        	}
        }
    } 


}
