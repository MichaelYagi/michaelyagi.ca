<?php
class RestController extends Zend_Rest_Controller
{
	public function init()
    {
    	//Disable layout to output XML/JSON
    	$this->_helper->layout()->disableLayout();
    	//Turn off view
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function indexAction() 
    {
    	$arrList = array("message" => "listing resource");
        $this->getResponse()->setBody(Zend_Json::encode($arrList))
        					->setHttpResponseCode(200);
    }

    public function getAction() 
    {
    	$arrList = array("message" => "getting resource");
        $this->getResponse()->setBody(Zend_Json::encode($arrList))
        					->setHttpResponseCode(200);
    }

    public function postAction() 
    {
    	$arrList = array("message" => "creating resource");
        $this->getResponse()->setBody(Zend_Json::encode($arrList))
        					->setHttpResponseCode(200);
    }

    public function putAction() 
    {
    	$arrList = array("message" => "putting resource");
        $this->getResponse()->setBody(Zend_Json::encode($arrList))
        					->setHttpResponseCode(200);
    }

    public function deleteAction() 
    {
    	$arrList = array("message" => "deleting resource");
        $this->getResponse()->setBody(Zend_Json::encode($arrList))
        					->setHttpResponseCode(200);
    }

}
?>