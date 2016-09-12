<?php
class Plugins_Redirect extends Zend_Controller_Plugin_Abstract
{
 
	protected function _redirect($request, $controller, $action)
	{
		if ($request->getControllerName() == $controller
			&& $request->getActionName()  == $action)
		{
			return TRUE;
		}
 
		$url = Zend_Controller_Front::getInstance()->getBaseUrl(); 
		$url .= '/' . $controller
			 . '/' . $action;
 
 
	   return $this->_response->setRedirect($url);
	}


}