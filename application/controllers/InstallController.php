<?php

class InstallController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        $this->view->headTitle(':Installation');
    }

    public function indexAction()
    {
        // action body
        Zend_Auth::getInstance()->clearIdentity();
		Zend_Session::forgetMe();
        
        $verify = new Application_Model_AccountOptions();
        $dbAdapterSet = $verify->dbAdapterSet();
        $adminTableExists = $verify->adminTableExists();
        
        if ($dbAdapterSet) 
        {
        	$tablesSet = $verify->setupTables();
        	$this->view->tablesSet = $tablesSet;
        	$this->view->adminTableExists = $adminTableExists;
        	
        } 
        
        $this->view->adapterSet = $dbAdapterSet;
        
        $form = new Application_Form_Install();
        $this->view->installform = $form;
        
        if ($this->getRequest()->isPost()) 
		{
			//Form data
			$formData = $this->getRequest()->getPost(); 
            
			//If the form data is valid
			if ($form->isValid($formData)) 
			{ 
				if ($formData['password']!=$formData['confirmpassword'])
				{
					$form->getElement('password')->addError("Passwords do not match");
					$form->getElement('password')->setAttrib('class','error');
					$form->getElement('confirmpassword')->addError("Passwords do not match");
					$form->getElement('confirmpassword')->setAttrib('class','error');

				} else if(isset($formData["submit"]) && trim($formData["submit"]) != "") { 
				
					$username = $formData['username'];
					$password = $formData['password'];
					
					$retID = $verify->createAdmin($username,$password);
					
					if(is_numeric($retID))
					{
						if ($retID > 0) {
							$this->_redirect('/about');
						} else {
							//Error
							$this->view->err = 1; 
							$form->populate($formData);
						}
					} else {
						//Error
						$this->view->err = 1; 
						$form->populate($formData);
					}
					
					
				} 
			} else {
				if ($formData['password']!=$formData['confirmpassword'])
				{
					$form->getElement('password')->addError("Passwords do not match");
					$form->getElement('password')->setAttrib('class','error');
					$form->getElement('confirmpassword')->addError("Passwords do not match");
					$form->getElement('confirmpassword')->setAttrib('class','error');
					
				}
			} 
		}
		
    }
    
}