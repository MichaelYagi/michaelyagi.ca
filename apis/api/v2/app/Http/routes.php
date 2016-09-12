<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

config(['result.empty' => Array(Array("retval" => 0,"message" => "No results"))]);
config(['result.unauthorized' => Array(Array("retval" => 0,"message" => "Unauthorized"))]);
config(['result.incorrect' => Array(Array("retval" => 0,"message" => "Incorrect"))]);
config(['result.incomplete' => Array(Array("retval" => 0,"message" => "Incomplete"))]);
config(['result.fail' => Array(Array("retval" => 0,"message" => "Fail"))]);
config(['result.success' => Array(Array("retval" => 1,"message" => "Success"))]);
config(['dir.image' => '"/var/www/michaelyagi.ca/public/media/recipeimages/"']);

$BASE_API_PATH='/api/v2/json';

$app->get($BASE_API_PATH.'/info', function() use ($app) {
    return response()->json([
		'framework' => $app->version(),
	]);
});

// Recipes
$app->get($BASE_API_PATH.'/recipes', 'RecipeController@showAll');
$app->get($BASE_API_PATH.'/recipe/{recipeid}', 'RecipeController@show');
$app->get($BASE_API_PATH.'/recipesByType/{type}/{value}', 'RecipeController@showByType');
$app->get($BASE_API_PATH.'/recipes/tag/{value}', 'RecipeController@showByTag');
$app->put($BASE_API_PATH.'/recipe', ['middleware' => 'BasicAuth', 'uses' => 'RecipeController@add']);
$app->put($BASE_API_PATH.'/recipe/{recipeid}', ['middleware' => 'BasicAuth', 'uses' => 'RecipeController@update']);
$app->delete($BASE_API_PATH.'/recipe/{recipeid}', ['middleware' => 'BasicAuth', 'uses' => 'RecipeController@remove']);

// Recipe Images
$app->get($BASE_API_PATH.'/recipe/{recipeid}/images', 'RecipeController@showImages');
$app->post($BASE_API_PATH.'/recipe/{recipeid}/images', ['middleware' => 'BasicAuth', 'uses' => 'RecipeController@addImages']);
$app->post($BASE_API_PATH.'/recipe/{recipeid}/image/{imageid}/{imagext}', ['middleware' => 'BasicAuth', 'uses' => 'RecipeController@removeImage']);

// Users
$app->put($BASE_API_PATH.'/user/recover', 'UserController@recover');
$app->get($BASE_API_PATH.'/user/{username}', ['middleware' => 'BasicAuth', 'uses' => 'UserController@showUserInfoByUsername']);
$app->put($BASE_API_PATH.'/user', 'UserController@add');
$app->put($BASE_API_PATH.'/user/{username}', ['middleware' => 'BasicAuth', 'uses' => 'UserController@update']);
$app->put($BASE_API_PATH.'/user/{username}/verify', 'UserController@verify');
