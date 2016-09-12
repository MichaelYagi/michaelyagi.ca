<?php

class FoodController extends Zend_Controller_Action {
	
	private $User;
	private $Pass;
	private $UserId;
	
    public function init() {
    
    	$this->User = NULL;
		$this->UserId = NULL;
    	$this->Pass = NULL;
    
    	if (isset($_SESSION["user_auth"]["user"]) && isset($_SESSION["user_auth"]["pass"]) && isset($_SESSION["user_auth"]["user_id"])) {
    		$this->User = $_SESSION["user_auth"]["user"];
    		$this->UserId = $_SESSION["user_auth"]["user_id"];
			$this->Pass = $_SESSION["user_auth"]["pass"];
    	}
    
        if (!is_null($this->User) && (time() - $_SESSION["user_auth"]["created"]) > $_SESSION["user_auth"]["expiretime"]) {
			$this->User = NULL;
			$this->UserId = NULL;
    		$this->Pass = NULL;
		}
	}

	//View all recipes by default
    public function indexAction() {
    	if (is_null($this->User)) {
			$this->_helper->redirector('login', 'food');
		} else {
			$this->_helper->redirector('recipes', 'food');
		}
    }

	//View all recipes
	public function recipesAction() {
		/*
		//If not logged in, redirect to login/registration page
        if (is_null($this->User)) {
        	$this->_helper->redirector('login', 'food');
        }
        */
	
    	$this->view->headTitle(':Recipes');
    
        // action body
		$recipe = new Application_Model_Recipe($this->User,$this->Pass);
	
		if (in_array("tag",array_keys($this->getRequest()->getParams()))) {
			$tag = (string)$this->getRequest()->getParams("tag")["tag"];
			$recipeArray = $recipe->getRecipeByType("tag",$tag);
		} else if (in_array("user",array_keys($this->getRequest()->getParams()))) {
			$user = (string)$this->getRequest()->getParams("user")["user"];
			$recipeArray = $recipe->getRecipeByType("user",$user);
		} else {
			$recipeArray = $recipe->getAllRecipes();
		}
		
		$pager = new Zend_Paginator(new Zend_Paginator_Adapter_Array($recipeArray));
    	$this->view->paginator = $this->getPagination($pager);
		$this->view->objpager = $pager;
		$this->view->user = $this->User;		
		
    }
    
    //View recipe
    public function recipeAction() {
    	/*
    	//If not logged in, redirect to login/registration page
        if (is_null($this->User)) {
        	$this->_helper->redirector('login', 'food');
        }
        */
	
    	$this->view->headTitle(':Recipe');
    	
    	if ($this->getRequest()->getParam("msg")>0) {
        	$this->view->msg = $this->getRequest()->getParam("msg");
        } else if ($this->getRequest()->getParam("err")>0) {
        	$this->view->err = $this->getRequest()->getParam("err");
        }
    	
    	$recipe_id = $this->getRequest()->getParam("id");
    	
    	$recipe = new Application_Model_Recipe($this->User,$this->Pass);
    	$recipe = $recipe->getRecipe($recipe_id);
    	
    	$this->view->user = $this->User;
    	$this->view->recipe = $recipe;
    }
    
	//Add recipe
    public function addAction() {
    	//If not logged in, redirect to login/registration page
        if (is_null($this->User)) {
        	$this->_helper->redirector('login', 'food');
        }
	
    	$this->view->headTitle(':Add Recipe');
    	
    	$recipeObj = new Application_Model_Recipe($this->User,$this->Pass);
    	
    	$this->view->user = $this->User;
    	
    	$form = new Application_Form_EditRecipe();
    	$this->view->recipeform = $form;
    	
    	//Submit button was pressed 
        if ($this->getRequest()->isPost()) {
		
			//Form data
			$formData = $this->getRequest()->getPost(); 
			
			if (!$form->isValid($formData)) { 			
				//Invalid credentials												
				$this->view->err = 1;
				$form->populate($formData);
			} else {
				
				$recipe_id = $recipeObj->addRecipe($formData,$this->UserId);
			
				if ($recipe_id > 0) {
					$this->_redirect('/food/recipe/id/'.$recipe_id.'/msg/1');
				} else {
					//Redirect with from intact
					$form->populate($formData);
					$this->_redirect('/food/add');
				}
			}
		}
    }
	
	//Modify recipe
	public function editAction() {
		//If not logged in, redirect to login/registration page
        if (is_null($this->User)) {
        	$this->_helper->redirector('login', 'food');
        }
	
    	$this->view->headTitle(':Edit Recipe');
    	
    	$recipe_id = $this->getRequest()->getParam("id");
    	
    	$recipeObj = new Application_Model_Recipe($this->User,$this->Pass);
    	$recipe = $recipeObj->getRecipe($recipe_id);
    	
    	$this->view->user = $this->User;
    	$this->view->recipe = $recipe;
    	
    	$form = new Application_Form_EditRecipe(array('recipeid' => $recipe_id));
		$form->getElement('title')->setValue(html_entity_decode($recipe['title']));
		$form->getElement('publish')->setChecked(false);
		if ($recipe['published']) {
			$form->getElement('publish')->setChecked(true);
		}
		//Get the order of the element after first ingredient input
		$order = $form->getElement('ingredient')->getOrder()+1;
		
		if (isset($recipe['ingredients']['amount']) || isset($recipe['ingredients']['unit']) || isset($recipe['ingredients']['ingredient'])) {
			$form->getElement('ingredient_amount')->setValue(html_entity_decode($recipe['ingredients']['amount']));
			$form->getElement('ingredient_unit')->setValue(html_entity_decode($recipe['ingredients']['unit']));
			$form->getElement('ingredient')->setValue(html_entity_decode($recipe['ingredients']['ingredient']));
		} else {
			foreach($recipe['ingredients'] as $key => $ingredient) {
				if ($key == 0) {
					$form->getElement('ingredient_amount')->setValue(html_entity_decode($ingredient['amount']));
					$form->getElement('ingredient_unit')->setValue(html_entity_decode($ingredient['unit']));
					$form->getElement('ingredient')->setValue(html_entity_decode($ingredient['ingredient']));
				} else {
			
					$form->getElement('ingredient_sort_id')->setValue($key+1);
			
					$amountelement = new Zend_Form_Element_Text("newIngredientAmount_".($key+1));
		
					$amountelement->setOrder($order)
									->setBelongsTo('ingredient_amount')->setDecorators(array('ViewHelper'))->setAttribs(array('id' => 'newIngredientAmount_'.($key+1),'placeholder' => 'Amount','size' => '10'));
				
					$order++;
				
					$amountelement->setValue($ingredient['amount']);
				
					$unitelement = new Zend_Form_Element_Text("newIngredientUnit_".($key+1));
				
					$unitelement->setBelongsTo("ingredient_unit")->setDecorators(array('ViewHelper'))->setOrder($order)->setAttribs(array('id' => 'newIngredientUnit_'.($key+1),'placeholder' => 'Unit','size' => '10'));
				
					$order++;
				
					$unitelement->setValue($ingredient['unit']);
				
					$ingredientelement = new Zend_Form_Element_Text("newIngredient_".($key+1));
				
					$ingredientelement->setBelongsTo("ingredient")
										->setDecorators(array('ViewHelper',array('HtmlTag',array('tag' => 'br', 'placement' => 'append', 'selfClosing' => true))))
										->setOrder($order)->setAttribs(array('id' => 'newIngredient_'.($key+1),'placeholder' => 'Ingredient','size' => '40'));
				
					$order++;
				
					$ingredientelement->setValue($ingredient['ingredient']);
				
					$form->addElements(array($amountelement,$unitelement,$ingredientelement));
				}			
			}
		}
		
		$order = $form->getElement('step')->getOrder()+1;
		
		if (isset($recipe['steps']['description'])) {
			$form->getElement('step')->setValue(html_entity_decode($recipe['steps']['description']));
		} else {
			foreach($recipe['steps'] as $key => $step) {
				if ($key == 0) {
					$form->getElement('step')->setValue(html_entity_decode($step['description']));
				} else { //start from order 6
					$form->getElement('step_sort_id')->setValue($key+1);
			
					$element = new Zend_Form_Element_Textarea("newStep_".($key+1));
		
					$element->setBelongsTo("step")->setOrder($order)->setAttrib('style', 'margin-left:-40px;margin-top:-20px;margin-bottom:20px;');
				
					$element->setValue($step['description']);
				
					$order++;
				
					$form->addElement($element);
				}
			}
		}
		
		$form->getElement('serves')->setValue(html_entity_decode($recipe['serves']));
		$form->getElement('prep_time')->setValue(date ('H:i',strtotime($recipe['prep_time'])));
		$form->getElement('cook_time')->setValue(date ('H:i',strtotime($recipe['cook_time'])));
		
        $this->view->recipeform = $form; 
		
		//Submit button was pressed 
        if ($this->getRequest()->isPost()) {
		
			//Form data
			$formData = $this->getRequest()->getPost(); 
			
			// Form has been submitted - run data through preValidation()
			//$this->preValidation($formData);
			
			if (!$form->isValid($formData)) { 			
				//Invalid credentials												
				$this->view->err = 1;
				$form->populate($formData);
			} else {
				$retval = $recipeObj->updateRecipe($recipe_id,$formData,$recipe["images_info"],$this->UserId);
				
				if ($retval==1) {
					$this->_redirect('/food/recipe/id/'.$recipe_id.'/msg/1');
				} else {
					//Redirect back with form intact
				}
			}
		}
	}
	
	public function deleteAction() {
		//If not logged in, redirect to login/registration page
        if (is_null($this->User)) {
        	$this->_helper->redirector('login', 'food');
        }
	
    	$this->view->headTitle(':Delete Recipe');
    	
    	$recipe_id = $this->getRequest()->getParam("id");
    	
    	$recipe = new Application_Model_Recipe($this->User,$this->Pass);
    	$retval = $recipe->deleteRecipe($recipe_id);
    	
    	if (isset($retval["retval"]) && $retval["retval"] == 1) {
    		$this->_redirect('/food/recipes/msg/1');
    	} else {
    		$this->_redirect('/food/recipe/id/'.$recipe_id.'/err/1');
    	}
	}
	
	public function newingredientfieldAction() {
	
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		
		$ajaxContext->addActionContext('newingredientamountfield', 'html')->initContext();

		$ingredient_sort_id = $this->_getParam('ingredient_sort_id', null);

		$amountelement = new Zend_Form_Element_Text("newIngredientAmount_".$ingredient_sort_id);
		
		$amountelement->setBelongsTo("ingredient_amount")->setDecorators(array('ViewHelper'))->setAttribs(array('id' => 'newIngredientAmount_'.$ingredient_sort_id,'placeholder' => 'Amount','size' => '10'));
		
		$unitelement = new Zend_Form_Element_Text("newIngredientUnit_".$ingredient_sort_id);
		
		$unitelement->setBelongsTo("ingredient_unit")->setDecorators(array('ViewHelper'))->setAttribs(array('id' => 'newIngredientUnit_'.$ingredient_sort_id,'placeholder' => 'Unit','size' => '10'));
		
		$ingredientelement = new Zend_Form_Element_Text("newIngredient_".$ingredient_sort_id);
		
		$ingredientelement->setBelongsTo("ingredient")
							->setDecorators(array('ViewHelper',array('HtmlTag',array('tag' => 'br', 'placement' => 'append', 'selfClosing' => true))))
							->setAttribs(array('id' => 'newIngredient_'.$ingredient_sort_id,'placeholder' => 'Ingredient','size' => '40'));
		
		echo $amountelement->__toString();
		echo $unitelement->__toString();
		echo $ingredientelement->__toString();
		
		exit();
	}
	
	public function newstepfieldAction() {

		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		
		$ajaxContext->addActionContext('newstepfield', 'html')->initContext();

		$step_sort_id = $this->_getParam('step_sort_id', null);

		$element = new Zend_Form_Element_Textarea("newStep_".$step_sort_id);
		
		$element->setBelongsTo("step")->setAttrib('style', 'margin-left:-40px;margin-top:-20px;margin-bottom:20px;');
		
		echo $element->__toString();
		
		exit();
	}
	
	/**
	 * After post, pre validation hook
	 * 
	 * Finds all fields where name includes 'newName' and uses addNewField to add
	 * them to the form object
	 * 
	 * @param array $data $_GET or $_POST
	 */
	private function preValidation(array $data) {

	  // array_filter callback
	  function findFields($field) {
		// return field names that include 'newIngredient' or 'newStep'
		if (strpos($field, 'newIngredient') !== false || strpos($field, 'newStep') !== false) {
		  return $field;
		}
	  }

	  // Search $data for dynamically added fields using findFields callback
	  $newFields = array_filter(array_keys($data), 'findFields');

	  foreach ($newFields as $fieldName) {
		// strip the id number off of the field name and use it to set new order
		$ingredient_order = ltrim($fieldName, 'newIngredient') + 2;
		$this->addNewField($fieldName, $data[$fieldName], $ingredient_order);
		
		// strip the id number off of the field name and use it to set new order
		$step_order = ltrim($fieldName, 'newStep') + 2;
		$this->addNewField($fieldName, $data[$fieldName], $step_order);
	  }
	}

	/**
	 * Adds new fields to form
	 *
	 * @param string $name
	 * @param string $value
	 * @param int    $order
	 */
	public function addNewField($name, $value, $order) {

	  $this->addElement('text', $name, array(
		'required'       => true,
		'label'          => 'Name',
		'value'          => $value,
		'order'          => $order
	  ));
	}
	
	//Change email, reset password
	public function accountAction() {
		//If not logged in, redirect to login/registration page
        if (is_null($this->User)) {
        	$this->_helper->redirector('login', 'food');
        }
	
		$userObj = new Application_Model_User();
        $userid = $userObj->getUserId($this->User);
        	
		if ($userid < 1) {
			$this->_helper->redirector('login', 'food');
		}
        
        $user_info = $userObj->getUserInfoById($userid);
        
        //Administer Account
        $email_form = new Application_Form_UserEmail(array('userid' => $userid));
		$email_form->getElement('email')->setValue(html_entity_decode($user_info['email']));
		
		$password_form = new Application_Form_UserPassword(array('userid' => $userid));
		
        $this->view->emailaccountform = $email_form; 
        $this->view->passwordaccountform = $password_form; 
		
		//Registration button was pressed 
        if ($this->getRequest()->isPost()) {
        
			//Form data
			$formData = $this->getRequest()->getPost(); 
            
			//If the form data is valid
			if ((isset($formData["submit_email"]) && $email_form->isValid($formData)) || (isset($formData["submit_password"]) || $password_form->isValid($formData))) { 
			
				//Change Password
				if (isset($formData["submit_password"])) {
					if ($formData['password']!=$formData['password_confirm']) {
						$password_form->getElement('password')->addError("Passwords do not match");
						$password_form->getElement('password')->setAttrib('class','error');
						$password_form->getElement('password_confirm')->addError("Passwords do not match");
						$password_form->getElement('password_confirm')->setAttrib('class','error');
						
						$this->view->err = 1;
					} else {
						$password = $formData['password'];
						
						$retval = $userObj->setUserHash($userid,$password);
						
						if ($retval != $user_info['email']) {
							$this->view->err = 1;
						} else {
							$retval = 1;
						}
						
						if ($retval == 1) {
							$this->_redirect('/food/login/msg/3');
						}
					}
				
				//Change Email
				} else {
					$email = $formData['email'];
					
					$retval = $userObj->setUserEmail($userid,$email);
					$retval = $retval["ret_val"];
					
					if ($retval != 1) {
						$email_form->getElement('email')->addError("Email already in use");
						$email_form->getElement('email')->setAttrib('class','error');
					
						$this->view->err = 1;
						$email_form->populate($formData);
						
						if ($retval == 1) {
							$this->_redirect('/food/account/msg/1');
						}
					}
				}
				
			}
		}
	}
	
	//Login
	public function loginAction() {
	
		if (isset($_SESSION['user_auth'])) {
			unset($_SESSION['user_auth']);
			unset($this->User);
			unset($this->UserId);
			unset($this->Pass);
			$_SESSION['user_auth'] = NULL;
			$this->User = NULL;
			$this->UserId = NULL;
			$this->Pass = NULL;
		}
		
		//Login a user
        $form = new Application_Form_RecipeLogin();
		
        $this->view->loginform = $form; 
        
        if ($this->getRequest()->getParam("msg")>0) {
        	$this->view->msg = $this->getRequest()->getParam("msg");
        } else if ($this->getRequest()->getParam("err")>0) {
        	$this->view->err = $this->getRequest()->getParam("err");
        }
        
        //Login button was pressed 
        if ($this->getRequest()->isPost()) {
        
			//Form data
			$formData = $this->getRequest()->getPost(); 
            
			//If the form data is valid
			if ($form->isValid($formData)) { 
			
				if(isset($formData["submit"]) && trim($formData["submit"]) != "") { 
				
					$username = $formData['username'];
					$password = $formData['password'];
					$rememberme = $formData['rememberme'];
		
					$user = new Application_Model_User();
					$user_info = $user->getUserInfo($username);
					$message = '';
		
					if ($user_info["hash"] != crypt($password, $user_info["hash"]) && !$user_info["suspended"]) {
						$message = "Failed Login on ".date('M j, Y')." at ".date('g:i:s a T').", user failed login: ".$username.", IP=>".$_SERVER['REMOTE_ADDR']."\n";
						$writer = new Zend_Log_Writer_Stream(APPLICATION_PATH.'/logs/foodactivity.log');
						$logger = new Zend_Log($writer);	
						$logger->info($message);
						
						//Invalid credentials												
						$this->view->err = 1;
						$form->populate($formData);
					} else {
						$message = "Successful Login on ".date('M j, Y')." at ".date('g:i:s a T').", Username=>".htmlentities(stripslashes($username)).", IP=>".$_SERVER['REMOTE_ADDR']."\n";
						$user_id = $user->getUserId($username);
						$_SESSION["user_auth"]["user_id"] = $user_id;
						$_SESSION["user_auth"]["user"] = $username;
						$_SESSION["user_auth"]["pass"] = $password;
						$_SESSION["user_auth"]["expiretime"] = "";
						$_SESSION["user_auth"]["created"] = time();
						
						if ($rememberme) {
							$_SESSION["user_auth"]["expiretime"] = (3600*24*7*365);
						} else {
							//forget after 1 hour
							$_SESSION["user_auth"]["expiretime"] = 3600;
						}
						
						$writer = new Zend_Log_Writer_Stream(APPLICATION_PATH.'/logs/foodactivity.log');
						$logger = new Zend_Log($writer);	
						$logger->info($message);
						
						$this->_helper->redirector('recipes', 'food');
					}
				}
			}
		}
		
	}
	
	//Register
	public function registerAction() {
	
		if (isset($_SESSION['user_auth'])) {
			unset($_SESSION['user_auth']);
			unset($this->User);
			unset($this->UserId);
			unset($this->Pass);
			$_SESSION['user_auth'] = NULL;
			$this->User = NULL;
			$this->UserId = NULL;
			$this->Pass = NULL;
		}
		
		//Add a new user
        $form = new Application_Form_RecipeRegister();
		
        $this->view->registerform = $form; 
		
		//Registration button was pressed 
        if ($this->getRequest()->isPost()) {
        
			//Form data
			$formData = $this->getRequest()->getPost(); 
            
			//If the form data is valid
			if ($form->isValid($formData)) { 
			
				if ($formData['password']!=$formData['password_confirm']) {
					$form->getElement('password')->addError("Passwords do not match");
					$form->getElement('password')->setAttrib('class','error');
					$form->getElement('password_confirm')->addError("Passwords do not match");
					$form->getElement('password_confirm')->setAttrib('class','error');

				} else if(isset($formData["submit"]) && trim($formData["submit"]) != "") { 
				
					$username = $formData['username'];
					$password = $formData['password'];
					$email = $formData['email'];
		
					$user = new Application_Model_User();
					$retval = $user->setNewUser($username,$email,$password);
		
					if ($retval > 0) {
						$this->_redirect('/food/login/msg/1');
					} else {
						//Possible duplicate											
						$this->view->err = 1;
						$form->populate($formData);
					}
				}
			}
		}
	}
	
	public function recoverAction() {
		
        if (isset($_SESSION['user_auth'])) {
			unset($_SESSION['user_auth']);
			unset($this->User);
			unset($this->UserId);
			unset($this->Pass);
			$_SESSION['user_auth'] = NULL;
			$this->User = NULL;
			$this->UserId = NULL;
			$this->Pass = NULL;
		}
		
		//Enter email
        $form = new Application_Form_Recover();
		
        $this->view->recoverform = $form; 
		
		//Registration button was pressed 
        if ($this->getRequest()->isPost()) {
        
			//Form data
			$formData = $this->getRequest()->getPost(); 
            
			//If the form data is valid
			if ($form->isValid($formData)) { 
				$email = $formData['email'];
				
				$user = new Application_Model_User();
				$retval = $user->resetPassword($email);
				
				if ($retval == 1) {
					$this->_redirect('/food/login/msg/2');
				} else {
					//Email does not exist											
					$this->view->err = 1;
					$form->populate($formData);
				}
			}
		}
        
	}
	
	public function logoutAction() {
		unset($_SESSION['user_auth']);
		unset($this->User);
		unset($this->UserId);
		unset($this->Pass);
		$_SESSION['user_auth'] = NULL;
		$this->User = NULL;
		$this->UserId = NULL;
    	$this->Pass = NULL;
        $this->_helper->redirector('login', 'food');
	}
	
	public function getLink($page, $itemsPerPage, $label) {
  		$q = http_build_query(array(
      						'page' => $page,
      						'count' => $itemsPerPage
    						));
  		return "<a href=\"?$q\"><button class='small'>$label</button></a>";
	}
	
	private function getPagination($pager, $separator = ' ') {
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
		if (!empty($pages->previous)) {
			$pageLinks[] = $this->getLink($pages->previous, $itemsPerPage, '&lsaquo;');
		}


		// build page number links
		foreach ($pages->pagesInRange as $x) {
			if ($x == $pages->current) {
    			$pageLinks[] = "<button class='small blue'>".$x."</button>";
    		} else {
      			$pageLinks[] = $this->getLink($x, $itemsPerPage, $x);
    		}
		} 

		// build next page link
		if (!empty($pages->next)) {
			$pageLinks[] = $this->getLink($pages->next, $itemsPerPage, '&rsaquo;');
		}  

		// build last page link
		$pageLinks[] = $this->getLink($pages->last, $itemsPerPage, '&raquo;');
		
		return implode($pageLinks, $separator);
	}
	
}