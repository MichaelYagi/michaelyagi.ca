<?php

class Application_Model_Recipe extends Zend_Db_Table_Abstract { 
        
	private $user;
	private $pass;
	private $api_url;
	private $api_version;
		
    public function Application_Model_Recipe($user=null,$pass=null) {
		$this->user = $user;
		$this->pass = $pass;
		
		$this->api_version = 1;
		
		if ($this->api_version === 1) {
			$this->api_url = "myagi.asuscomm.com:9245";
		} else {
			$this->api_url = "myagi.asuscomm.com:9246";
		}
		$this->api_url .= "/api/v".$this->api_version."/";
				
		$this->api_format = "json/";
    } 
    
    public function getApiUrl() {
    	return $this->api_url;
    }
	
	public function getAllRecipes() {
		$ch = curl_init('http://'.$this->user.':'.$this->pass.'@'.$this->api_url.$this->api_format.'recipes');

		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");                                                                     
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));    

		$result = curl_exec($ch);
		
		$result = json_decode($result,true);
		
		$ret_arr = array();
		
		if (!isset($result[0])) {
			$ret_arr[0] = $result;
		} else {
			$ret_arr = $result;
		}

		return $ret_arr;
    } 
	
	public function getRecipeByType($type,$value) {
		$ch = curl_init('http://'.$this->user.':'.$this->pass.'@'.$this->api_url.$this->api_format.'recipesByType/'.$type.'/'.$value);
		
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");                                                                     
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));    

		$result = curl_exec($ch);
		
		$result = json_decode($result,true);
		
		$ret_arr = array();
		
		if (!isset($result[0])) {
			$ret_arr[0] = $result;
		} else {
			$ret_arr = $result;
		}
		
		return $ret_arr;
	}
	
	//Get user recipes
	public function getRecipe($recipe_id) {
		$ch = curl_init('http://'.$this->user.':'.$this->pass.'@'.$this->api_url.$this->api_format.'recipe/'.$recipe_id);

		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");                                                                     
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));    

		$result = curl_exec($ch);
		
		$result = json_decode($result,true);
		
		return $result;
    } 
    
    //Add a recipe
    public function addRecipe($recipeData,$userId) {

    	$result = 0;
    	
    	/*
    	echo "<pre>";
    	var_export($recipeData);
    	echo "</pre>";
    	echo "<pre>";
    	var_export($_FILES);
    	echo "</pre>";
    	exit();
    	*/
    	
    	
    	//{"title":"test recipe","user":"myagi","ingredients":[{"sort_order":"1","amount":"2","unit":"cups","ingredient":"sugar"},{"sort_order":"2","amount":"5","unit":"TBS","ingredient":"salt"}],"steps":[{"sort_order":"1","description":"blah 1"},{"sort_order":"2","description":"step 2 - profit"}],"tags":[{"keyword":"sugary"},{"keyword":"salty"}]}
    	try {
    		$recipeArray = array();
		
			$recipeArray["title"] = $recipeData["title"];
			$recipeArray["published"] = $recipeData["publish"];
			$recipeArray["user"] = $this->user;
			$recipeArray["user_id"] = $userId;
			$recipeArray["serves"] = $recipeData["serves"];
			$recipeArray["prep_time"] = $recipeData["prep_time"].":00";
			$recipeArray["cook_time"] = $recipeData["cook_time"].":00";
			
			$sort_count = 1;
			for ($x = 0;$x < sizeof($recipeData["ingredient_amount"]);$x++) {
			
				if ($x == 0) {
					if (!empty($recipeData["ingredient_amount"][$x]) || !empty($recipeData["ingredient_unit"][$x]) || !empty($recipeData["ingredient"][$x])) {
						$recipeArray["ingredients"][$x]["sort_order"] = (string)($sort_count);
						$recipeArray["ingredients"][$x]["amount"] = $recipeData["ingredient_amount"][$x];
						$recipeArray["ingredients"][$x]["unit"] = $recipeData["ingredient_unit"][$x];
						$recipeArray["ingredients"][$x]["ingredient"] = $recipeData["ingredient"][$x];
						$sort_count++;
					}
				} else {
					if (!empty($recipeData["ingredient_amount"]["newIngredientAmount_".($x+1)]) || !empty($recipeData["ingredient_unit"]["newIngredientUnit_".($x+1)]) || !empty($recipeData["ingredient"]["newIngredient_".($x+1)])) {
						$recipeArray["ingredients"][$x]["sort_order"] = (string)($sort_count);
						$recipeArray["ingredients"][$x]["amount"] = $recipeData["ingredient_amount"]["newIngredientAmount_".($x+1)];
						$recipeArray["ingredients"][$x]["unit"] = $recipeData["ingredient_unit"]["newIngredientUnit_".($x+1)];
						$recipeArray["ingredients"][$x]["ingredient"] = $recipeData["ingredient"]["newIngredient_".($x+1)];
						$sort_count++;
					}
				}
			}
			
			$sort_count = 1;
			for ($x = 0;$x < sizeof($recipeData["step"]);$x++) {
				if ($x == 0) {
					if (!empty($recipeData["step"][$x])) {
						$recipeArray["steps"][$x]["sort_order"] = (string)($sort_count);
						$recipeArray["steps"][$x]["description"] = $recipeData["step"][$x];
						$sort_count++;
					}
				} else {
					if (!empty($recipeData["step"]["newStep_".($x+1)])) {
						$recipeArray["steps"][$x]["sort_order"] = (string)($sort_count);
						$recipeArray["steps"][$x]["description"] = $recipeData["step"]["newStep_".($x+1)];
						$sort_count++;
					}
				}
			}
		
			if (!empty($recipeData['tags'])) {
				$tag_array = explode(',', $recipeData['tags']);
				foreach($tag_array as $key => $value) {
					$recipeArray["tags"][$key]["keyword"] = $value;
				}
			}
			
			//Add recipe
			$ch = curl_init('http://'.$this->user.':'.$this->pass.'@'.$this->api_url.$this->api_format.'recipe');
			
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");                                                                     
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($recipeArray));                                                                  
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
				'Content-Type: application/json',                                                                                
				'Content-Length: ' . strlen(json_encode($recipeArray)))                                                                       
			);   
			$result = curl_exec($ch);
			$result = json_decode($result);
			
			if (isset($result->id) && is_numeric($result->id) && $result->id > 0) {
				$recipe_id = (int)$result->id;
			} else {
				//Failed to create recipe id
				return 0;
			}
			
			//Add images
			$image_count=0;
			foreach($_FILES["file"]["tmp_name"] as $key => $value) {
				if (strlen($value) > 0) {
					$image_data["file_".$key] = '@'.$value;
					$image_count++;
				}
			}
			
			foreach($_FILES["file"]["name"] as $key => $value) {
				if (strlen($value) > 0) {
					$image_data["file_".$key] = $image_data["file_".$key].";filename=".$value;
				}
			}
			
			if ($image_count > 0) {
				//curl -i -X POST -F "files=@test.png" http://myagi:hilaredarche@myagi.asuscomm.com:9245/api/v1/json/recipe/59/image
				$ch = curl_init();
				$postFix = ($this->api_version > 1) ? 's' : '';
				curl_setopt($ch, CURLOPT_URL, 'http://'.$this->user.':'.$this->pass.'@'.$this->api_url.$this->api_format.'recipe/'.$recipe_id.'/image'.$postFix);
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $image_data);
				$result = curl_exec($ch);
			}
    	
    		$result = $recipe_id;
		} catch(Exception $e) {
			$result = 0;
		}
		
		return $result;
    	
    }
	
	//Update a recipe
	public function updateRecipe($recipe_id,$recipeData,$original_images_info,$userId) {
	
		/*
		echo "<pre>";
    	var_export($recipeData);
    	echo "</pre>";
    	echo "<pre>";
    	var_export($_FILES);
    	echo "</pre>";
    	exit();
    	*/
	
		$result = 0;
		
		try {
		
			$recipeArray = array();
		
			$recipeArray["title"] = $recipeData["title"];
			$recipeArray["published"] = $recipeData["publish"];
			$recipeArray["user_id"] = $userId;
			$recipeArray["serves"] = $recipeData["serves"];
			$recipeArray["prep_time"] = $recipeData["prep_time"].":00";
			$recipeArray["cook_time"] = $recipeData["cook_time"].":00";
		
			$sort_count = 1;
			for ($x = 0;$x < sizeof($recipeData["ingredient_amount"]);$x++) {
			
				if ($x == 0) {
					if (!empty($recipeData["ingredient_amount"][$x]) || !empty($recipeData["ingredient_unit"][$x]) || !empty($recipeData["ingredient"][$x])) {
						$recipeArray["ingredients"][$x]["sort_order"] = (string)($sort_count);
						$recipeArray["ingredients"][$x]["amount"] = $recipeData["ingredient_amount"][$x];
						$recipeArray["ingredients"][$x]["unit"] = $recipeData["ingredient_unit"][$x];
						$recipeArray["ingredients"][$x]["ingredient"] = $recipeData["ingredient"][$x];
						$sort_count++;
					}
				} else {
					if (!empty($recipeData["ingredient_amount"]["newIngredientAmount_".($x+1)]) || !empty($recipeData["ingredient_unit"]["newIngredientUnit_".($x+1)]) || !empty($recipeData["ingredient"]["newIngredient_".($x+1)])) {
						$recipeArray["ingredients"][$x]["sort_order"] = (string)($sort_count);
						$recipeArray["ingredients"][$x]["amount"] = $recipeData["ingredient_amount"]["newIngredientAmount_".($x+1)];
						$recipeArray["ingredients"][$x]["unit"] = $recipeData["ingredient_unit"]["newIngredientUnit_".($x+1)];
						$recipeArray["ingredients"][$x]["ingredient"] = $recipeData["ingredient"]["newIngredient_".($x+1)];
						$sort_count++;
					}
				}
			}
			
			$sort_count = 1;
			for ($x = 0;$x < sizeof($recipeData["step"]);$x++) {
				if ($x == 0) {
					if (!empty($recipeData["step"][$x])) {
						$recipeArray["steps"][$x]["sort_order"] = (string)($sort_count);
						$recipeArray["steps"][$x]["description"] = $recipeData["step"][$x];
						$sort_count++;
					}
				} else {
					if (!empty($recipeData["step"]["newStep_".($x+1)])) {
						$recipeArray["steps"][$x]["sort_order"] = (string)($sort_count);
						$recipeArray["steps"][$x]["description"] = $recipeData["step"]["newStep_".($x+1)];
						$sort_count++;
					}
				}
			}
		
			if (!empty($recipeData['tags'])) {
				$tag_array = explode(',', $recipeData['tags']);
				foreach($tag_array as $key => $value) {
					$recipeArray["tags"][$key]["keyword"] = $value;
				}
			}
		
			//Update recipe
			$ch = curl_init('http://'.$this->user.':'.$this->pass.'@'.$this->api_url.$this->api_format.'recipe/'.$recipe_id);
		
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");                                                                     
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($recipeArray));                                                                  
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
				'Content-Type: application/json',                                                                                
				'Content-Length: ' . strlen(json_encode($recipeArray)))                                                                       
			);   
			$result = curl_exec($ch);
		
			$serverUrlHelper = new Zend_View_Helper_ServerUrl();
		
			//Delete Images
			if (sizeof($original_images_info) == 1) {
				$request = Zend_Controller_Front::getInstance()->getRequest();

				$original_image_urls = $serverUrlHelper->serverUrl().'/media/recipeimages/'.$recipe_id.'/'.$original_images_info[0]['id'];
				if (!empty($original_images_info['extension'])) {
					$original_image_urls .= ".".$original_images_info['extension'];
				}
			
				$path_parts = pathinfo($original_image_urls);
				if (!in_array($path_parts["filename"],$recipeData["image_ids"])) {
					$curl_string = 'http://'.$this->user.':'.$this->pass.'@'.$this->api_url.$this->api_format.'recipe/'.$recipe_id.'/image/'.$path_parts["filename"];
					if (strlen($path_parts["extension"]) > 0) {
						$curl_string .= '/'.$path_parts["extension"];
					}
					$ch = curl_init($curl_string);
					curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");                                                                     
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
					curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));    

					$result = curl_exec($ch);
				}
			} else {
				foreach($original_images_info as $imageinfo) {
					$request = Zend_Controller_Front::getInstance()->getRequest();

					$image_url = $serverUrlHelper->serverUrl().'/media/recipeimages/'.$recipe_id.'/'.$imageinfo['id'];
					if (!empty($imageinfo['extension'])) {
						$image_url .= ".".$imageinfo['extension'];
					}
					echo "2<br>";
					echo $image_url;exit();
				
					$path_parts = pathinfo($image_url);
					if (!in_array($path_parts["filename"],$recipeData["image_ids"])) {
						$curl_string = 'http://'.$this->user.':'.$this->pass.'@'.$this->api_url.$this->api_format.'recipe/'.$recipe_id.'/image/'.$path_parts["filename"];
						if (strlen($path_parts["extension"]) > 0) {
							$curl_string .= '/'.$path_parts["extension"];
						}
						$ch = curl_init($curl_string);
						curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");                                                                     
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
						curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));    

						$result = curl_exec($ch);
					}
				}
			}
		
			//Add images
			$image_count=0;
			foreach($_FILES["file"]["tmp_name"] as $key => $value) {
				if (strlen($value) > 0) {
					$image_data["file_".$key] = '@'.$value;
					$image_count++;
				}
			}
			
			foreach($_FILES["file"]["name"] as $key => $value) {
				if (strlen($value) > 0) {
					$image_data["file_".$key] = $image_data["file_".$key].";filename=".$value;
				}
			}
			
			if ($image_count > 0) {
				//curl -i -X POST -F "files=@test.png" http://myagi:hilaredarche@myagi.asuscomm.com:9245/api/v1/json/recipe/59/image
				$ch = curl_init();
				$postFix = ($this->api_version > 1) ? 's' : '';
				curl_setopt($ch, CURLOPT_URL, 'http://'.$this->user.':'.$this->pass.'@'.$this->api_url.$this->api_format.'recipe/'.$recipe_id.'/image'.$postFix);
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $image_data);
				$result = curl_exec($ch);
			}
			
			$result = 1;
		} catch(Exception $e) {
			$result = 0;
		}
		
		
		return $result;
	}
	
	//Admin Delete
	public function adminDeleteRecipe($recipe_id) {
		try {   
        	if (!empty($recipe_id) & is_numeric($recipe_id)) {
        		$db = Zend_Db_Table::getDefaultAdapter();
				$data = $db->prepare("CALL recipeDeleteRecipeSP(?)");
				$data->bindParam(1, $recipe_id);
				$data->execute();
				$result = $data->fetchAll(PDO::FETCH_ASSOC);
				$data->closeCursor();  

				$retVal = $result[0]["retval"];
				
        	} else {
        		$retVal = -1;
        	}
               
               
        } catch (Exception $ex) { 
                throw $ex; 
        }       
        
        return $retVal;
	}
	
	//Admin Delete
	public function adminPublishRecipe($recipe_id,$action) {
		$publish = 0;
		if ($action == 3) {
			$publish = 1;
		}
	
		try {   
        	if (!empty($recipe_id) & is_numeric($recipe_id)) {
        		$db = Zend_Db_Table::getDefaultAdapter();
				$data = $db->prepare("CALL recipeUpdatePublishRecipeSP(?,?)");
				$data->bindParam(1, $recipe_id);
				$data->bindParam(2, $publish);
				$data->execute();
				$result = $data->fetchAll(PDO::FETCH_ASSOC);
				$data->closeCursor();  

				$retVal = $result[0]["retval"];
				
        	} else {
        		$retVal = -1;
        	}
               
               
        } catch (Exception $ex) { 
                throw $ex; 
        }       
        
        return $retVal;
	}
	
	//Delete recipe
	public function deleteRecipe($recipe_id) {
		$result = 0;
		
		try {
			$curl_string = 'http://'.$this->user.':'.$this->pass.'@'.$this->api_url.$this->api_format.'recipe/'.$recipe_id;
			
			$ch = curl_init($curl_string);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");                                                                     
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));    

			$result = curl_exec($ch);
			
			$result = json_decode($result,true);
			
		} catch(Exception $e) {
			$result = 0;
		}
		
		return $result;
	}
	
}
