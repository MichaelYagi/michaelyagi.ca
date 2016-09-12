<?php

require_once('../wr/WR.class.php');
require_once('../Log.class.php');

class Recipe {
	
	protected $WebService;
	private $baseImageDir;
	
	public function __construct() {
		$this->WebService = new WebRequest();
		$this->baseImageDir = "/var/www/michaelyagi.ca/public/media/recipeimages/";
	}
	
	public function getAllRecipes() {
		$recipes = $this->WebService->setMethod('recipeGetAllRecipesSP')->getData();
			
		if (sizeof($recipes) == 1)  {
			$recipes = $recipes[0];
		}
		
		$recipes["retval"] = 1;
		$recipes["message"] = "Success";
		
		return $recipes;
	}
	
	public function getRecipesByType($type,$value) {
		$recipes = $this->WebService->setMethod('recipeGetRecipesByTypeSP')->setParam("type",$type)->setParam("value",$value)->getData();
			
		if (sizeof($recipes) == 0) {
			$recipes["retval"] = 0;
			$recipes["message"] = "No Results";
		} else if (sizeof($recipes) == 1)  {
			$recipes = $recipes[0];
			$recipes["retval"] = 1;
			$recipes["message"] = "Success";
		} else {
			$recipes["retval"] = 1;
			$recipes["message"] = "Success";
		}
		
		return $recipes;
	}
	
	public function searchTags($search_term) {
		$tags = $this->WebService->setMethod('recipeSearhTagsSP')->setParam("r_search",$search_term)->getData();
		
		if (sizeof($tags) > 0) {
			$tags["retval"] = 1;
			$tags["message"] = "Success";
		} else {
			$tags["retval"] = 0;
			$tags["message"] = "No Results";
		}
	
		return $tags;
		
	}
	
	public function getRecipe($recipe_id) {
	
		$recipe = $this->WebService->setMethod('recipeGetRecipeSP')->setParam("rid",$recipe_id)->getData();
				
		if (isset($recipe["id"]) && $recipe["id"] > 0) {
		
			$ingredients = $this->WebService->setMethod('recipeGetIngredientsSP')->setParam("rid",$recipe_id)->getData();
	
			$steps = $this->WebService->setMethod('recipeGetStepsSP')->setParam("rid",$recipe_id)->getData();
			
			$tags = $this->WebService->setMethod('recipeGetTagsSP')->setParam("rid",$recipe_id)->getData();
			
			$images = $this->WebService->setMethod('recipeGetRecipeImagesSP')->setParam("rid",$recipe_id)->getData();
			$imageurls = array();
			
			if (sizeof($images) > 0) {
				if (isset($images[0])) {
					for($x = 0;$x < sizeof($images);$x++) {
						$image = $images[$x];
						
						array_push($imageurls,$image);
					}
				} else {
					array_push($imageurls,$images);
				}
			}
			
			$recipe["retval"] = 1;
			$recipe["message"] = "Success";
			$recipe['ingredients'] = $ingredients;
			$recipe['steps'] = $steps;
			$recipe['tags'] = $tags;
			$recipe['images_info'] = $imageurls;
			
		} else {
			$recipe["retval"] = 0;
			$recipe["message"] = "Error retrieving recipe or recipe does not exist";
		}
		
		return $recipe;
	}
	
	public function getRecipeImageInfo($recipe_id) {
		$images_info = $this->WebService->setMethod('recipeGetRecipeImageInfoSP')->setParam("rid",$recipe_id)->getData();
		
		$images["retval"] = 1;
		$images["message"] = "Success";
		$images['info'] = $images_info;
		
		return $images;
	}
	
	public function deleteRecipe($recipe_id) {
	
		$images = $this->WebService->setMethod('recipeGetRecipeImagesSP')->setParam("rid",$recipe_id)->getData();
	
		if (sizeof($images) > 0) {
			if (isset($images[0])) {
				for($x = 0;$x < sizeof($images);$x++) {
					$image = $images[$x];
					
					if (strlen($image["extension"]) > 0) {
						$file_mask = $this->baseImageDir.$recipe_id."/".$image["id"].".".strtolower($image["extension"]);
					} else {
						$file_mask = $this->baseImageDir.$recipe_id."/".$image["id"].".*";
					}
					
					try {
						unlink($file_mask);
						$retval = true;
					} catch(Exception $e) {
						$retval = false;
					}
					
				}
			} else {
				if (strlen($images["extension"]) > 0) {
					$file_mask = $this->baseImageDir.$recipe_id."/".$images["id"].".".strtolower($images["extension"]);
				} else {
					$file_mask = $this->baseImageDir.$recipe_id."/".$images["id"].".*";
				}
				
				try {
					foreach(glob($file_mask) as $f) {
						unlink($f);
					}
					$retval = true;
				} catch(Exception $e) {
					$retval = false;
				}
			}
			
			if (is_dir($this->baseImageDir.$recipe_id)) {
				rmdir($this->baseImageDir.$recipe_id);
			}
		}
	
		$recipe = $this->WebService->setMethod('recipeDeleteRecipeSP')->setParam("rid",$recipe_id)->getData();
		
		return $recipe;
	}
	
	public function createRecipe($title,$user,$user_id,$prep,$cook,$serves,$published) {
	
		$recipe_id = $this->WebService->setMethod('recipeCreateRecipeSP')
							->setParam("title",$title)
							->setParam("user",$user)
							->setParam("user_id",$user_id)
							->setParam("prep",$prep)
							->setParam("cook",$cook)
							->setParam("serves",$serves)
							->setParam("published",$published)
							->getData();
		return $recipe_id;
	}
	
	public function updateRecipe($recipe_id,$title,$user,$user_id,$prep,$cook,$serves,$published) {
	
		$retval = $this->WebService->setMethod('recipeUpdateRecipeSP')
						->setParam("rid",$recipe_id)
						->setParam("title",$title)
						->setParam("user",$user)
						->setParam("user_id",$user_id)
						->setParam("prep",$prep)
						->setParam("cook",$cook)
						->setParam("serves",$serves)
						->setParam("published",$published)
						->getData();
					
		return $retval;
	}
	
	public function deleteIngredients($recipe_id) {
		$retval = $this->WebService->setMethod('recipeDeleteIngredientSP')
									->setParam("rid",$recipe_id)
									->getData();
										
		return $retval;
	}
	
	public function createIngredient($recipe_id,$sort_order,$amount,$unit,$ingredient) {
		$retval = $this->WebService->setMethod('recipeCreateIngredientSP')
						->setParam("recipe_id",$recipe_id)
						->setParam("sort_order",$sort_order)
						->setParam("amount",$amount)
						->setParam("unit",$unit)
						->setParam("ingredient",$ingredient)
						->getData();
						
		
	}
	
	public function deleteSteps($recipe_id) {
		$retval = $this->WebService->setMethod('recipeDeleteStepSP')
									->setParam("rid",$recipe_id)
									->getData();
										
		return $retval;
	}
	
	public function createStep($recipe_id,$sort_order,$description) {

		$retval = $this->WebService->setMethod('recipeCreateStepSP')
									->setParam("recipe_id",$recipe_id)
									->setParam("sort_order",$sort_order)
									->setParam("description",$description)
									->getData();
									
		return $retval;							
							
	}
	
	public function createTag($recipe_id,$keyword) {
		$this->WebService->setMethod('recipeCreateTagSP')
							->setParam("recipe_id",$recipe_id)
							->setParam("r_keyword",$keyword)
							->getData();
	}
	
	public function deleteTags($recipe_id) {
		$retval = $this->WebService->setMethod('recipeDeleteTagSP')
									->setParam("rid",$recipe_id)
									->getData();
		return $retval;
	}
}
