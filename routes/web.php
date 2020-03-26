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


// main模块
Route::group(['prefix'=>'', 'namespace'=>'Main', 'middleware'=>['jwtauth']], function() {
	Route::get('/', 'mainController@mainPortal')->name('portal');
	Route::get('portal', 'mainController@mainPortal')->name('portal');
	Route::get('portalcube', 'mainController@mainPortalcube')->name('portalcube');
	Route::get('portalcubeuser', 'mainController@portalcubeUser')->name('portalcubeuser');
	Route::get('configgets', 'mainController@configGets')->name('smt.configgets');

	// logout
	Route::get('logout', 'mainController@logout')->name('main.logout');
});


// login模块
Route::group(['prefix' => 'login', 'namespace' =>'Login'], function() {
	Route::get('/', 'LoginController@index')->name('login');
	Route::post('checklogin', 'LoginController@checklogin')->name('login.checklogin');
});
