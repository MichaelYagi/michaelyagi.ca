<?php

class AdminController extends Zend_Controller_Action
{
	
	private $_username;
	
    public function init()
    {
        $this->view->headTitle(':Admin');

		//set identity
        if (Zend_Auth::getInstance()->hasIdentity())
        {
        	// get the user info from the storage (session)  
        	$userInfo = Zend_Auth::getInstance()->getStorage()->read(); 
        	$this->view->user = $userInfo['user'];
        	$this->_username = $userInfo['user'];
        	$this->view->expiretime = $userInfo['expiretime'];
        }
    }

    public function indexAction()
    {
        //If not logged in, redirect to login form
        if (!Zend_Auth::getInstance()->hasIdentity())
        {
        	$this->_helper->redirector('login', 'admin');
        }
        
        $this->view->headTitle(':New Post');
        
        if ($this->getRequest()->getParam("msg")>0)
        {
        	$this->view->msg = $this->getRequest()->getParam("msg");
        }
        else if ($this->getRequest()->getParam("err")>0)
        {
        	$this->view->err = $this->getRequest()->getParam("err");
        }
        
        $form = new Application_Form_NewPost();
        $this->view->newpostform = $form;
        
        if ($this->getRequest()->isPost()) 
		{
			//Form data
			$formData = $this->getRequest()->getPost(); 
            
			//If the form data is valid
			if ($form->isValid($formData)) 
			{ 
				if(isset($formData["submit"]) && trim($formData["submit"]) != "") 
				{ 
					//Insert new user
					$objNewPost = new Application_Model_Blog();

					//$arr returns the Code if success and empty if not 
					$retID = $objNewPost->setNewBlogPost(htmlentities(stripslashes($formData['title'])),htmlentities(stripslashes($formData['content'])),$formData['active']);        
					
					if(is_numeric($retID) && $retID > 0) 
					{ 
						$this->_redirect('/admin/editarticle/id/'.$retID.'/msg/2');
					} 
					else if (is_numeric($retID) && $retID <= 0)
					{ 
						//Error
						$this->view->err = 1; 
						$form->populate($formData);
					}
					else 
					{
						//Didn't return anything numeric
					}
					
				} 
			} else { 
				if ($form->getElement('title')->hasErrors())
				{
					$form->getElement('title')->setAttrib('class','error');
				}
			
				$this->view->err = 1; 
				$form->populate($formData); 
			} 
        } 
    }
    
    public function editpostAction()
    {
    	//If not logged in, redirect to login form
        if (!Zend_Auth::getInstance()->hasIdentity())
        {
        	$this->_helper->redirector('login', 'admin');
        }
        
        $this->view->headTitle(':Edit Posts');
    
    	if ($this->getRequest()->getParam("msg")>0)
        {
        	$this->view->msg = $this->getRequest()->getParam("msg");
        }
        else if ($this->getRequest()->getParam("err")>0)
        {
        	$this->view->err = $this->getRequest()->getParam("err");
        }
        
    	//get all blog posts
        $blog = new Application_Model_Blog();
		
		$viewby = $this->getRequest()->getParam("view");
		
		$arrBlog = null;
		if ($viewby == "status")
		{
			$arrBlog = $blog->getBlogPosts(0, $viewby);
		}
    	else
    	{
    		$arrBlog = $blog->getBlogPosts(0);
    	}
    	
    	$pager = new Zend_Paginator(new Zend_Paginator_Adapter_Array($arrBlog));
    	$this->view->paginator = $this->getPagination($pager);
		$this->view->objpager = $pager;
    	
    	if ($this->getRequest()->getParam("postaction")=="delete"&&is_numeric($this->getRequest()->getParam("id")))
    	{
    		$id = $this->getRequest()->getParam("id");
    		$retVal = $blog->setDeletePost($id);
    		
    		
    		if(is_numeric($retVal) && $retVal == 1) 
			{ 	
				$this->_redirect($this->getRedirectUrl($this->getRequest(),'editpost','msg',1));
			}
			else
			{
				$this->view->err = 2; 
			}
    	}
    	else if ($this->getRequest()->isPost()) 
		{
			//Form data
			$formData = $this->getRequest()->getPost();
			
			if(isset($formData["submit"]) && trim($formData["submit"]) != "") 
			{
				if(is_numeric($formData["action"]))
				{
					if (!empty($formData["postaction"]))
					{
						$isActive = $formData["action"];
						$idList = '';
						
						$counter = 0;
						foreach($formData["postaction"] as $value)
						{
							$idList .= $value;
							if ($counter == $listSize-1)
							{
								$idList .= '';
							}
							else
							{
								$idList .= ',';
							}
							$counter++;
						}
						
						if ($isActive!=2)
						{
							$retVal = $blog->setEditBlogPost($idList,$isActive);
						}
						else
						{
							$retVal = $blog->setDeletePosts($idList);
						}
						
						if(is_numeric($retVal) && $retVal == 1) 
						{ 	
							$this->_redirect($this->getRedirectUrl($this->getRequest(),'editpost','msg',1));
						} 
						else if(is_numeric($retVal) && $retVal == 2) 
						{ 
							$this->_redirect($this->getRedirectUrl($this->getRequest(),'editpost','msg',2));
						}
						else
						{
							$this->_redirect($this->getRedirectUrl($this->getRequest(),'editpost','err',2));						
						}
					}
					else
					{
						$this->_redirect($this->getRedirectUrl($this->getRequest(),'editpost','err',3));
					}
				}	
				else
				{
					$this->_redirect($this->getRedirectUrl($this->getRequest(),'editpost','err',1));
				}
			}
		}
    }
    
    public function editrecipeAction() {
    	//If not logged in, redirect to login form
        if (!Zend_Auth::getInstance()->hasIdentity()) {
        	$this->_helper->redirector('login', 'admin');
        }
        
        $this->view->headTitle(':Edit Recipes');
        
        if ($this->getRequest()->getParam("msg")>0) {
        	$this->view->msg = $this->getRequest()->getParam("msg");
        } else if ($this->getRequest()->getParam("err")>0) {
        	$this->view->err = $this->getRequest()->getParam("err");
        }
        
        //get blog post
        $recipeObj = new Application_Model_Recipe();
        
        $arrRecipes = $recipeObj->getAllRecipes();
        
        $pager = new Zend_Paginator(new Zend_Paginator_Adapter_Array($arrRecipes));
    	$this->view->paginator = $this->getPagination($pager);
		$this->view->objpager = $pager;
        
        
        if ($this->getRequest()->isPost()) {
			//Form data
			$formData = $this->getRequest()->getPost();
			
			if(isset($formData["submit"]) && trim($formData["submit"]) != "") {
				if(is_numeric($formData["action"]) && $formData["action"] == 4) {
				
					if (!empty($formData["postaction"])) {
						
						foreach($formData["postaction"] as $value) {
							$retVal = $recipeObj->adminDeleteRecipe($value);
							if ($retVal == 0) {
								//TODO:Something went wrong
							}
						}
						
						
						if(is_numeric($retVal) && $retVal == 1) { 	
							$this->_redirect($this->getRedirectUrl($this->getRequest(),'editrecipe','msg',1));
						} else if(is_numeric($retVal) && $retVal == 2) { 
							$this->_redirect($this->getRedirectUrl($this->getRequest(),'editrecipe','msg',2));
						} else {
							$this->_redirect($this->getRedirectUrl($this->getRequest(),'editrecipe','err',2));						
						}
					} else {
						$this->_redirect($this->getRedirectUrl($this->getRequest(),'editrecipe','err',3));
					}
				} else if (is_numeric($formData["action"]) && ($formData["action"] == 2 || $formData["action"] == 3)) {
					if (!empty($formData["postaction"])) {
						
						foreach($formData["postaction"] as $value) {
							$retVal = $recipeObj->adminPublishRecipe($value,$formData["action"]);
							if ($retVal == 0) {
								//TODO:Something went wrong
							}
						}
						
						
						if(is_numeric($retVal) && $retVal == 1) { 	
							$this->_redirect($this->getRedirectUrl($this->getRequest(),'editrecipe','msg',1));
						} else if(is_numeric($retVal) && $retVal == 2) { 
							$this->_redirect($this->getRedirectUrl($this->getRequest(),'editrecipe','msg',2));
						} else {
							$this->_redirect($this->getRedirectUrl($this->getRequest(),'editrecipe','err',2));						
						}
					} else {
						$this->_redirect($this->getRedirectUrl($this->getRequest(),'editrecipe','err',3));
					}
				} else {
					$this->_redirect($this->getRedirectUrl($this->getRequest(),'editrecipe','err',1));
				}
			}
		}
    }
    
    public function editrecipeuserAction() {
    
    	//If not logged in, redirect to login form
        if (!Zend_Auth::getInstance()->hasIdentity()) {
        	$this->_helper->redirector('login', 'admin');
        }
        
        $this->view->headTitle(':Edit Recipe Users');
        
        if ($this->getRequest()->getParam("msg")>0) {
        	$this->view->msg = $this->getRequest()->getParam("msg");
        } else if ($this->getRequest()->getParam("err")>0) {
        	$this->view->err = $this->getRequest()->getParam("err");
        }
        
        //get blog post
        $recipeUser = new Application_Model_User();
        
        $arrUsers = $recipeUser->getAllUsers();
        
        $pager = new Zend_Paginator(new Zend_Paginator_Adapter_Array($arrUsers));
    	$this->view->paginator = $this->getPagination($pager);
		$this->view->objpager = $pager;
        
        
        if ($this->getRequest()->isPost()) {
			//Form data
			$formData = $this->getRequest()->getPost();
			
			if(isset($formData["submit"]) && trim($formData["submit"]) != "") {
				if(is_numeric($formData["action"])) {
				
					if (!empty($formData["postaction"])) {
					
						$isSuspended = $formData["action"];
						$idList = '';
						$listSize = sizeof($formData["postaction"]);
						$counter = 0;
						
						$counter = 0;
						foreach($formData["postaction"] as $value) {
						
							$idList .= $value;
							if ($counter == $listSize-1) {
								$idList .= '';
							} else {
								$idList .= ',';
							}
							$counter++;
						}

						$retVal = $recipeUser->setUserStatus($idList,$isSuspended);
						
						if(is_numeric($retVal) && $retVal == 1) { 	
							$this->_redirect($this->getRedirectUrl($this->getRequest(),'editrecipeuser','msg',1));
						} else if(is_numeric($retVal) && $retVal == 2) { 
							$this->_redirect($this->getRedirectUrl($this->getRequest(),'editrecipeuser','msg',2));
						} else {
							$this->_redirect($this->getRedirectUrl($this->getRequest(),'editrecipeuser','err',2));						
						}
					} else {
						$this->_redirect($this->getRedirectUrl($this->getRequest(),'editrecipeuser','err',3));
					}
				} else {
					$this->_redirect($this->getRedirectUrl($this->getRequest(),'editrecipeuser','err',1));
				}
			}
		}
    }

	public function editarticleAction()
    {
		//If not logged in, redirect to login form
        if (!Zend_Auth::getInstance()->hasIdentity())
        {
        	$this->_helper->redirector('login', 'admin');
        }
        
        $this->view->headTitle(':Edit Post');
        
        if ($this->getRequest()->getParam("msg")>0)
        {
        	$this->view->msg = $this->getRequest()->getParam("msg");
        }
        else if ($this->getRequest()->getParam("err")>0)
        {
        	$this->view->err = $this->getRequest()->getParam("err");
        }
        
        //get blog post
        $blog = new Application_Model_Blog();
        
        $id = $this->getRequest()->getParam("id");
        
        if (is_numeric($id) && $id > 0)
        {
        	$getArticle = $blog->getBlogPost($id, 0);
        	$this->view->postid = $id;
        	$this->view->article = $getArticle;
        	
        	$form = new Application_Form_EditPost(array('blogid' => $id));
        	$form->getElement('title')->setValue(html_entity_decode($getArticle['title']));
        	$form->getElement('content')->setValue(html_entity_decode($getArticle['post']));
        	$form->getElement('active')->setValue($getArticle['isActive']);
        	
        	$this->view->editpostform = $form;
        	if ($getArticle['isActive'])
        	{
        		$this->view->postid = $id;
			}
			else
			{
				$this->view->postid = 0;
			}
			
			if ($this->getRequest()->isPost()) 
			{
				//Form data
				$formData = $this->getRequest()->getPost(); 
				//print_r($formData); exit();
            
				//If the form data is valid
				//if (!empty($formData['title'])&&!empty($formData['content'])&&($formData['active']==1||$formData['active']==0))
				//{	
				if ($form->isValid($formData)) 
				{ 
					if(isset($formData["submit"]) && trim($formData["submit"]) != "") 
					{ 
						//Update

						//$arr returns the Code if success and empty if not 
						$retID = $blog->setEditArticle($id,htmlentities(stripslashes($formData['title'])),htmlentities(stripslashes($formData['content'])),$formData['active']);        
					
						if(is_numeric($retID) && $retID > 0) 
						{ 
							$this->_redirect('/admin/editarticle/id/'.$retID.'/msg/1');
						} 
						else if (is_numeric($retID) && $retID <= 0)
						{ 
							//Error
							$this->view->err = 1; 
							$form->populate($formData);
						}
						else 
						{
							//Didn't return anything numeric
							echo "Oops";
						}
						
					} 
				} 
				else 
				{ 
					if ($form->getElement('title')->hasErrors())
					{
						$form->getElement('title')->setAttrib('class','error');
					}
					$this->view->err = 1;
					$form->populate($formData); 
				} 
	        } 
        }
	}

	public function editcommentAction()
    {
    	//If not logged in, redirect to login form
        if (!Zend_Auth::getInstance()->hasIdentity())
        {
        	$this->_helper->redirector('login', 'admin');
        }
        
        $this->view->headTitle(':Edit Comments');
    
    	if ($this->getRequest()->getParam("msg")>0)
        {
        	$this->view->msg = $this->getRequest()->getParam("msg");
        }
        else if ($this->getRequest()->getParam("err")>0)
        {
        	$this->view->err = $this->getRequest()->getParam("err");
        }
        
    	//get all comments
        $comment = new Application_Model_Comment();
		
		$viewby = $this->getRequest()->getParam("view");
		
		$arrBlog = null;
		if ($viewby != "")
		{
			$arrBlog = $comment->getAllComments($viewby);
		}
    	else
    	{
    		$arrBlog = $comment->getAllComments();
    	}
    	
    	$pager = new Zend_Paginator(new Zend_Paginator_Adapter_Array($arrBlog));
    	$this->view->paginator = $this->getPagination($pager);
		$this->view->objpager = $pager;
    	    	
    	if ($this->getRequest()->getParam("commentaction")=="delete"&&is_numeric($this->getRequest()->getParam("id")))
    	{
    		$id = $this->getRequest()->getParam("id");
    		$retVal = $comment->setDeleteComment($id);
    		
    		
    		if(is_numeric($retVal) && $retVal == 1) 
			{ 	
				$this->_redirect($this->getRedirectUrl($this->getRequest(),'editcomment','msg',1));
			}
			else
			{
				$this->view->err = 2; 
			}
    	}
    	else if ($this->getRequest()->isPost()) 
		{
			//Form data
			$formData = $this->getRequest()->getPost();
			
			if(isset($formData["submit"]) && trim($formData["submit"]) != "") 
			{
				if(is_numeric($formData["action"]))
				{
					if (!empty($formData["postaction"]))
					{
						$isActive = $formData["action"];
						$idList = '';
						$listSize = sizeof($formData["postaction"]);
						$counter = 0;
						
						foreach($formData["postaction"] as $value)
						{
							$idList .= $value;
							if ($counter == $listSize-1)
							{
								$idList .= '';
							}
							else
							{
								$idList .= ',';
							}
							$counter++;
						}
						
						if ($isActive==0||$isActive==1)
						{
							$retVal = $comment->setEditComments($idList,$isActive);
						}
						else if ($isActive==2)
						{
							$retVal = $comment->setDeleteComments($idList,$isActive);
						}
						else if ($isActive==3||$isActive==4)
						{
							if ($isActive==3)
							{
								$isActive = 1;
							}
							else
							{
								$isActive = 0;
							}
							
							$retVal = $comment->setMarkComments($idList,$isActive);
						}
					
						if(is_numeric($retVal)&&$retVal>0) 
						{ 	
							$this->_redirect($this->getRedirectUrl($this->getRequest(),'editcomment','msg',$retVal));
						} 
						else
						{
							$this->_redirect($this->getRedirectUrl($this->getRequest(),'editcomment','err',2));
						}
					}
					else
					{
						$this->_redirect($this->getRedirectUrl($this->getRequest(),'editcomment','err',3));
					}
				}	
				else
				{
					$this->_redirect($this->getRedirectUrl($this->getRequest(),'editcomment','err',1));
				}
			}
		}
    }
    
    public function editsinglecommentAction()
    {
    	//If not logged in, redirect to login form
        if (!Zend_Auth::getInstance()->hasIdentity())
        {
        	$this->_helper->redirector('login', 'admin');
        }
        
        $this->view->headTitle(':Edit Comment');
    
    	if ($this->getRequest()->getParam("msg")>0)
        {
        	$this->view->msg = $this->getRequest()->getParam("msg");
        }
        else if ($this->getRequest()->getParam("err")>0)
        {
        	$this->view->err = $this->getRequest()->getParam("err");
        }
        
    	//get a comment
        $comment = new Application_Model_Comment();
		
    	$id = $this->getRequest()->getParam("id");
    	
    	//Mark the comment as read
    	$comment->setCommentRead($id);
        
        if (is_numeric($id) && $id > 0)
        {
        	
        	$this->view->comment = $comment->getComment($id);
        	
    		//launch detected
    		if ($this->getRequest()->isPost()) 
			{
			
				//Form data
				$formData = $this->getRequest()->getPost();
				if(isset($formData["submit"]) && trim($formData["submit"]) != "") 
				{
					if(is_numeric($formData["isactive"]))
					{
				
						$isActive = $formData["isactive"];
						
						$retVal = $comment->setEditComment($id,$isActive);
						
						if(is_numeric($retVal)) 
						{ 	
							$this->_redirect('/admin/editsinglecomment/id/'.$id.'/msg/'.$retVal);
						} 
						else
						{
							$this->_redirect('/admin/editsinglecomment/id/'.$id.'/err/1');
						}
					}
				}
			}
		}
    }
    
    public function managefilesAction()
    {
    	//If not logged in, redirect to login form
        if (!Zend_Auth::getInstance()->hasIdentity())
        {
        	$this->_helper->redirector('login', 'admin');
        }
        
        $this->view->headTitle(':Manage Files');
    
    	if ($this->getRequest()->getParam("msg")>0)
        {
        	$this->view->msg = $this->getRequest()->getParam("msg");
        }
        else if ($this->getRequest()->getParam("err")>0)
        {
        	$this->view->err = $this->getRequest()->getParam("err");
        }
                
        //get all files in the public media folder
        $filemanager = new Application_Model_FileManager();
        
        //sorting
        $viewby = $this->getRequest()->getParam("view");
        
        $arrFiles = null;
        if (""!=$viewby)
		{
			$arrFiles = $filemanager->getAllFileInfo($viewby);
		}
    	else
    	{
    		$arrFiles = $filemanager->getAllFileInfo();
    	}
        
        $pager = new Zend_Paginator(new Zend_Paginator_Adapter_Array($arrFiles));
    	$this->view->paginator = $this->getPagination($pager);
		$this->view->objpager = $pager;
        
        //Launch detected
    	if ($this->getRequest()->isPost()) 
		{
			//Form data
			$formData = $this->getRequest()->getPost();
			
			if(isset($formData["submit"]) && trim($formData["submit"]) != "") 
			{
				if(is_numeric($formData["action"]))
				{
					if (!empty($formData["deletefileaction"]))
					{
						$arrFilenames = array();
						$listSize = sizeof($formData["deletefileaction"]);
						if ($listSize == 0)
						{
							$this->_redirect($this->getRedirectUrl($this->getRequest(),'managefiles','msg',2));
						}
						else
						{
							foreach($formData["deletefileaction"] as $value)
							{
								array_push($arrFilenames, $value);
							}
						
							$retVal = $filemanager->setDeleteFiles($arrFilenames);
						
							if(is_numeric($retVal)) 
							{ 	
								$this->_redirect($this->getRedirectUrl($this->getRequest(),'managefiles','msg',$retVal));
							}	 
							else
							{
								$this->_redirect($this->getRedirectUrl($this->getRequest(),'managefiles','err',2));
							}
						}
					}
					else
					{
						$this->_redirect($this->getRedirectUrl($this->getRequest(),'managefiles','err',3));
					}
				}
				else
				{
					$this->_redirect($this->getRedirectUrl($this->getRequest(),'managefiles','err',1));
				}
			}
			else if(isset($formData["upload"]) && trim($formData["upload"]) != "") 
			{
				$upload = new Zend_File_Transfer_Adapter_Http();
 				$upload->addValidator('NotExists', false, APPLICATION_PATH.'/../public/media/')
 						->setDestination(APPLICATION_PATH.'/../public/media/');
				if(!$upload->isValid())
				{
					$messages = $upload->getMessages();
					$this->view->fileMessages = $messages;
					//$this->_redirect($this->getRedirectUrl($this->getRequest(),'managefiles','err',5));
				}
				else if ($upload->receive()) 
				{ 
					$fileInfo = $upload->getFileInfo();
					$this->view->filename = $fileInfo['datafile']['name'];
					//$this->_redirect($this->getRedirectUrl($this->getRequest(),'managefiles','msg',3));
				}
				else
				{
					$this->_redirect($this->getRedirectUrl($this->getRequest(),'managefiles','err',4));
				}
			}
		}
		
    }

	public function optionsAction()
	{
		//If not logged in, redirect to login form
        if (!Zend_Auth::getInstance()->hasIdentity())
        {
        	$this->_helper->redirector('login', 'admin');
        }
        
        $this->view->headTitle(':Set Site Options');
	
		if ($this->getRequest()->getParam("msg")>0)
        {
        	$this->view->msg = $this->getRequest()->getParam("msg");
        }
        else if ($this->getRequest()->getParam("err")>0)
        {
        	$this->view->err = $this->getRequest()->getParam("err");
        }
	
		$form = new Application_Form_Options();
		$this->view->form = $form;
		
		//get site options
        $options = new Application_Model_SiteOptions();
        $view = Zend_Layout::getMvcInstance()->getView();
        $objSiteBackground = $options->getSiteBackground();
        $status = $options->getSiteStatus();
        $message = $options->getSiteMessage();
        
        //Set values for form
        $form->getElement('sitestatus')->setValue($status['value']);
        $form->getElement('sitemessage')->setValue($message);
        $form->getElement('cssbg')->setValue($objSiteBackground->name);
        
        if ($this->getRequest()->isPost()) 
		{
			//Form data
			$formData = $this->getRequest()->getPost(); 
            
			//If the form data is valid
			if ($form->isValid($formData)) 
			{ 
				if(isset($formData["submit"]) && trim($formData["submit"]) != "") 
				{
					$sitestatus = $formData['sitestatus'];
					$sitemessage = $formData['sitemessage'];
					$sitebg = $formData['cssbg'];
					
					$bgRetVal = $options->setSiteBackground($sitebg);					
					$siteStatRetVal = $options->setSiteStatus($sitestatus);
					$siteMessageRetVal = $options->setSiteMessage($sitemessage);

					if($siteStatRetVal==1&&$bgRetVal==1&&$siteMessageRetVal==1)
					{
						$this->_redirect('/admin/options/msg/1');
					} 
					else
					{
						$this->_redirect('/admin/options/err/1');
					}
				}
			}
		}
	}
	
	public function accountAction()
	{
		//If not logged in, redirect to login form
        if (!Zend_Auth::getInstance()->hasIdentity())
        {
        	$this->_helper->redirector('login', 'admin');
        }
        
        $this->view->headTitle(':Set Admin Options');
        
        $username = $this->_username;
        
        if ($this->getRequest()->getParam("msg")>0)
        {
        	$this->view->msg = $this->getRequest()->getParam("msg");
        }
        else if ($this->getRequest()->getParam("err")>0)
        {
        	$this->view->err = $this->getRequest()->getParam("err");
        }
	
		$account = new Application_Model_AccountOptions();
		$form = new Application_Form_ChangePassword();
		$this->view->form = $form;
		
		if ($this->getRequest()->isPost()) 
		{
			//Form data
			$formData = $this->getRequest()->getPost(); 
            
			//If the form data is valid
			if ($form->isValid($formData)) 
			{ 
				if ($formData['newpassword']!=$formData['confirmpassword'])
				{
					$form->getElement('newpassword')->addError("Passwords do not match");
					$form->getElement('newpassword')->setAttrib('class','error');
					$form->getElement('confirmpassword')->addError("Passwords do not match");
					$form->getElement('confirmpassword')->setAttrib('class','error');

				}
				else if(isset($formData["submit"]) && trim($formData["submit"]) != "") 
				{
					$oldpassword = $formData['oldpassword'];
					$newpassword = $formData['newpassword'];
					$confirmpassword = $formData['confirmpassword'];
					
					$retVal = $account->setChangePassword($username,$oldpassword,$newpassword);
					
					if($retVal==1)
					{
						$this->_redirect('/admin/account/msg/1');
					}
					else if ($retVal==-2)
					{
						//incorrect password entered
						$this->_redirect('/admin/account/err/2');
					}
					else
					{
						$this->_redirect('/admin/account/err/1');
					}
				}
			}
			else
			{
				if ($formData['newpassword']!=$formData['confirmpassword'])
				{
					$form->getElement('newpassword')->addError("Passwords do not match");
					$form->getElement('newpassword')->setAttrib('class','error');
					$form->getElement('confirmpassword')->addError("Passwords do not match");
					$form->getElement('confirmpassword')->setAttrib('class','error');

				}
			}
		}
	}
	
	public function usersAction()
	{
		//If not logged in, redirect to login form
        if (!Zend_Auth::getInstance()->hasIdentity()) {
        	$this->_helper->redirector('login', 'admin');
        }
        
        $this->view->headTitle(':Set User API Accounts');
        
        if ($this->getRequest()->getParam("msg")>0) {
        	$this->view->msg = $this->getRequest()->getParam("msg");
        } else if ($this->getRequest()->getParam("err")>0) {
        	$this->view->err = $this->getRequest()->getParam("err");
        }
        
    	//get all users
        $users = new Application_Model_User();
        $this->view->user_model = $users;
		
		$viewby = $this->getRequest()->getParam("view");
		
		$arrUsers = null;
		if ($viewby != "") {
			$arrUsers = $users->getAllUsers($viewby);
		} else {
    		$arrUsers = $users->getAllUsers();
    	}
    	
    	$pager = new Zend_Paginator(new Zend_Paginator_Adapter_Array($arrUsers));
    	$this->view->paginator = $this->getPagination($pager);
		$this->view->objpager = $pager;
    	    	
    	if ($this->getRequest()->isPost()) {
    	
			//Form data
			$formData = $this->getRequest()->getPost();
			
			if(isset($formData["submit"]) && trim($formData["submit"]) != "") {
			
				if(is_numeric($formData["action"])) {
				
					if (!empty($formData["useraction"])) {
					
						$status = $formData["action"];
						$idList = '';
						
						$counter = 0;
						
						foreach($formData["useraction"] as $value) {
						
							$idList .= $value;
							if ($counter == $listSize-1) {
								$idList .= '';
							} else {
								$idList .= ',';
							}
							$counter++;
						}
						
						if ($status != 1) {
							$status = 0;
						}
						$retVal = $users->setUserStatus($idList,$status);
						
						if(!empty($retVal)) { 	
							$this->_redirect($this->getRedirectUrl($this->getRequest(),'users','msg',1));
						} else {
							$this->_redirect($this->getRedirectUrl($this->getRequest(),'users','err',1));						
						}
					} else {
						$this->_redirect($this->getRedirectUrl($this->getRequest(),'users','err',1));
					}
				} else {
					$this->_redirect($this->getRedirectUrl($this->getRequest(),'users','err',1));
				}
			}
		}
        
    }

		public function logsAction()
	{   
		//If not logged in, redirect to login form
        if (!Zend_Auth::getInstance()->hasIdentity())
        {
        	$this->_helper->redirector('login', 'admin');
        }
        
        $this->view->headTitle(':View Logs');
		
		//Application Log Form
        $appLogForm = new Application_Form_ViewLog(array("type" => "Application"));
        
        //Login Log Form
        $loginLogForm = new Application_Form_ViewLog(array("type" => "Login"));
        
        //Food Log Form
        $foodLoginLogForm = new Application_Form_ViewLog(array("type" => "FoodLogin"));
        
        //Api Log Form
        $apiLoginLogForm = new Application_Form_ViewLog(array("type" => "ApiLogin"));
        
        $logs = new Application_Model_LogManager();
        $appLogForm->getElement("ApplicationLog")->setValue($logs->getApplicationLog());
        $loginLogForm->getElement("LoginLog")->setValue($logs->getLoginLog());
        $foodLoginLogForm->getElement("FoodLoginLog")->setValue($logs->getFoodLoginLog());
        $apiLoginLogForm->getElement("ApiLoginLog")->setValue($logs->getApiLoginLog());
        
        $this->view->applogform = $appLogForm;
        $this->view->loginlogform = $loginLogForm;
        $this->view->foodloginlogform = $foodLoginLogForm;
        $this->view->apiloginlogform = $apiLoginLogForm;
        
        if ($this->getRequest()->isPost()) 
		{
			//Form data
			$formData = $this->getRequest()->getPost(); 
            
			//If the form data is valid
			if(isset($formData["clearApplicationLog"]) && trim($formData["clearApplicationLog"]) != "") 
			{ 
				$logs->setClearApplicationLog();
				$this->_helper->redirector('logs', 'admin');
			}
			else if(isset($formData["clearLoginLog"]) && trim($formData["clearLoginLog"]) != "")
			{
				$logs->setClearLoginLog();
				$this->_helper->redirector('logs', 'admin');
			}
			else if(isset($formData["clearFoodLoginLog"]) && trim($formData["clearFoodLoginLog"]) != "")
			{
				$logs->setClearFoodLoginLog();
				$this->_helper->redirector('logs', 'admin');
			}
			else if(isset($formData["clearApiLoginLog"]) && trim($formData["clearApiLoginLog"]) != "")
			{
				$logs->setClearApiLoginLog();
				$this->_helper->redirector('logs', 'admin');
			}
		}
	}

	public function loginAction()
	{   
		$this->view->headTitle(':Login');
		
		//If not logged in, redirect to login form
        if (Zend_Auth::getInstance()->hasIdentity())
        {
        	$this->_helper->redirector('index', 'admin');
        }
		
		//Add a new user
        $form = new Application_Form_Login();
		
        $this->view->loginform = $form; 
        //Login button was pressed 
        if ($this->getRequest()->isPost()) 
		{
			//Form data
			$formData = $this->getRequest()->getPost(); 
            
			//If the form data is valid
			if ($form->isValid($formData)) 
			{ 
				if(isset($formData["submit"]) && trim($formData["submit"]) != "") 
				{ 
					$username = $formData['username'];
					$password = $formData['password'];
					$rememberme = $formData['rememberme'];
					
					//record login username, password and timestamp here in an xml
					$writer = new Zend_Log_Writer_Stream(APPLICATION_PATH.'/logs/adminactivity.log');
					$logger = new Zend_Log($writer);
															
					$authAdapter = $this->getAuthAdapter(htmlentities(stripslashes($password)));
					$authAdapter->setIdentity(htmlentities(stripslashes($username)))
								->setCredential(htmlentities(stripslashes($password)));
					$auth = Zend_Auth::getInstance();
					$data = $auth->getStorage()->read();
					$result = $auth->authenticate($authAdapter);
					if (!$result->isValid())
					{					
						$message = "Failed Login on ".date('M j, Y')." at ".date('g:i:s a T').", Username=>".htmlentities(stripslashes($username)).", IP=>".$_SERVER['REMOTE_ADDR']."\n";
						$logger->info($message);
										
						//Invalid credentials												
						$this->view->err = 1;
						$form->populate($formData);
					}
					else
					{
						$message = "Successful Login on ".date('M j, Y')." at ".date('g:i:s a T').", Username=>".htmlentities(stripslashes($username)).", IP=>".$_SERVER['REMOTE_ADDR']."\n";
						$logger->info($message);
					
						$authSession = new Zend_Session_Namespace('Zend_Auth');
						$authSession->user = $username;
						$data['expiretime'] = '';
						$data['user'] = $username;
						if ($rememberme)
						{
							$authSession->setExpirationSeconds(3600*24*7*365);
							Zend_Session::rememberMe(3600*24*7*365);
							$data['expiretime'] = 'You must log out manually';
						}
						else 
						{
							//forget after 1 hour
							$authSession->setExpirationSeconds(3600);
							Zend_Session::rememberMe(3600);
							$data['expiretime'] = "Session expires on ".date('M j',time()+3600)." at ".date('g:i a',time()+3600)." EST";
						}
												
						$auth->getStorage()->write($data);
						$this->_helper->redirector('index', 'admin');
					}
				}
			} 
			else 
			{ 
				if ($form->getElement('username')->hasErrors())
				{
					$form->getElement('username')->setAttrib('class','error');
				}
				if ($form->getElement('password')->hasErrors())
				{
					$form->getElement('password')->setAttrib('class','error');
				}
			
				//Invalid form data so populate form and try again 
				$formData["username"] = htmlentities(stripslashes($formData["username"])); 
				$this->view->err = 2;
				$form->populate($formData); 
			} 
        } 
	}

	public function logoutAction()
	{
		Zend_Auth::getInstance()->clearIdentity();
		Zend_Session::forgetMe();
        $this->_helper->redirector('login', 'admin');
	}
	
	private function getAuthAdapter($pass)
	{
		$authAdapter = new Zend_Auth_Adapter_DbTable(Zend_Db_Table::getDefaultAdapter());
		$tools = new Application_Model_EncryptUtility();
		
		$encryptedPass =  trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $tools->getStaticSalt(), $pass, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)))); 

		$authAdapter->setTableName('admin')
					->setIdentityColumn('username')
					->setCredentialColumn('password')
					->setCredentialTreatment('SHA1(CONCAT(?,"'.$encryptedPass.'"))');
					
		return $authAdapter;
	}
	
	private function getRedirectUrl($request,$action,$messagetype,$messagevalue)
	{
		$url = null;
		$string = '';
		if (""!=$request->getParam("page")&&""!=$request->getParam("count"))
        {
        	if (""!=$request->getParam("view"))
        	{
        		$url = '/admin/'.$action.'/'.$messagetype.'/'.$messagevalue.'/view/'.$request->getParam("view").'?page='.$request->getParam("page").'count='.$request->getParam("count");
        	}
        	else
        	{
        		$url = '/admin/'.$action.'/'.$messagetype.'/'.$messagevalue.'?page='.$request->getParam("page").'count='.$request->getParam("count");
        	}
    	}
  		else
        {
        	if (""!=$request->getParam("view"))
        	{
        		$url = '/admin/'.$action.'/'.$messagetype.'/'.$messagevalue.'/view/'.$request->getParam("view");
        	}
        	else
        	{
        		$url = '/admin/'.$action.'/'.$messagetype.'/'.$messagevalue;
        	}		
		}
		
		return $url;
	}
	
	public function getLink($page, $itemsPerPage, $label) 
	{
  		$q = http_build_query(array(
      						'page' => $page,
      						'count' => $itemsPerPage
    								));
  		return "<a href=\"?$q\"><button class='small'>$label</button></a>";
	}
	
	public function getPagination($pager, $separator = ' ')
	{
		// set page number from request
		$currentPage = isset($_GET['page']) ? (int) htmlentities($_GET['page']) : 1;
		$pager->setCurrentPageNumber($currentPage);

		// set number of items per page from request
		$itemsPerPage = isset($_GET['count']) ? (int) htmlentities($_GET['count']) : 10;
		$pager->setItemCountPerPage($itemsPerPage);

		// set number of pages in page range
		//$pager->setPageRange(5);

		// get page data
		$pages = $pager->getPages('Sliding');

		// create page links
		$pageLinks = array();

		// build first page link
		$pageLinks[] = $this->getLink($pages->first, $itemsPerPage, '&laquo;');

		// build previous page link
		if (!empty($pages->previous)) 
		{
			$pageLinks[] = $this->getLink($pages->previous, $itemsPerPage, '&lsaquo;');
		}


		// build page number links
		foreach ($pages->pagesInRange as $x) 
		{
			if ($x == $pages->current) 
			{
    			$pageLinks[] = "<button class='small blue'>".$x."</button>";
    		} else {
      			$pageLinks[] = $this->getLink($x, $itemsPerPage, $x);
    		}
		} 

		// build next page link
		if (!empty($pages->next)) 
		{
			$pageLinks[] = $this->getLink($pages->next, $itemsPerPage, '&rsaquo;');
		}  

		// build last page link
		$pageLinks[] = $this->getLink($pages->last, $itemsPerPage, '&raquo;');
		
		return implode($pageLinks, $separator);
	}
}
