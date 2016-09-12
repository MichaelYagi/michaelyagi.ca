<?php
class Model_UserAcl extends Zend_Acl
{
	public function __construct() 
	{
		//User Roles...inherited
		$this->addRole(new Zend_Acl_Role('guest'))
        		->addRole(new Zend_Acl_Role('regular'), 'guest')
        		->addRole(new Zend_Acl_Role('premium'), 'regular')
        		->addRole(new Zend_Acl_Role('admin'), 'premium');
	
		//Resources
		//action, controller
    	$this->add(new Zend_Acl_Resource('account'));
        $this->add(new Zend_Acl_Resource('login'), 'account');
        $this->add(new Zend_Acl_Resource('logout'), 'account');
        $this->add(new Zend_Acl_Resource('premium'), 'account');
        
        $this->add(new Zend_Acl_Resource('user'));

		//Permissions based on Role and Resource
		//role, controller, action
		$this->allow('guest', 'user');
    }

}