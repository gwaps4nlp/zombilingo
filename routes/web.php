<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::get('logout', 'Auth\LoginController@logout')->name('logout');

Route::get('auth/unsubscribe', 'Auth\RegisterController@getUnsubscribe');
Route::post('auth/unsubscribe', 'Auth\RegisterController@postUnsubscribe');


Route::pattern('id', '[0-9]+');

// Home
Route::get('/', 'HomeController@index')->name('home');
// Informations
Route::get('informations', 'HomeController@informations')->name('informations');
// Toggle language french/english
Route::get('language', 'HomeController@language');

// Charter
Route::get('charter', function()
{
    return view('front.charter');
})->name('charter');

Route::get('translation', function()
{
    return view('back.translations.index');
})->middleware('admin');

// UserController
Route::group(array('before' => 'auth'), function ()
{
	Route::get('user/home', 'UserController@getHome');
	Route::get('user/players', 'UserController@getPlayers')->name('players');
	Route::get('user/connected', 'UserController@getConnected');
	Route::get('user/index-admin', 'UserController@getIndexAdmin')->middleware('admin');
    Route::get('user/{user}', 'UserController@show')->name('show-user');
    Route::get('user/ask-friend/{user}', 'UserController@getAskFriend');
    Route::get('user/accept-friend/{user}', 'UserController@getAcceptFriend');
    Route::get('user/cancel-friend/{user}', 'UserController@getCancelFriend');
    Route::post('user/change-email', 'UserController@postChangeEmail');
    Route::post('password/change', 'UserController@postChangePassword');
});
// AdminController
Route::group(array('before' => 'admin'), function ()
{
	Route::get('admin', 'AdminController@getIndex');
	Route::get('admin/reporting', 'AdminController@getReporting');
	Route::get('admin/mwe', 'AdminController@getMwe');
});
// AnnotationUserController
Route::get('annotation-user/index', 'AnnotationUserController@getIndex')->middleware('auth');
Route::get('annotation-user/admin-index', 'AnnotationUserController@getAdminIndex')->middleware('admin');
// AssetController
Route::get('asset/conll', 'AssetController@getConll');

// ChallengeController
Route::get('challenge/number-annotations/{challenge}', 'ChallengeController@getNumberAnnotations')->middleware('auth');
Route::get('challenge/results', 'ChallengeController@getResults')->middleware('auth');
Route::group(array('before' => 'admin'), function ()
{
	Route::get('challenge/index', 'ChallengeController@getIndex');
	Route::get('challenge/create', 'ChallengeController@getCreate');
	Route::post('challenge/create', 'ChallengeController@postCreate');
	Route::get('challenge/edit/{challenge}', 'ChallengeController@getEdit');
	Route::post('challenge/edit/{challenge}', 'ChallengeController@postEdit');
});

// CorpusController
Route::group(array('before' => 'admin'), function ()
{
	Route::get('corpus/show/{corpus}', 'CorpusController@getShow');
	Route::get('corpus/create', 'CorpusController@getCreate');
	Route::post('corpus/create', 'CorpusController@postCreate');
	Route::get('corpus/edit/{corpus}', 'CorpusController@getEdit');
	Route::post('corpus/edit/{corpus}', 'CorpusController@postEdit');
	Route::get('corpus/index', 'CorpusController@getIndex');
	Route::get('corpus/stat-player', 'CorpusController@getStatPlayer');
	Route::get('corpus/delete', 'CorpusController@getDelete');
	Route::post('corpus/delete', 'CorpusController@postDelete');
	Route::get('corpus/import', 'CorpusController@getImport');
	Route::get('corpus/import-from-url', 'CorpusController@getImportFromUrl');
	Route::post('corpus/import-from-url', 'CorpusController@postImportFromUrl');
	Route::post('corpus/sentences-splitter', 'CorpusController@postSentencesSplitter');
	Route::post('corpus/tokeniser', 'CorpusController@postTokeniser');
	Route::post('corpus/pos-tagger', 'CorpusController@postPosTagger');
	Route::post('corpus/parse', 'CorpusController@postParse');
	Route::post('corpus/save-parse', 'CorpusController@postSaveParse');
	Route::get('corpus/post-import', 'CorpusController@getPostImport');
	Route::get('corpus/post-export', 'CorpusController@getPostExport');
	Route::get('corpus/export', 'CorpusController@getExport');
	Route::post('corpus/export', 'CorpusController@postExport');
	Route::get('corpus/export-mwe', 'CorpusController@getExportMwe');
	Route::post('corpus/export-mwe', 'CorpusController@postExportMwe');
	Route::get('corpus/file', 'CorpusController@getFile');
	Route::post('corpus/import', 'CorpusController@postImport');
	Route::get('corpus/compute-stats-by-date', 'CorpusController@getComputeStatsByDate');
	Route::get('corpus/compute-complexity', 'CorpusController@getComputeComplexity');
	Route::get('corpus/compare', 'CorpusController@getCompare');
	Route::get('corpus/compare-conll', 'CorpusController@getCompareConll');
	Route::get('corpus/confidence-by-user', 'CorpusController@getConfidenceByUser');
	Route::get('corpus/evolution-scores', 'CorpusController@getEvolutionScores');
	Route::get('corpus/diff-by-pos', 'CorpusController@getDiffByPos');
	Route::get('corpus/diff-by-relation', 'CorpusController@getDiffByRelation');
	Route::get('corpus/split-annotations', 'CorpusController@getSplitAnnotations');
});
// DiscussionController
Route::group(array('before' => 'auth'), function ()
{
	Route::get('game/history', 'DiscussionController@getHistory')->name('history');
	Route::get('discussion/index/{id}', 'DiscussionController@getIndex');
	Route::get('discussion', 'DiscussionController@getIndex')->name('index-discussion');
	Route::get('discussion/thread', 'DiscussionController@getThread');
	Route::get('discussion/follow-thread', 'DiscussionController@getFollowThread');
	Route::get('discussion/un-follow-thread', 'DiscussionController@getUnFollowThread');
	Route::post('discussion/new', 'DiscussionController@postNew');
});
Route::group(array('before' => 'admin'), function ()
{
	Route::get('discussion/delete', 'DiscussionController@getDelete');
});

// Route::get('constant-game/index', '\Gwaps4nlp\Core\ConstantGameController@getIndex');
// FaqController
// Route::get('faq', 'FaqController@getIndex')->name('faq');
// Route::group(array('before' => 'admin'), function ()
// {
// 	Route::get('faq/admin-index', 'FaqController@getAdminIndex');
// 	Route::post('faq/create-question-answer', 'FaqController@postCreateQuestionAnswer');
// 	Route::post('faq/delete-question-answer', 'FaqController@postDeleteQuestionAnswer');
// 	Route::post('faq/create-section-faq', 'FaqController@postCreateSectionFaq');
// 	Route::post('faq/delete-section-faq', 'FaqController@postDeleteSectionFaq');
// 	Route::post('faq/update-order-sections', 'FaqController@postUpdateOrderSections');
// 	Route::post('faq/update-order-question-answer', 'FaqController@postUpdateOrderQuestionAnswer');

// });
// NegativeAnnotationController
Route::group(array('before' => 'admin'), function ()
{
	Route::get('negative-annotation/index', 'NegativeAnnotationController@getIndex');
	Route::get('negative-annotation/change-visibility', 'NegativeAnnotationController@getChangeVisibility');
	Route::get('negative-annotation/delete', 'NegativeAnnotationController@getDelete');
	Route::get('negative-annotation/delete-by-relation', 'NegativeAnnotationController@getDeleteByRelation');
	Route::get('negative-annotation/import', 'NegativeAnnotationController@getImport');
	Route::post('negative-annotation/import', 'NegativeAnnotationController@postImport');
	Route::post('negative-annotation/delete', 'NegativeAnnotationController@postDelete');
});
// PageController
Route::group(array('before' => 'admin'), function ()
{
	Route::get('page/index', 'PageController@getIndex');
	Route::get('page/edit', 'PageController@getEdit');
	Route::post('page/edit', 'PageController@postEdit');
});
//ReportController
Route::post('report/send', 'ReportController@postSend')->middleware('auth');
//SentenceController
Route::group(array('before' => 'admin'), function ()
{
	Route::get('sentence/index', 'SentenceController@getIndex');
	Route::post('sentence/index', 'SentenceController@postIndex');
	Route::get('sentence/search', 'SentenceController@getSearch');
	Route::get('sentence/graph/{sentence}', 'SentenceController@getGraph');
	Route::get('sentence/{sentence}', 'SentenceController@show');
});

//TutorialAnnotationController
Route::group(array('before' => 'admin'), function ()
{
	Route::get('tutorial-annotation/index', 'TutorialAnnotationController@getIndex');
	Route::get('tutorial-annotation/change-visibility', 'TutorialAnnotationController@getChangeVisibility');
	Route::get('tutorial-annotation/delete-by-relation', 'TutorialAnnotationController@getDeleteByRelation');
	Route::get('tutorial-annotation/import', 'TutorialAnnotationController@getImport');
	Route::post('tutorial-annotation/import', 'TutorialAnnotationController@postImport');
});
//Annotator
Route::get('annotator/graph-corpus/{id}', 'AnnotatorController@getGraphCorpus');
Route::get('annotator/graph/{id}', 'AnnotatorController@getGraph');
Route::get('annotator/correction/{id}', 'AnnotatorController@getCorrection');
Route::post('annotator/save', 'AnnotatorController@postSave');
Route::post('annotator/save-config', 'AnnotatorController@postSaveConfig');


Route::get('/sentence/{id}', 'SentenceController@show');

Route::get('game', 'GameController@index')->name('game');

// Mode Duel
Route::get('duel', 'DuelController@getIndex')->name('duel');
Route::get('duel/modal-new', 'DuelController@getModalNew');
Route::get('duel/compare-results/{id}', 'DuelController@getCompareResults');
Route::get('duel/revenge/{id}', 'DuelController@getRevenge');
Route::post('duel/new', 'DuelController@postNew');
Route::post('duel/join', 'DuelController@postJoin');

Route::get('pos-game', 'PosGameController@getIndex')->name('pos-game');

Route::get('export', function () {
    return redirect('informations#export');
});

Route::get('game/proto', [
	'uses' => 'GameController@indexGame'
]);

Route::get('mini-game/index', function()
{
    return view('front.minigame.index');
})->name('mini-game');

Route::get('mini-game/origin', 'MiniGameController@getOrigin');
Route::get('mini-game/origin-proto', 'MiniGameController@getOriginProto');
Route::get('mini-game/definition', 'MiniGameController@getDefinition');



Route::get('game/demo', 'GameController@indexDemo')->name('demo');

Route::get('game/{mode}/jsonContent', [
	'uses' => 'GameController@jsonContent'
]);

Route::get('game/{mode}/begin/{id}', [
	'uses' => 'GameController@begin'
]);

Route::get('game/{mode}/answer', [
	'uses' => 'GameController@answer'
]);
//ObjectController
Route::get('game/inventaire', 'ObjectController@inventaire');
Route::get('shop', 'ObjectController@index');
Route::get('game/buyObject/{id}', 'ObjectController@buyObject');
Route::get('object/checkHelpAsSeen/{id}', 'ObjectController@checkHelpAsSeen');
Route::get('shop/objectWon', 'ObjectController@objectWon');
Route::get('shop/{mode}/useObject/{id}', 'ObjectController@useObject');

Route::group(array('before' => 'admin'), function ()
{
    Route::get('/laravel-filemanager', '\Unisharp\Laravelfilemanager\controllers\LfmController@show');
    Route::post('/laravel-filemanager/upload', '\Unisharp\Laravelfilemanager\controllers\UploadController@upload');
    // list all lfm routes here...
});

