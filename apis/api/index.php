<!DOCTYPE html>
<html><head>
<title>Recipe API</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<meta name="description" content="" />
<meta name="copyright" content="" />
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap-theme.min.css">
<style>
body {
  padding-top: 40px;
}
section {
padding-top:50px;
margin-top:-40px;
}
</style>
</head>
<body>

<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

function url_origin($s, $use_forwarded_host=false) {
	$ssl = (!empty($s['HTTPS']) && $s['HTTPS'] == 'on') ? true:false;
    $sp = strtolower($s['SERVER_PROTOCOL']);
    $protocol = substr($sp, 0, strpos($sp, '/')) . (($ssl) ? 's' : '');
    $host = url_origin_host($s, $use_forwarded_host);
    
    return $protocol . '://' . $host;
}
function url_origin_host($s, $use_forwarded_host=false) {
	$ssl = (!empty($s['HTTPS']) && $s['HTTPS'] == 'on') ? true:false;
    $sp = strtolower($s['SERVER_PROTOCOL']);
	$port = $s['SERVER_PORT'];
    $port = ((!$ssl && $port=='80') || ($ssl && $port=='443')) ? '' : ':'.$port;
    $host = ($use_forwarded_host && isset($s['HTTP_X_FORWARDED_HOST'])) ? $s['HTTP_X_FORWARDED_HOST'] : (isset($s['HTTP_HOST']) ? $s['HTTP_HOST'] : null);
    $host = isset($host) ? $host : $s['SERVER_NAME'] . $port;
    
    return $host;
}
function full_host($s, $use_forwarded_host=false) {
    return url_origin_host($s, $use_forwarded_host) . $s['REQUEST_URI'];
}
function full_url($s, $use_forwarded_host=false) {
    return url_origin($s, $use_forwarded_host) . $s['REQUEST_URI'];
}

function isSecure() {
  return
    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || $_SERVER['SERVER_PORT'] == 443;
}

$absolute_host = full_host($_SERVER);
$absolute_url = full_url($_SERVER);

$current_url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$base_url_array = parse_url($current_url); 
$schema = isSecure() ? 'https' : 'http';
$host = $base_url_array['host'];

$version = 'v1';
$port = '9245';
if (isset($_GET['version'])) {
	if ($_GET['version'] === 'v2') {
		$version = 'v2';
		$port = '9246';
	}
}

$apiBaseUrlExample = $schema."://".$host.":".$port."/".$version."/";
$apiBaseUrlPassExample = $schema."://{user}:{pass}@".$host.":".$port."/".$version."/";
?>

<!-- Fixed navbar -->
<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
  <div class="container">
	<div class="navbar-header">
	  <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
		<span class="sr-only">Toggle navigation</span>
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
	  </button>
	  <a class="navbar-brand" href="/api/?version=v2">Recipe API</a>
	</div>
	<div id="navbar" class="navbar-collapse collapse">
	  <ul class="nav navbar-nav">
	    <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Version <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="/api/?version=v2">v2</a></li>
            <li><a href="/api/?version=v1">v1</a></li>
          </ul>
        </li>
		<li><a href="#api-about">About</a></li>
		<li><a href="#api-format">Format</a></li>
		<li><a href="#api-index">Index</a></li>
		<li><a href="#api-recipes">Recipes</a></li>
		<li><a href="#api-images">Recipe Images</a></li>
		<li><a href="#api-users">Users</a></li>
		<li><a href="#api-account">Account</a></li>
	  </ul>
	</div><!--/.nav-collapse -->
  </div>
</nav>

<div class="container">

<!-- About section -->
<section id="api-about">
<h3>About</h3>
<p>I wanted to learn how to implement and use RESTful web services. The following were great resources while implementing and testing the API:
<ul>
	<li><a href="http://www.restapitutorial.com/lessons/httpmethods.html">REST API TUTORIAL</a></li>
	<li><a href="http://coreymaynard.com/blog/creating-a-restful-api-with-php/">Creating a RESTful API with PHP</a></li>
	<li><a href="http://www.redmine.org/projects/redmine/wiki/Rest_api_with_curl">Using the REST API with cURL</a></li>
	<li><a href="http://www.ivangabriele.com/php-how-to-use-4-methods-delete-get-post-put-in-a-restful-api-client-using-curl/">RESTful API client using cURL</a></li>
</ul>
</p>

<p>The <a href="http://myagi.asuscomm.com:9244/food">recipe book</a> utilizes this API. Below is work in progress API documentation and usage examples.</p>

<p>PUT, POST and DELETE requests can only be completed by <a href="http://myagi.asuscomm.com:9244/food/register">registered</a> users.</p>
</section>

<!-- Format section -->
<section id="api-format">
<h3>Format</h3>
<p>
Return XML: <code><?php echo $apiBaseUrlExample; ?>xml/</code><br>
Return JSON: <code><?php echo $apiBaseUrlExample; ?>json/</code>
</p>
</section>

<!-- Index section -->
<section id="api-index">
<h3>Index</h3>
<ul>
	<li><a href="#api-recipes">recipes</a> [<a href="#api-getrecipes">GET</a>]</li>
	<li><a href="#api-recipesbytype">recipesByType/{type}/{value}</a> [<a href="#api-getrecipesbytype">GET</a>]</li>
	<li><a href="#api-recipeid">recipe/{recipe_id}</a> [<a href="#api-getrecipeid">GET</a>][<a href="#api-putrecipeid">PUT</a>][<a href="#api-deleterecipeid">DELETE</a>]</li>
	<li><a href="#api-recipe">recipe</a> [<a href="#api-putrecipe">PUT</a>]</li>
	<li><a href="#api-images">recipe/{recipe_id}/images</a> [<a href="#api-getimages">GET</a>]</li>
	<li><a href="#api-imageid">recipe/{recipe_id}/image</a> [<a href="#api-postimage">POST</a>][<a href="#api-deleteimage">DELETE</a>]</li>
	<li><a href="#api-tags">tag/{value}</a> [<a href="#api-gettag">GET</a>]
	<li><a href="#api-user">user</a> [<a href="#api-putuser">PUT</a>]</li>
	<li><a href="#api-username">user/{username}</a> [<a href="#api-getusername">GET</a>][<a href="#api-putusername">PUT</a>]</li>
	<li><a href="#api-usernameverify">user/{username}/verify</a> [<a href="#api-putusernameverify">PUT</a>]</li>
	<li><a href="#api-recover">recover</a> [<a href="#api-putrecover">PUT</a>]</li>
</ul>
</section>

<section id="api-recipes">
<h3>Recipes</h3>
</section>

<!-- Get all recipes -->
<section id="api-recipes">

<div class="panel panel-default">
	<div class="panel-title">
		<code>recipes</code>
	</div>
	
	<div class="panel-body">
	
		<section id="api-getrecipes">
		<span class="label label-default">GET</span><br><br>
  
		Get all recipes.
		
		<br><br>
		
		Example of PHP cURL request:<br>

		<pre>
$ch = curl_init('<?php echo $apiBaseUrlExample; ?>json/recipes');

curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");                                                                     
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));    

$result = curl_exec($ch);
		</pre>
		</section>

	</div>
	
</div>
	
</section>
				
				
<!-- Filter by type -->
<section id="api-recipesbytype">
<div class="panel panel-default">
	<div class="panel-title">
		<code>recipesByType/{type}/{value}</code>
	</div>
	<div class="panel-body">
	
		<table class="table table-condensed">
			<tr>
				<th>parameter</th>
				<th>value</th>
				<th>description</th>
			</tr>
			
			<tr>
				<td>type</td>
				<td>string</td>
				<td>A string to filter on a {value}. Valid types are <em>tag</em>, <em>user</em> or <em>search</em></td>
			</tr>
			
			<tr>
				<td>value</td>
				<td>string</td>
				<td>A string to be used as a value on a {type}. Either a <em>tag</em> or <em>search</em> keyword or <em>user</em> username</td>
			</tr>
			
		</table>
		
		<section id="api-getrecipesbytype">
		<span class="label label-default">GET</span><br><br>
		  
		Filter recipes by username, tag keyword or search term. The search term searches on tag, title, ingredient or step of a recipe.
		
		<br><br>
		
		Example of PHP cURL request, get all recipes tagged as <em>savory</em>:<br>
		<pre>
$ch = curl_init('<?php echo $apiBaseUrlExample; ?>json/recipesByType/tag/savory');

curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");                                                                     
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));    

$result = curl_exec($ch);
		</pre>
		</section>
		
	</div>
</div>
	
</section>	
				
				
<!-- Recipe by ID -->
<section id="api-recipeid">
<div class="panel panel-default">
	<div class="panel-title">
		<code>recipe/{recipe_id}</code>
	</div>
	<div class="panel-body">
	
		<table class="table table-condensed">
			<tr>
				<th>parameter</th>
				<th>value</th>
				<th>description</th>
			</tr>
			
			<tr>
				<td>recipe_id</td>
				<td>int</td>
				<td>A recipe's ID</td>
			</tr>
			
		</table>
		
		<section id="api-getrecipeid">
		<span class="label label-default">GET</span><br><br>
  
		Get a recipe by recipe ID.
		
		<br><br>
		
		Example of JSON cURL request and response:<br>

<pre>
Request:
curl -i -H "Accept: application/json" -X GET -H "Content-Type: application/json" <?php echo $apiBaseUrlExample; ?>json/recipe/90

Response:
{  
   "id":"90",
   "title":"Cider-Glazed Honey-Baked Ham",
   "prep_time":"00:10:00",
   "cook_time":"01:00:00",
   "serves":"8",
   "user":"myagi",
   "added":"2014-12-21 22:46:52",
   "modified":"2014-12-21 22:46:52",
   "retval":1,
   "message":"Success",
   "ingredients":[  
      {  
         "sort_order":"1",
         "amount":"4 to 5",
         "unit":"pounds",
         "ingredient":"fully cooked boneless cured ham"
      },
      {  
         "sort_order":"2",
         "amount":"1",
         "unit":"cup",
         "ingredient":"apple or pear cider"
      },
      {  
         "sort_order":"3",
         "amount":"1/4",
         "unit":"cup",
         "ingredient":"firmly packed brown sugar"
      },
      {  
         "sort_order":"4",
         "amount":"1/4",
         "unit":"cup",
         "ingredient":"country-style Dijon mustard"
      },
      {  
         "sort_order":"5",
         "amount":"1/4",
         "unit":"cup",
         "ingredient":"honey"
      },
      {  
         "sort_order":"6",
         "amount":"",
         "unit":"",
         "ingredient":"Red, green and yellow apple slices"
      }
   ],
   "steps":[  
      {  
         "sort_order":"1",
         "description":"Heat oven to 350° F."
      },
      {  
         "sort_order":"2",
         "description":"Place ham into ungreased 13x9-inch baking pan."
      },
      {  
         "sort_order":"3",
         "description":"Pour apple cider over ham."
      },
      {  
         "sort_order":"4",
         "description":"Stir together all remaining ingredients except apple slices in medium bowl."
      },
      {  
         "sort_order":"5",
         "description":"Spoon sauce over ham."
      },
      {  
         "sort_order":"6",
         "description":"Bake, basting every 15 minutes with pan juices, for 60 to 70 minutes or until heated through."
      },
      {  
         "sort_order":"7",
         "description":"Serve carved ham with pan juices; garnish with apple slices."
      }
   ],
   "tags":[  
      {  
         "keyword":"ham"
      },
      {  
         "keyword":"honey"
      },
      {  
         "keyword":"glazed"
      },
      {  
         "keyword":"holiday"
      }
   ],
   "image_urls":[  
      "http://myagi.asuscomm.com:9244/media/recipeimages/90/48.JPG",
      "http://myagi.asuscomm.com:9244/media/recipeimages/90/49.JPG"
   ]
}
</pre>
	</section>	
	
	<section id="api-putrecipeid">
		<span class="label label-default">PUT</span><br><br>
		
		Allows users to update a recipe by recipe ID.
		
		<br><br>
		
		Example of PHP cURL request and response:<br>
		
<pre>
Request:

//Update recipe
$ch = curl_init('<?php echo $apiBaseUrlPassExample; ?>json/recipe/59');

curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");                                                                     
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dataArray));                                                                  
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
	'Content-Type: application/json',                                                                                
	'Content-Length: ' . strlen(json_encode($dataArray)))                                                                       
);   
$result = curl_exec($ch);

Response:
{  
   "title":"test recipe",
   "user":"myagi",
   "serves":"7",
   "prep_time":"00:01:00",
   "cook_time":"05:00:00",
   "ingredients":[  
      {  
         "sort_order":"1",
         "amount":"5",
         "unit":"TBS",
         "ingredient":"salt"
      }
   ],
   "steps":[  
      {  
         "sort_order":"1",
         "description":"step 1"
      },
      {  
         "sort_order":"2",
         "description":"step 2 - profit"
      }
   ],
   "tags":[  
      {  
         "keyword":"salty"
      }
   ]
}
</pre>
		
	</section>
	
	<section id="api-deleterecipeid">
		<span class="label label-default">DELETE</span><br><br>
		
		Allows users to delete a recipe by recipe ID.
		
		<br><br>
		
		Example of cURL request:<br>
<pre>
curl -i -H "Accept: application/json" -X DELETE -H "Content-Type: application/json" <?php echo $apiBaseUrlPassExample; ?>/json/recipe/34
</pre>
	</section>

	</div>
</div>
</section>	

<!-- Create recipe -->
<section id="api-recipe">

<div class="panel panel-default">
	<div class="panel-title">
		<code>recipe</code>
	</div>
	
	<div class="panel-body">
	
		<section id="api-putrecipe">
		<span class="label label-default">PUT</span><br><br>
  
		Allows users to create a recipe.
		
		<br><br>
		
		Example of PHP cURL request:<br><br>
<pre>

$jsonData = 
'{  
   "title":"test recipe",
   "user":"myagi",
   "serves":"7",
   "prep_time":"00:01:00",
   "cook_time":"05:00:00",
   "ingredients":[  
      {  
         "sort_order":"1",
         "amount":"2",
         "unit":"cups",
         "ingredient":"sugar"
      },
      {  
         "sort_order":"2",
         "amount":"5",
         "unit":"TBS",
         "ingredient":"salt"
      }
   ],
   "steps":[  
      {  
         "sort_order":"1",
         "description":"blah 1"
      },
      {  
         "sort_order":"2",
         "description":"step 2 - profit"
      }
   ],
   "tags":[  
      {  
         "keyword":"sugary"
      },
      {  
         "keyword":"salty"
      }
   ]
}';

//Create recipe
$ch = curl_init('<?php echo $apiBaseUrlPassExample; ?>json/recipe');

curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");                                                                     
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);                                                                  
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
	'Content-Type: application/json',                                                                                
	'Content-Length: ' . strlen($jsonData))                                                                       
);   
$result = curl_exec($ch);
</pre>
		</section>

	</div>
	
</div>
	
</section>				
				
<!-- Get all images for a recipe -->
<section id="api-images">
<h3>Recipe Images</h3>
</section>

<div class="panel panel-default">
	<div class="panel-title">
		<code>recipe/{recipe_id}/images</code>
	</div>
	
	<div class="panel-body">
		<table class="table table-condensed">
			<tr>
				<th>parameter</th>
				<th>value</th>
				<th>description</th>
			</tr>
			
			<tr>
				<td>recipe_id</td>
				<td>int</td>
				<td>A recipe's ID</td>
			</tr>
			
		</table>
	
	
		<section id="api-getimages">
		<span class="label label-default">GET</span><br><br>
  
		Get images by recipe ID.
		
		<br><br>
		
		Example of JSON response:<br>
<pre>
{  
   "retval":1,
   "message":"Success",
   "info":[  
      {  
         "id":"46",
         "extension":"JPG",
         "recipe_id":"82"
      },
      {  
         "id":"47",
         "extension":"JPG",
         "recipe_id":"82"
      }
   ]
}
</pre>
		</section>

	</div>
	
</div>
	
</section>				
							
<!-- Create or Delete Images -->
<section id="api-imageid">

<div class="panel panel-default">
	<div class="panel-title">
		<code>recipe/{recipe_id}/image<?php echo ($version === 'v2') ? 's':''; ?></code>
	</div>
	
	<div class="panel-body">
		<table class="table table-condensed">
			<tr>
				<th>parameter</th>
				<th>value</th>
				<th>description</th>
			</tr>
			
			<tr>
				<td>recipe_id</td>
				<td>int</td>
				<td>A recipe's ID</td>
			</tr>
			
		</table>
	
	
		<section id="api-postimage">
		<span class="label label-default">POST</span><br><br>
  
		Allows users to add images for a recipe ID.
		
		<br><br>
		
		Example of PHP cURL request:<br>
<pre>
//Add images
$image_count=0;
foreach($_FILES["file"]["tmp_name"] as $key => $value) {
	if (strlen($value) > 0) {
		$image_data["file_".$key] = '@'.$value;
		$image_count++;
	}
}

if ($image_count > 0) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, '<?php echo $apiBaseUrlPassExample; ?>json/recipe/59/image');
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $image_data);
	$result = curl_exec($ch);
}
</pre>
</pre>

		</section>
		
		<section id="api-deleteimage">
		<span class="label label-default">DELETE</span><br><br>
  
		Allows users to delete an image for a recipe ID.
		
		<br><br>
		
		Example of DELETE request:<br>
<pre>
curl -i -X DELETE <?php echo $apiBaseUrlPassExample; ?>json/recipe/1/image/2/PNG -H "Content-Type: application/json
</pre>

		</section>

	</div>
	
</div>
	
</section>	

<!-- Find tags based on search -->
<section id="api-tags">
<div class="panel panel-default">
	<div class="panel-title">
		<code>tag/{value}</code>
	</div>
	
	<div class="panel-body">
		<table class="table table-condensed">
			<tr>
				<th>parameter</th>
				<th>value</th>
				<th>description</th>
			</tr>
			
			<tr>
				<td>value</td>
				<td>string</td>
				<td>A keyword to search tags</td>
			</tr>
			
		</table>
		
		<section id="api-gettag">
		<span class="label label-default">GET</span><br><br>
  
		Allows users to search tags by keyword. Can be used for autocomplete.
		
		<br><br>
		
		Example of PHP cURL request:<br>
<pre>
curl -i -H "Accept: application/json" -X GET -H "Content-Type: application/json" <?php echo $apiBaseUrlExample; ?>json/<?php echo ($version === 'v2') ? 'recipes/' : '';?>tag/test
</pre>

		</section>

	</div>

</section>

<section id="api-users">
<h3>Users</h3>
</section>

<!-- Create Users -->
<section id="api-user">
<div class="panel panel-default">
	<div class="panel-title">
		<code>user</code>
	</div>
	
	<div class="panel-body">
	
		<section id="api-putuser">
		<span class="label label-default">PUT</span><br><br>
  
		Add a user.
		
		<br><br>
		
		Example of cURL request:<br>
<pre>
curl -i -H "Accept: application/json" -X PUT -H "Content-Type: application/json" <?php echo $apiBaseUrlExample; ?>json/user -d '{"username":"new_username","password":"new_password","email":"new_email"}'
</pre>

		</section>

	</div>
	
</div>
</section>

<!-- Change Email and Password -->
<section id="api-username">
<div class="panel panel-default">
	<div class="panel-title">
		<code>user/{username}</code>
	</div>
	
	<div class="panel-body">
		<table class="table table-condensed">
			<tr>
				<th>parameter</th>
				<th>value</th>
				<th>description</th>
			</tr>
			
			<tr>
				<td>username</td>
				<td>string</td>
				<td>A username</td>
			</tr>
			
		</table>
		
		<section id="api-getusername">
		<span class="label label-default">GET</span><br><br>
  
		Get users id and email.
		
		<br><br>
		
		Example of cURL request:<br>
<pre>
curl -i -H "Accept: application/json" -X GET -H "Content-Type: application/json" <?php echo $apiBaseUrlPassExample; ?>json/user/{username}
</pre>

		</section>
	
		<section id="api-putusername">
		<span class="label label-default">PUT</span><br><br>
  
		Allows users to change their Email and/or Password
		
		<br><br>
		
		Example of cURL request:<br>
<pre>
curl -i -H "Accept: application/json" -X PUT -H "Content-Type: application/json" <?php echo $apiBaseUrlPassExample; ?>json/user/{username} -d '{"password":"updated_password","email":"updated_email"}'
</pre>

		</section>

	</div>
	
</div>
</section>

<!-- Verify User -->
<section id="api-usernameverify">
<div class="panel panel-default">
	<div class="panel-title">
		<code>user/{username}/verify</code>
	</div>
	
	<div class="panel-body">
		<table class="table table-condensed">
			<tr>
				<th>parameter</th>
				<th>value</th>
				<th>description</th>
			</tr>
			
			<tr>
				<td>username</td>
				<td>string</td>
				<td>A username</td>
			</tr>
			
		</table>
	
		<section id="api-putusernameverify">
		<span class="label label-default">PUT</span><br><br>
  
		Verify a users credentials
		
		<br><br>
		
		Example of cURL request:<br>
<pre>
curl -i -H "Accept: application/json" -X PUT -H "Content-Type: application/json" <?php echo $apiBaseUrlExample; ?>json/user/your_username/verify -d '{"password":"aPassword"}'
</pre>

		</section>

	</div>
	
</div>
</section>

<!-- Get all images for a recipe -->
<section id="api-account">
<h3>Accounts</h3>
</section>

<section id="api-recover">
<div class="panel panel-default">
	<div class="panel-title">
		<code>recover</code>
	</div>
	
	<div class="panel-body">
	
		<section id="api-putrecover">
		<span class="label label-default">PUT</span><br><br>
  
		Allows users to recover their password by email
		
		<br><br>
		
		Example of cURL request:<br>
<pre>
curl -i -H "Accept: application/json" -X PUT -H "Content-Type: application/json" <?php echo $apiBaseUrlExample; ?>json/<?php echo ($version === 'v2') ? 'user/' : ''; ?>recover -d '{"email":"anemail@mail.com"}'
</pre>

		</section>

	</div>
	
</div>
</section>

<p class="text-center">
<a rel="license" href="http://creativecommons.org/licenses/by-sa/4.0/"><img alt="Creative Commons License" style="border-width:0" src="https://i.creativecommons.org/l/by-sa/4.0/80x15.png" /></a>
</p>
<br>

</div> <!-- /container -->

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
<script src="https://google-code-prettify.googlecode.com/svn/loader/run_prettify.js"></script>
</body>
</html>
