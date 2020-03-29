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

	// logout
	Route::get('logout', 'mainController@logout')->name('main.logout');
});


// login模块
Route::group(['prefix' => 'login', 'namespace' =>'Login'], function() {
	Route::get('/', 'LoginController@index')->name('login');
	Route::post('checklogin', 'LoginController@checklogin')->name('login.checklogin');
});

// AdminController路由
Route::group(['prefix'=>'admin', 'namespace'=>'Admin', 'middleware'=>['jwtauth','permission:permission_super_admin']], function() {

	// 显示system页面
	Route::get('systemIndex', 'AdminController@systemIndex')->name('admin.system.index');
	
	// 获取config数据信息
	Route::get('systemList', 'AdminController@systemList')->name('admin.system.list');

	// 显示config页面
	Route::get('configIndex', 'AdminController@configIndex')->name('admin.config.index');

	// 获取config数据信息
	Route::get('configList', 'AdminController@configList')->name('admin.config.list');

	// 获取group数据信息
	Route::get('groupList', 'AdminController@groupList')->name('admin.group.list');
	
	// 修改config数据
	Route::post('configChange', 'AdminController@configChange')->name('admin.config.change');

	// logout
	Route::get('logout', 'AdminController@logout')->name('admin.logout');

});