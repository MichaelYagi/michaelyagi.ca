<?php

require_once('../wr/WR.class.php');

class Image {

	private $baseImageDir;
	private $WebService;

	public function __construct() {
		$this->baseImageDir = "/var/www/michaelyagi.ca/public/media/recipeimages/";
		$this->WebService = new WebRequest();
	}
	
	public function getImages($recipeID) {
		$images = $this->WebService->setMethod('recipeGetRecipeImagesSP')->setParam("rid",$recipeID)->getData();
		$urls = array();
		
		if (sizeof($images) > 0) {
			if (isset($images[0])) {
				for($x = 0;$x < sizeof($images);$x++) {
					$image = $images[$x];
					
					if (strlen($image["extension"]) > 0) {
						$url = $this->baseImageDir.$recipe_id."/".$image["id"].".".$image["extension"];
					} else {
						$url = $this->baseImageDir.$recipe_id."/".$image["id"];
					}
					
					array_push($imageurls,$url);
				}
			} else {
				if (strlen($images["extension"]) > 0) {
					$imageurls = $this->baseImageDir.$recipe_id."/".$images["id"].".".$images["extension"];
				} else {
					$imageurls = $this->baseImageDir.$recipe_id."/".$images["id"];
				}
			}
		}
		
		return $urls;
	}
	
	public function getImage($recipeID,$imageID,$ext) {
		
		if ($imageID > 0) {
			if (strlen($ext) > 0) {
				$url = $this->baseImageDir.$recipeID."/".$imageID.".".strtolower($ext);
			} else {
				$url = $this->baseImageDir.$recipeID."/".$imageID;
			}
		}
		
		return $url;
	}
	
	public function addImages($recipeID,$file_array) {
	
		$retval["retval"] = false;
	
		$filedirectory = $this->baseImageDir.$recipeID;
		
		//If directory DNE, create it
		if (!file_exists($filedirectory)) {
			if (!mkdir($filedirectory, 0777)) {
				//die('Failed to create directory:'.$filedirectory);
				$retval["message"] = "Failed to create directory:".$filedirectory;
				return $retval;
			}
		}
		
		if (sizeof($file_array) == 0) {
			$retval["message"] = "Incorrect file path or no file present:";
		}

		foreach($file_array as $key => $value) { 
	
			$name = $value["name"];
			$ext = end((explode(".", $name)));
			$ext = strtolower($ext);
			
			$imageID = $this->WebService->setMethod('recipeCreateImageIdSP')->setParam("rid",$recipeID)->setParam("ext",$ext)->getData();

			$imageID = $imageID["iid"];

			if ($imageID > 0) {
				$filename = $filedirectory."/".$imageID.".".$ext;
			
				//Save the file
				if (move_uploaded_file($value["tmp_name"], $filename)) {
					chmod($filename,0755);
					$retval["imageid"] = $imageID;
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
	
	public function deleteImage($recipeID,$imageID,$ext) {
		$retval = false;
		$file_unlinked = false;
		
		if ($imageID > 0) {
			if (strlen($ext) > 0) {
				$file_mask = $this->baseImageDir.$recipeID."/".$imageID.".".strtolower($ext);
			} else {
				$file_mask = $this->baseImageDir.$recipeID."/".$imageID.".*";
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
					$this->WebService->setMethod('recipeDeleteImageSP')->setParam("iid",$imageID)->getData();
					$retval = true;
				} catch(Exception $e) {
					$retval = false;
				}
			}
		
		}
		
		return $retval;
	}

}
