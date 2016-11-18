<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

// Home
Route::get('/', [
	'uses' => 'HomeController@index', 
	'as' => 'home'
]);
Route::get('/proto', [
	'uses' => 'HomeController@index'
]);
// Informations
Route::get('/informations', [
	'as' => 'informations', 
	'uses' => 'HomeController@informations'
]);
// Charter
Route::get('/charter', [
	'as' => 'charter', 
	function()
{
    return view('front.charter');
}]);
Route::pattern('id', '[0-9]+');

Route::get('/user/{id}', [
	'uses' => 'UserController@show',
	'as' => 'show-user'
]);
Route::get('/sentence/{id}', [
	'uses' => 'SentenceController@show'
]);
Route::get('/user/ask-friend/{id}', [
	'uses' => 'UserController@getAskFriend'
]);
Route::get('/user/accept-friend/{id}', [
	'uses' => 'UserController@getAcceptFriend'
]);

Route::get('/user/cancel-friend/{id}', [
	'uses' => 'UserController@getCancelFriend'
]);

// Auth
Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
	'admin' => 'AdminController',
	'corpus' => 'CorpusController',
	'user' => 'UserController',
	'mini-game' => 'MiniGameController',
	'sentence' => 'SentenceController',
	'negative-annotation' => 'NegativeAnnotationController',
	'tutorial-annotation' => 'TutorialAnnotationController',
	'test' => 'TestController',
	'constant-game' => 'ConstantGameController',
	'trophy' => 'TrophyController',
	'page' => 'PageController',
	'news' => 'NewsController',
	'report' => 'ReportController',
	'annotation-user' => 'AnnotationUserController',
	'duel' => 'DuelController',
	'challenge' => 'ChallengeController',
	'message' => 'MessageController',
	'pos-game' => 'PosGameController',
]);

Route::get('/game', [
	'uses' => 'GameController@index', 
	'as' => 'game'
]);

Route::get('/pos-game', [
	'uses' => 'PosGameController@getIndex', 
	'as' => 'pos-game'
]);

Route::get('user/players', [
	'uses' => 'UserController@getPlayers', 
	'as' => 'players'
]);

Route::get('/game/proto', [
	'uses' => 'GameController@indexGame'
]);

Route::get('/game/demo', [
	'uses' => 'GameController@indexDemo', 
	'as' => 'demo'
]);
Route::get('/asset', [
	'uses' => 'AssetController@get', 
	'as' => 'asset'
]);
Route::get('/asset/conll', [
	'uses' => 'AssetController@getConll', 
	'as' => 'conll'
]);

Route::get('/game/{mode}/jsonContent', [
	'uses' => 'GameController@jsonContent'
]);

Route::get('/game/{mode}/begin/{id}', [
	'uses' => 'GameController@begin'
]);

Route::get('/game/{mode}/answer', [
	'uses' => 'GameController@answer'
]);

Route::get('/game/inventaire', [
	'uses' => 'ObjectController@inventaire'
]);

Route::get('/shop', [
	'uses' => 'ObjectController@index',
	'as' => 'shop',
]);

Route::get('/game/buyObject/{id}', [
	'uses' => 'ObjectController@buyObject'
]);
Route::get('/object/checkHelpAsSeen/{id}', [
	'uses' => 'ObjectController@checkHelpAsSeen'
]);
Route::get('/shop/objectWon', [
	'uses' => 'ObjectController@objectWon'
]);
Route::get('/shop/{mode}/useObject/{id}', [
	'uses' => 'ObjectController@useObject'
]);
Route::get('language', 'HomeController@language');

