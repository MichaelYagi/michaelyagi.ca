<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        $this->_redirect('/about');
    }

    public function indexAction()
    {
        
    }
    
}

