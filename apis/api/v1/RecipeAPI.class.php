<?php

ini_set('display_startup_errors',1);
ini_set('display_errors',1);
error_reporting(-1);
/*
TODO:
*/

require_once('../API.class.php');
require_once('../User.class.php');
require_once('../Image.class.php');
require_once('../Recipe.class.php');
require_once('../wr/WR.class.php');
require_once('../Log.class.php');

class RecipeAPI extends API {
    protected $User;
    protected $UserID;
	protected $UserObj;

    public function __construct($request, $origin) {
    
    	parent::__construct($request);
		
		$this->UserObj = new User();
		
		$needs_authorization = true;
		
		if (($this->method == "PUT" && $this->endpoint == "user" && empty($this->verb) && strlen($this->verb) == 0) || 
			($this->method == "PUT" && $this->endpoint == "recover") ||
			($this->method == "PUT" && $this->endpoint == "user" && !empty($this->verb) && strlen($this->verb) > 0 && sizeof($this->args) == 1 && !empty($this->args[0]) && $this->args[0] == "verify") ||
			 $this->method == "GET" && $this->endpoint != "user") {
			$needs_authorization = false;
		}
		
		//Registering user so no authentication
		if ($needs_authorization) {
		
			if (!isset($_SERVER['PHP_AUTH_USER'])) {
				header('WWW-Authenticate: Basic realm="Food Realm"');
				header('HTTP/1.0 401 Unauthorized');
				echo 'You do not have permission to view this page';
				die();
			} else {

				$user = $_SERVER['PHP_AUTH_USER'];
				$pass = $_SERVER['PHP_AUTH_PW'];
			
				$user_info = $this->UserObj->getUserInfoByUser($user);

				if ($user_info["hash"] != crypt($pass, $user_info["hash"]) && !$user_info["suspended"]) {
				
					if ($this->format == "xml") {
						$xml = new SimpleXMLElement("<?xml version='1.0' encoding='UTF-8'?><Error></Error>");

						$error = $xml->addChild('error',"Incorrect username or password");

						print($xml->asXML());
					} else {
						print("{'error':'Incorrect username or password'}");
					}
					exit();
				}	

				$this->User = $user;
				$this->UserID = $this->UserObj->getUserId($user);
			}
		
		}
    
    }
	
    /**
     * Example of an Endpoint
     */
     protected function helloworld() {
        if ($this->method == 'GET') {
			$ret_array = array();
			array_push($ret_array,"You've found me! Hello World!");
			$this->setRootXML(__FUNCTION__);
            return $ret_array;
        } else {
            return "Only accepts GET requests";
        }
     }
	 
     protected function about() {
        if ($this->method == 'GET') {
			$this->setRootXML(__FUNCTION__);
			
			$date = strtotime('12/23/2014');

			$dateformat = date('Y-m-d',$date);
			
			$ret_array["version"] = "1";
			$ret_array["title"] = "Recipe API";
			$ret_array["date"] = $dateformat;
			$ret_array["author"] = "Michael Yagi";
			$ret_array["contact"] = "myagi.developer@gmail.com";
			
            return $ret_array;
        } else {
            return "Only accepts GET requests";
        }
     }

	 protected function tag() {
		 
		$recipes = new Recipe(); 
		$this->setRootXML(__FUNCTION__); 
		 
        if ($this->method == 'GET') {
			
			if (!empty($this->verb) && strlen($this->verb) > 0) {
				$search_term = $this->verb;
			
				$retarr = $recipes->searchTags($search_term);
				
			} else {
				$retarr["retval"] = 0;
				$retarr["message"] = "Not enough criteria";
			}
			
            return $retarr;
        } else {
            return "Only accepts GET requests";
        }
     }
	 
	 protected function recipes() {
	 
	 	$recipes = new Recipe();
		$this->setRootXML(__FUNCTION__);
		
        if ($this->method == 'GET') {

			$recipes = $recipes->getAllRecipes();
			
            return $recipes;
        } else {
            return "Only accepts GET requests";
        }
     }
	 
	 protected function recover() {
		
		$user_obj = json_decode($this->file);
		$this->setRootXML(__FUNCTION__);
		
		if ($this->method == 'PUT') {
			if (isset($user_obj->email)) {
				$email = $user_obj->email;
				
				$ret_val = $this->UserObj->resetPassword($email);
				
				$retarr["retval"] = $ret_val;
				
				if ($ret_val == 1) {
					$retarr["message"] = "Success";
				} else {
					$retarr["message"] = "Failed";
				}
			} else {
				$retarr["retval"] = 0;
				$retarr["message"] = "Not enough criteria";
			}
			
			return $retarr;
		} else {
            return "Unknown method request";
        }
        
	}
	 
	protected function user() {
		
		$user_obj = json_decode($this->file);
		$this->setRootXML(__FUNCTION__);
			 
		if ($this->method == 'PUT') {
		
			/*************************************/
			//	VERIFY USER						  /
			/*************************************/
			if ((!empty($this->verb) && strlen($this->verb) > 0) && isset($user_obj->password) && sizeof($this->args) == 1 && !empty($this->args[0]) && $this->args[0] == "verify") {
				$username = $this->verb;
				$password = $user_obj->password;
								
				$user_info = $this->UserObj->getUserInfoByUser($username);

				if ($user_info["hash"] != crypt($password, $user_info["hash"])) {
					$retarr["userid"] = 0;
					$retarr["email"] = null;
					$retarr["retval"] = 0;
					$retarr["message"] = "Incorrect Credentials";
				} else if ($user_info["suspended"]) {
					$retarr["userid"] = 0;
					$retarr["email"] = null;
					$retarr["retval"] = 0;
					$retarr["message"] = "User suspended";
				} else {
					$user = $this->UserObj->getUserIdEmail($username);
					$retarr["userid"] = $user["userid"];
					$retarr["email"] = $user["email"];
					$retarr["retval"] = 1;
					$retarr["message"] = "User verified";
				}
				
				$log_object = new Log("foodapilogin");
				$log_message = $log_object->get_client_ip()."\n";
				$log_message .= var_export($retarr,true);
				$log_retval = $log_object->write($log_message);
			
				return $retarr;
			/*************************************/
			//	UPDATE HASH	OR EMAIL			  /
			/*************************************/
			//curl -i -H "Accept: application/json" -X PUT -H "Content-Type: application/json" http://test:test@myagi.asuscomm.com:9245/api/v1/json/user/username -d '{"password":"test2"}'
			//curl -i -H "Agent-Type: application/json" http://test:test@myagi.asuscomm.com:9245/api/v1/json/user/username -d '{"email":"test@test.com"}'
			} else if (!empty($this->verb) && strlen($this->verb) > 0 && sizeof($this->args) == 0) {
			
				$username = $this->verb;
				$user = $this->UserObj->getUserInfoByUser($username);
				
				//Verify user identity
				if ($username == $this->User) {
					$uid = $this->UserID;
					
					if (isset($user_obj->password)) {
						//Generate the hash based on password
						$retval = $this->UserObj->setUserHash($uid,$user_obj->password);
						$retarr["password_changed"] = $retval["retVal"];
					} else {
						$retarr["password_changed"] = 0;
					}
					
					if (isset($user_obj->email)) {
						//Change email
						$retval = $this->UserObj->setUserEmail($uid,$user_obj->email);
						$retarr["email_changed"] = $retval["ret_val"];
					} else {
						$retarr["email_changed"] = 0;
					}
					
					$retarr["retval"] = 1;
					$retarr["message"] = "Updated User";
				} else {
					$retarr["retval"] = 0;
					$retarr["message"] = "Not authorized";
				}
				
				return $retarr;
			} else if (empty($this->verb) && strlen($this->verb) == 0) {
				/*************************************/
				//	ADD USER						  /
				/*************************************/
				//curl -i -H "Accept: application/json" -X PUT -H "Content-Type: application/json" http://myagi:hilaredarche@myagi.asuscomm.com:9245/api/v1/json/user -d '{"username":"test","password":"password","email":"email"}'
				if (isset($user_obj->username) && isset($user_obj->password) && isset($user_obj->email)) {
					$username = $user_obj->username;
					$password = $user_obj->password;
					$email = $user_obj->email;
					
					$retval = $this->UserObj->createUser($username,$password,$email);
					
					if ($retval > 0) {
						$retarr["retval"] = $retval;
						$retarr["message"] = "Username successfully created";
					} else {
						$retarr["retval"] = 0;
						$retarr["message"] = "Please choose a different username and/or email";
					}
				} else {
					$retarr["retval"] = 0;
					$retarr["message"] = "Incorrect criteria";
				}
				
				$log_object = new Log("foodapilogin");
				$log_message = $log_object->get_client_ip()."\n";
				$log_message .= $username."\n";
				$log_message .= $email."\n";
				$log_message .= var_export($retarr,true);
				$log_retval = $log_object->write($log_message);
				
				return $retarr;
			} else {
				$retarr["retval"] = 0;
				$retarr["message"] = "Not enough criteria";
			}
		} else if ($this->method == 'GET') { 
			/*************************************/
			//	GET USER ID & EMAIL				  /
			/*************************************/
			if (!empty($this->verb) && strlen($this->verb) > 0 && sizeof($this->args) == 0) {
			
				$username = $this->verb;
				
				if ($username == $this->User) {
				
					$user = $this->UserObj->getUserIdEmail($username);
				
					if ($user["userid"] > 0) {
						$retarr["retval"] = 1;
						$retarr["message"] = "Success";
						$retarr["user_id"] = $user["userid"];
						$retarr["user_email"] = $user["email"];
					} else {
						$retarr["retval"] = 0;
						$retarr["message"] = "User not found";
						$retarr["user_id"] = 0;
						$retarr["user_email"] = null;
					}
				} else {
					$retarr["retval"] = 0;
					$retarr["message"] = "Not Authorized";
					$retarr["user_id"] = 0;
					$retarr["user_email"] = null;
				}
				
				return $retarr;
			} else {
				$retarr["retval"] = 0;
				$retarr["message"] = "Not enough criteria";
			}
		} else {
            return "Unknown method request";
        }
	 }
	 
	 protected function recipesByType() {
	 
	 	$recipes = new Recipe();
		$this->setRootXML(__FUNCTION__);
		
        if ($this->method == 'GET') {
			if (sizeof($this->args) == 1 && !empty($this->verb) && !empty($this->args[0])) {
				$type = $this->verb;
				$value = $this->args[0];
				$recipes = $recipes->getRecipesByType($type,$value);
			} else {
				$recipes = "Must have type and value.";
			}
			
            return $recipes;
        } else {
            return "Only accepts GET requests";
        }
     }
	 
	//Exmaple requests: 
	//curl -i -H "Accept: application/json" -X GET -H "Content-Type: application/json" http://myagi:hilaredarche@myagi.asuscomm.com:9245/api/v1/json/recipe/34
	//curl -i -H "Accept: application/json" -X PUT -H "Content-Type: application/json" http://myagi:hilaredarche@myagi.asuscomm.com:9245/api/v1/json/recipe -d '{"title":"test recipe","user":"myagi","ingredients":[{"sort_order":"1","amount":"2","unit":"cups","ingredient":"sugar"},{"sort_order":"2","amount":"5","unit":"TBS","ingredient":"salt"}],"steps":[{"sort_order":"1","description":"blah 1"},{"sort_order":"2","description":"step 2 - profit"}],{"tags":[{"keyword":"sugary"},{"keyword":"salty"}]}}'
	//curl -i -X POST -F "files=@test.png" http://myagi:hilaredarche@myagi.asuscomm.com:9245/api/v1/json/recipe/59/image
	//curl -i -H "Accept: application/json" -X GET -H "Content-Type: application/json" http://myagi:hilaredarche@myagi.asuscomm.com:9245/api/v1/json/recipes
	//curl -i -H "Accept: application/json" -X PUT -H "Content-Type: application/json" http://myagi:hilaredarche@myagi.asuscomm.com:9245/api/v1/json/recipe/59 -d '{"delete_image":{"iid":"11","ext":"png"}}'
	protected function recipe() {
	 
	 	$recipes = new Recipe();
		$this->setRootXML(__FUNCTION__);
	 
        if ($this->method == 'GET') {
        
			/*************************************/
			//	GET								  /
			/*************************************/
        	if (sizeof($this->args) == 1 && is_numeric($this->args[0])) {
				
				$recipe_id = $this->args[0];
			
				$recipe = $recipes->getRecipe($recipe_id);
				
			} else if(sizeof($this->args) == 2 && is_numeric($this->args[0]) && $this->args[1] == "images") {
				$recipe_id = $this->args[0];
			
				$images = $recipes->getRecipeImageInfo($recipe_id);
				
				return $images;
			} else {
				$recipe = "Must pass an integer value";
			}
			
            return $recipe;
            
        } else if ($this->method == 'POST') {
        
        	$retval["retval"] = false;
			$retval["message"] = "";
        	
        	if (is_numeric($this->args[0]) && $this->args[0] > 0) {
        	
        		$recipe_id = $this->args[0];
				
				$recipe = $recipes->getRecipe($recipe_id);
				
				if ($recipe_id == $recipe["id"]) {
					$recipe_id = $recipe["id"];
				} else {
					$recipe_id = 0;
				}
				
				if ($recipe_id > 0 && $recipe["user"] == $this->User) {
				
					/*************************************/
					//	Add Image						  /
					/*************************************/
					if (!empty($this->args[1]) && $this->args[1] == "image") {
						$image = new Image();
						$retval = $image->addImages($recipe_id,$_FILES);
					} else {
						$retval["message"] = "Incorrect argument";
					}
				
				} else {
					$retval["message"] = "Recipe not found or not authorized";
				}
			
			} else {
				$retval["message"] = "Incorrect argument, must pass recipe ID";
			}
			
			return $retval;
			
            
        } else if ($this->method == 'PUT') {

			//contains recipe info
			$recipe_obj = json_decode($this->file);
        
        	/*************************************/
			//	UPDATE							  /
			/*************************************/
        	if (isset($this->args[0]) && is_numeric($this->args[0]) && $this->args[0] > 0) {
        	
				$recipe_id = $this->args[0];
				
				$recipe = $recipes->getRecipe($recipe_id);
				
				if ($recipe_id == $recipe["id"]) {
					$recipe_id = $recipe["id"];
				} else {
					$recipe_id = 0;
				}
				
				if (isset($recipe_id) && $recipe_id > 0 && $recipe["user"] == $this->User) {
				
					//Update Recipe
					if (isset($recipe_obj->title)) {
						$recipe_retval = $recipes->updateRecipe((int)$recipe_id,(string)$recipe_obj->title,$this->User,(string)$recipe_obj->user_id,(string)$recipe_obj->prep_time,(string)$recipe_obj->cook_time,(string)$recipe_obj->serves,(int)$recipe_obj->published);
					}
					
					//Delete and Insert Ingredients
					$del_ing_retval = $recipes->deleteIngredients((int)$recipe_id);
										
					if (isset($recipe_obj->ingredients)) {
					
						if (is_array($recipe_obj->ingredients)) {
							foreach($recipe_obj->ingredients as $ingredient) {
								$recipes->createIngredient($recipe_id,(int)$ingredient->sort_order,(string)$ingredient->amount,(string)$ingredient->unit,(string)$ingredient->ingredient);
							}
						} else {
							$recipes->createIngredient($recipe_id,(int)$recipe_obj->ingredients->sort_order,(string)$recipe_obj->ingredients->amount,(string)$recipe_obj->ingredients->unit,(string)$recipe_obj->ingredients->ingredient);
						}
					}
					
					//Delete and Insert Steps
					$del_step_retval = $recipes->deleteSteps((int)$recipe_id);

					if (isset($recipe_obj->steps)) {
						
						if (is_array($recipe_obj->steps)) {
							foreach($recipe_obj->steps as $step) {
								$retval = $recipes->createStep($recipe_id,(int)$step->sort_order,(string)$step->description);
							}
						} else {
							$retval = $recipes->createStep($recipe_id,(int)$recipe_obj->steps->sort_order,(string)$recipe_obj->steps->description);
						}
					}
					
					//Delete and Insert Tags
					$del_tag_retval = $recipes->deleteTags((int)$recipe_id);
										
					if (isset($recipe_obj->tags)) {
						if (is_array($recipe_obj->tags)) {
							foreach($recipe_obj->tags as $tag) {
								$recipes->createTag($recipe_id,(string)$tag->keyword);
							}
						} else {
							$recipes->createTag($recipe_id,(string)$recipe_obj->tags->keyword);
						}
					}
					
					$recipe["retval"] = 1;
					$recipe["message"] = "Success";
				} else {
					$recipe["retval"] = 0;
					$recipe["message"] = "Error retrieving recipe, not authorized or recipe not found";
				}
				
				return $recipe;
				
			/*************************************/
			//	INSERT							  /
			/*************************************/
			} else {

				$recipe_id = $recipes->createRecipe((string)$recipe_obj->title,$this->User,(string)$recipe_obj->user_id,(string)$recipe_obj->prep_time,(string)$recipe_obj->cook_time,(string)$recipe_obj->serves,(int)$recipe_obj->published);
								
				$recipe_id = (int)$recipe_id['rid'];
				
				if ($recipe_id > 0) {
				
					if (isset($recipe_obj->ingredients)) {
					
						if (is_array($recipe_obj->ingredients)) {
							foreach($recipe_obj->ingredients as $ingredient) {
							
								$recipes->createIngredient($recipe_id,(int)$ingredient->sort_order,(string)$ingredient->amount,(string)$ingredient->unit,(string)$ingredient->ingredient);

							}
						} else {
							$recipes->createIngredient($recipe_id,(int)$recipe_obj->ingredients->sort_order,(string)$recipe_obj->ingredients->amount,(string)$recipe_obj->ingredients->unit,(string)$recipe_obj->ingredients->ingredient);
						}
					}
					
					if (isset($recipe_obj->steps)) {
						if (is_array($recipe_obj->steps)) {
							foreach($recipe_obj->steps as $step) {
								$recipes->createStep($recipe_id,(int)$step->sort_order,(string)$step->description);
							}
						} else {
							$recipes->createStep($recipe_id,(int)$recipe_obj->steps->sort_order,(string)$recipe_obj->steps->description);
						}
					}
					
					if (isset($recipe_obj->tags)) {
						if (is_array($recipe_obj->tags)) {
							foreach($recipe_obj->tags as $tag) {
								$recipes->createTag($recipe_id,(string)$tag->keyword);
							}
						} else {
							$recipes->createTag($recipe_id,(string)$recipe_obj->tag->keyword);
						}
					}
				
					$recipe['id'] = $recipe_id;
					$recipe['message'] = "Success";
					$recipe['retval'] = 1;
				} else {
					$recipe['id'] = 0;
					$recipe['message'] = "Insert failed";
					$recipe['retval'] = 0;
				}
				
				return $recipe;
			}
		} else if($this->method == 'DELETE') {
		
			$message = array();
			
			//contains recipe info
			$recipe_obj = json_decode($this->file);
			
			if (is_numeric($this->args[0]) && $this->args[0] > 0) {
				$recipe_id = $this->args[0];
				
				$recipe = $recipes->getRecipe($recipe_id);
				
				if ($recipe["id"] > 0 && $recipe["user"] == $this->User) {
				
					if (sizeof($this->args) == 1) {
						/*************************************/
						//	DELETE RECIPE					  /
						/*************************************/
						//Delete recipe, ingredients and steps
						$recipe = $recipes->deleteRecipe($recipe_id);
						
						$message["retval"] = 1;
						$message["message"] = "Success";
					} else if ($this->args[1] == "image" && is_numeric($this->args[2]) && $this->args[2] > 0) {
				
						//curl -i -X DELETE http://myagi:hilaredarche@myagi.asuscomm.com:9245/api/v1/json/recipe/57/image/10/png -H "Content-Type: application/json"
						/*************************************/
						//	DELETE IMAGE					  /
						/*************************************/
						$iid = $this->args[2];
						$ext = '';
						
						if (isset($this->args[3])) {
							$ext = $this->args[3];
						}
						
						$image = new Image();
						$del_image_retval = $image->deleteImage($recipe_id,(int)$iid,(string)$ext);
						
						$recipe["retval"] = $del_image_retval;
					
					} 
					
					if ($recipe["retval"] == 1) {				
						$message["retval"] = 1;
						$message["message"] = "Success";					
					} else {
						$message["retval"] = 0;
						$message["message"] = "Recipe not found";
					}
				
				} else {
					$message["retval"] = 0;
					$message["message"] = "Error deleting recipe or image: recipe not found or not authorized";
				}
			} else {
				$message["retval"] = 0;
				$message["message"] = "Error deleting recipe or image: argument must be recipe id";
			}
			
			return $message;
        } else {
			$message = array();
			$message["retval"] = 0;
			$message["message"] = "Only accepts GET,POST or DELETE requests, current request: ".$this->method;
            return $message;
        }
     }
     
 }
