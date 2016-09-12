<?php

namespace App\Http\Controllers;

use DB;
use App\Recipe;
use Illuminate\Http\Request;

class RecipeController extends Controller
{
	
    /**
     * Retrieve all recipes.
     *
     * @return Response
     */
    public function showAll()
    {
		$recipes = DB::select('CALL recipeGetAllRecipesSP');
		
		if (!$recipes)
		{
			$recipes = config('result.empty');
		}
		
        return $recipes;
    }
	
	/**
     * Retrieve recipes by recipe id.
     *
     * @param  int  $recipeid
     * @return Response
     */
    public function show($recipeid)
    {
		$recipe = DB::select('CALL recipeGetRecipeSP(?)',Array($recipeid));
		
		if (!$recipe)
		{
			return config('result.empty');
		}
		
		$recipe = (array) $recipe[0];
		
		$ingredients = DB::select('CALL recipeGetIngredientsSP(?)',Array($recipeid));

		$steps = DB::select('CALL recipeGetStepsSP(?)',Array($recipeid));
		
		$tags = DB::select('CALL recipeGetTagsSP(?)',Array($recipeid));
		
		$images = DB::select('CALL recipeGetRecipeImagesSP(?)',Array($recipeid));
		$imageurls = array();
		
		foreach($images as $image)
		{
			array_push($imageurls,$image);
		}
		
		$recipe["retval"] = 1;
		$recipe["message"] = "Success";
		$recipe['ingredients'] = $ingredients;
		$recipe['steps'] = $steps;
		$recipe['tags'] = $tags;
		$recipe['images_info'] = $imageurls;
		
        return $recipe;
    }
	
	/**
     * Retrieve recipes by type. Valid types are 'user', 'tag' or 'search'.
     *
     * @param  string  $type
	 * @param  string  $value
     * @return Response
     */
    public function showByType($type,$value)
    {
		$recipes = DB::select('CALL recipeGetRecipesByTypeSP(?,?)',Array($type,$value));

		if (!$recipes)
		{
			$recipes = config('result.empty');
		}
		
        return $recipes;
    }
	
	/**
     * Retrieve recipes by tag.
     *
	 * @param  string  $value
     * @return Response
     */
    public function showByTag($value)
    {
		$recipes = DB::select('CALL recipeSearhTagsSP(?)',Array($value));

		if (!$recipes)
		{
			$recipes = config('result.empty');
		}
		
        return $recipes;
    }
	
	/**
     * Update recipe by recipe id.
     *
     * @param  Request  $request
	 * @param  int	$recipeid
     * @return Response
     */
	public function update(Request $request,$recipeid)
	{	
		$recipe = DB::select('CALL recipeGetRecipeSP(?)',Array($recipeid));
		$recipe = $recipe[0];

		if (!$recipe->user || $request->user()[0]->userid != $recipe->user_id || $recipe->id != $recipeid)
		{
			return config('result.unauthorized');
		}
		
		$title = '';
		$prepTime = '';
		$cookTime = '';
		$serves = '';
		$published = 0;
					
		if ($request->isJson()) 
		{
			// Update Recipe
			$title = $request->json()->get('title');
			$prepTime = $request->json()->get('prep_time');
			$cookTime = $request->json()->get('cook_time');
			$serves = $request->json()->get('serves');
			$published = $request->json()->get('published');
			
			$recipe_retval = DB::select('CALL recipeUpdateRecipeSP(?,?,?,?,?,?,?,?)',
										Array(
											$recipeid,
											$title,
											$recipe->user,
											$recipe->user_id,
											$prepTime,
											$cookTime,
											$serves,
											$published
										)
			);
			
			// Delete and Insert Ingredients
			$ing_retval = DB::select('CALL recipeDeleteIngredientSP(?)',Array($recipeid));
			$ingredients = $request->json()->get('ingredients');	
			if ($this->isAssoc($ingredients))
			{
				$ingredients = Array($ingredients);
			}
			foreach($ingredients as $ingredient)
			{
				$ing_retval_retval = DB::select('CALL recipeCreateIngredientSP(?,?,?,?,?)',
											Array(
												$recipeid,
												$ingredient["sort_order"],
												$ingredient["amount"],
												$ingredient["unit"],
												$ingredient["ingredient"]
											)
				);	
			}
			
			// Delete and Insert Steps
			$steps_retval = DB::select('CALL recipeDeleteStepSP(?)',Array($recipeid));
			$steps = $request->json()->get('steps');
			if ($this->isAssoc($steps))
			{
				$steps = Array($steps);
			}
			foreach($steps as $step)
			{
				$step_retval = DB::select('CALL recipeCreateStepSP(?,?,?)',
											Array(
												$recipeid,
												$step["sort_order"],
												$step["description"]
											)
				);	
			}
			
			// Delete and Insert Steps
			$tags_retval = DB::select('CALL recipeDeleteTagSP(?)',Array($recipeid));
			$tags = $request->json()->get('tags');	
			if ($this->isAssoc($tags))
			{
				$tags = Array($tags);
			}			
			foreach($tags as $tag)
			{
				$tag_retval = DB::select('CALL recipeCreateTagSP(?,?)',Array($recipeid,$tag["keyword"]));	
			}
			
			$recipe = $this->show($recipeid);
			return $recipe;
		}
		
		return config('result.incorrect');
	}
	
	/**
     * Delete recipe by recipe id.
     *
     * @param  Request  $request
	 * @param  int	$recipeid
     * @return Response
     */
	public function remove(Request $request,$recipeid)
    {
		$recipe = DB::select('CALL recipeGetRecipeSP(?)',Array($recipeid));
		$recipe = $recipe[0];

		if (!$recipe->user || $request->user()[0]->userid != $recipe->user_id || $recipe->id != $recipeid)
		{
			return config('result.unauthorized');
		}
		
		$images = DB::select('CALL recipeGetRecipeImagesSP(?)',Array($recipeid));
		$imageDir = config('dir.image');
	
		if (sizeof($images) > 0) {
			if (isset($images[0])) {
				for($x = 0;$x < sizeof($images);$x++) {
					$image = $images[$x];
					
					if (strlen($image["extension"]) > 0) {
						$file_mask = $imageDir.$recipeid."/".$image->id.".".strtolower($image->extension);
					} else {
						$file_mask = $imageDir.$recipeid."/".$image->id.".*";
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
					$file_mask = $imageDir.$recipeid."/".$images->id.".".strtolower($images->extension);
				} else {
					$file_mask = $imageDir.$recipeid."/".$images->id.".*";
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
			
			if (is_dir($imageDir.$recipeid)) {
				rmdir($imageDir.$recipeid);
			}
		}
	
		$recipe = DB::select('CALL recipeDeleteRecipeSP(?)',Array($recipeid));
		$retval = $recipe[0];
		
		if ($retval <= 0)
		{
			return config('result.fail');
		}
		
		return config('result.success');
    }
	
	/**
     * Add a new recipe. Returns a recipeid.
     *
     * @param  Request  $request
     * @return Response
     */
	public function add(Request $request)
	{		

		if ($request->user()[0]->userid <= 0)
		{
			return config('result.unauthorized');
		}
		
		$userid = $request->user()[0]->userid;
		$user = DB::select('CALL commonGetUserInfoByIdSP(?)',Array($userid));
		$user = $user[0];
		
		$user = $user->username;
		$title = '';
		$prepTime = '';
		$cookTime = '';
		$serves = '';
		$published = 0;
					
		if ($request->isJson()) 
		{
			// Update Recipe
			$title = $request->json()->get('title');
			$prepTime = $request->json()->get('prep_time');
			$cookTime = $request->json()->get('cook_time');
			$serves = $request->json()->get('serves');
			$published = $request->json()->get('published');
			
			$recipeid = DB::select('CALL recipeCreateRecipeSP(?,?,?,?,?,?,?)',
										Array(
											$title,
											$user,
											$userid,
											$prepTime,
											$cookTime,
											$serves,
											$published
										)
			);
			$recipeid = $recipeid[0]->rid;
				
			if ($recipeid > 0) {
				
				$ingredients = $request->json()->get('ingredients');	
				if ($this->isAssoc($ingredients))
				{
					$ingredients = Array($ingredients);
				}
				foreach($ingredients as $ingredient)
				{
					$ing_retval_retval = DB::select('CALL recipeCreateIngredientSP(?,?,?,?,?)',
												Array(
													$recipeid,
													$ingredient["sort_order"],
													$ingredient["amount"],
													$ingredient["unit"],
													$ingredient["ingredient"]
												)
					);	
				}
				
				// Delete and Insert Steps
				$steps = $request->json()->get('steps');
				if ($this->isAssoc($steps))
				{
					$steps = Array($steps);
				}
				foreach($steps as $step)
				{
					$step_retval = DB::select('CALL recipeCreateStepSP(?,?,?)',
												Array(
													$recipeid,
													$step["sort_order"],
													$step["description"]
												)
					);	
				}
				
				// Delete and Insert Steps
				$tags = $request->json()->get('tags');	
				if ($this->isAssoc($tags))
				{
					$tags = Array($tags);
				}			
				foreach($tags as $tag)
				{
					$tag_retval = DB::select('CALL recipeCreateTagSP(?,?)',Array($recipeid,$tag["keyword"]));	
				}
				
				$recipe = $this->show($recipeid);
				return $recipe;
			}
		}
		
		return config('result.incorrect');
	}
	
	/**
     * Retrieve recipe images by recipe id.
     *
     * @param  int  $recipeid
     * @return Response
     */
    public function showImages($recipeid)
    {
		$images = DB::select('CALL recipeGetRecipeImagesSP(?)',Array($recipeid));
		$imageurls = array();
		$imageDir = config('dir.image');

		if (sizeof($images) > 0) {
			if (isset($images[0])) {
				for($x = 0;$x < sizeof($images);$x++) {
					$image = $images[$x];
					
					if (strlen($image->extension) > 0) {
						$url = $imageDir.$recipeid."/".$image->id.".".$image->extension;
					} else {
						$url = $imageDir.$recipeid."/".$image->id;
					}
					
					array_push($imageurls,$url);
				}
			} else {
				if (strlen($images["extension"]) > 0) {
					$imageurls = $imageDir.$recipeid."/".$images->id.".".$images->extension;
				} else {
					$imageurls = $imageDir.$recipeid."/".$images->id;
				}
			}
		}
		
		return $imageurls;
    }
	
	/**
     * Add images by recipe id.
     *
     * @param  Request  $request
	 * @param  int	$recipeid
     * @return Response
     */
	public function addImages(Request $request,$recipeid)
	{	
		$recipe = DB::select('CALL recipeGetRecipeSP(?)',Array($recipeid));
		$recipe = $recipe[0];

		if (!$recipe->user || $request->user()[0]->userid != $recipe->user_id || $recipe->id != $recipeid)
		{
			return config('result.unauthorized');
		}
		
		$retval["retval"] = false;
		$imageDir = config('dir.image');
	
		$filedirectory = $imageDir.$recipeid;
		
		//If directory DNE, create it
		if (!file_exists($filedirectory)) {
			if (!mkdir($filedirectory, 0777)) {
				//die('Failed to create directory:'.$filedirectory);
				$retval["message"] = "Failed to create directory:".$filedirectory;
				return $retval;
			}
		}
		
		$file_array = $_FILES;
		
		if (sizeof($file_array) == 0) {
			$retval["message"] = "Incorrect file path or no file present:";
		}

		foreach($file_array as $key => $value) { 
	
			$name = $value["name"];
			$ext = end((explode(".", $name)));
			$ext = strtolower($ext);
			
			$imageid = DB::select('CALL recipeCreateImageIdSP(?,?)',Array($recipeid,$ext));
			$imageid = $imageid[0];
			$imageid = $imageid->iid;

			if ($imageid > 0) {
				$filename = $filedirectory."/".$imageid.".".$ext;
			
				//Save the file
				if (move_uploaded_file($value["tmp_name"], $filename)) {
					chmod($filename,0755);
					$retval["imageid"] = $imageid;
					$retval["retval"] = true;
					$retval["message"] = "Image saved";
				} else {
					$retval["imageid"] = 0;
					$retval["retval"] = false;
					$retval["message"] = "Image could not be saved";
				}
			} else {
				$retval["imageid"] = 0;
				$retval["retval"] = false;
				$retval["message"] = "Image ID could not be created";
			}
		
		}
		
		return $retval;
	}
	
	/**
     * Delete image by recipe id, image id and image file extension.
     *
     * @param  Request  $request
	 * @param  int	$recipeid
	 * @param  int	$imageid
	 * @param  string	$imageext
     * @return Response
     */
	public function removeImage(Request $request,$recipeid,$imageid,$imagext='')
	{	
		$recipe = DB::select('CALL recipeGetRecipeSP(?)',Array($recipeid));
		$recipe = $recipe[0];

		if (!$recipe->user || $request->user()[0]->userid != $recipe->user_id || $recipe->id != $recipeid)
		{
			return config('result.unauthorized');
		}
		
		$retval = false;
		$file_unlinked = false;
		$imageDir = config('dir.image');
	
		if ($imageid > 0) {
			if (strlen($imagext) > 0) {
				$file_mask = $imageDir.$recipeid."/".$imageid.".".strtolower($imagext);
			} else {
				$file_mask = $imageDir.$recipeid."/".$imageid.".*";
			}
			
			try {
				foreach(glob($file_mask) as $f) {
					unlink($f);
				}
				$file_unlinked = true;
			} catch(Exception $e) {
				$retval = false;
			}
		
			if ($file_unlinked) {
				try {
					$retval = DB::select('CALL recipeDeleteImageSP(?)',Array($imageid));
					$retval = true;
				} catch(Exception $e) {
					$retval = false;
				}
			}
		
		}
		
		return $retval;
	}
	
	private function isAssoc($arr)
	{
		return array_keys($arr) !== range(0, count($arr) - 1);
	}
}