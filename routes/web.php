<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Auth::routes();

Route::get('/', 'HomeController@index');
Route::group(['middleware'=>['auth']],function(){
	Route::group(['prefix'=>'admin'],function(){
		Route::get('users/{id}/restore',['as'=>'adminusers.restore', 'uses'=>'AdminUserController@restore']);	
		Route::resource('users','AdminUserController',['names'=>'adminusers']);
		Route::get('clients/{id}/restore',['as'=>'adminclients.restore', 'uses'=>'AdminClientController@restore']);	
		Route::resource('clients', 'AdminClientController',['names'=>'adminclients']);
		Route::get('spreadsheets/{id}/import',['as'=>'adminspreadsheetimport', 'uses'=>'AdminSpreadsheetController@import']);	
		Route::post('spreadsheets/{id}/import',['as'=>'adminspreadsheetimport', 'uses'=>'AdminSpreadsheetController@importupload']);	
		Route::get('spreadsheets/{id}/duplicate',['as'=>'adminspreadsheetduplicate', 'uses'=>'AdminSpreadsheetController@duplicate']);	
		Route::resource('spreadsheets', 'AdminSpreadsheetController',['names'=>'adminspreadsheets']);	
	});
	Route::group(['prefix'=>'client'],function(){
		Route::get('spreadsheets/{id}/export',['as'=>'clientspreadsheetexport', 'uses'=>'ClientSpreadsheetController@export']);	
		Route::resource('spreadsheets','ClientSpreadsheetController',['names'=>'clientspreadsheets']);	
	});
	Route::get('reports/{id}/duplicate',['as'=>'reports.duplicate', 'uses'=>'ReportsController@duplicate']);	
	Route::get('reports/generate/{id}',['as'=>'reports.generate', 'uses'=>'ReportsController@generate']);	
	Route::resource('reports', 'ReportsController');	
	Route::resource('settings', 'SettingsController');	
});