<?php

class NewsController extends Zend_Controller_Action
{
	private $_controller;
    public function init()
    {
		$this->_controller = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
    	$this->view->controller = $this->_controller;      
        $this->view->headTitle(':News');
    }

    public function indexAction()
    {
        // action body
		$blog = new Application_Model_Blog();
		$comment = new Application_Model_Comment();
		
    	$this->view->arrBlog = $blog->getBlogPosts();
    }

	public function articleAction()
    {
        // action body		
		$article_id = $this->_getParam("id");
		$request = Zend_Controller_Front::getInstance()->getRequest();
        $url = $request->getScheme() . '://' . $request->getHttpHost();
		
		if (is_numeric($article_id))
		{
			$blog = new Application_Model_Blog();
			$postcomment = new Application_Model_Comment();
						
			$arrPost = $blog->getBlogPost($article_id);
			$arrComments = $postcomment->getBlogPostComments($article_id);
			
			//Add a new comment form
        	$form = new Application_Form_PostComment(array('blogid' => $article_id));
        	$this->view->commentform = $form;
			
    		$this->view->arrPost = $arrPost;
    		$this->view->arrComments = $arrComments;
    		$this->view->headTitle(":".$arrPost['title']);
    		
    		if ($this->getRequest()->getParam("msg")==1)
        	{
        		$this->view->msg = 1;
        	}
    		
    		if ($this->getRequest()->isPost()) 
			{
				//Form data
				$formData = $this->getRequest()->getPost();
			
				//If the form data is valid
				if ($form->isValid($formData)) 
				{ 
					if(isset($formData["submit"]) && trim($formData["submit"]) != "") 
					{	
						$name = htmlentities(stripslashes($formData['name']));
						$email = htmlentities(stripslashes($formData['email']));
						$comment = htmlentities(stripslashes($formData['comment']));
						$active = $formData['active'];
					
						$retID = $postcomment->setNewComment($article_id, $name, $email, $comment, $active);
						
						if(is_numeric($retID) && $retID > 0) 
						{	 
							$message = "New comment by ".$name."\n\n".$comment."\n\nEmail: ".$email."\n\nStatus: ".$active."\n\nEdit: ".$url."/admin/editsinglecomment/id/".$retID."\n\n";
							
							try {
								$mail = new Zend_Mail();
								$mail->setBodyText($message)
									->setFrom('$email', $name)
									->addTo('michaeltyagi@gmail.com', 'Michael Yagi')
									->setSubject('Comment from '.$name)
									->send();
							} catch(Exception $e) {
								//Do nothing for now
							}
						
							$this->_redirect('/'.$this->_controller.'/article/id/'.$article_id.'/msg/1');
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
				}
				else
				{
					if ($form->getElement('name')->hasErrors())
					{
						$form->getElement('name')->setAttrib('class','error');
					}
					if ($form->getElement('email')->hasErrors())
					{
						$form->getElement('email')->setAttrib('class','error');
					}
					$this->view->err = 1;
					$form->populate($formData);
				}
			}
    	}
    }
    
    public function refreshAction()
	{
		$form = new Application_Form_PostComment(array('blogid' => 0));
	    $captcha = $form->getElement('captcha')->getCaptcha();

		$data = array();

		$data['id']  = $captcha->generate();
   	 	$data['src'] = $captcha->getImgUrl() .
    	    	        $captcha->getId() .
        	    	    $captcha->getSuffix();
	
	   	$this->_helper->json($data);
	}
    
}

