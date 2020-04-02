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
	// Route::get('groupList', 'AdminController@groupList')->name('admin.group.list');
	
	// 修改config数据
	Route::post('configChange', 'AdminController@configChange')->name('admin.config.change');

	// logout
	Route::get('logout', 'AdminController@logout')->name('admin.logout');

});



// UserController路由
Route::group(['prefix'=>'user', 'namespace'=>'Admin', 'middleware'=>['jwtauth','permission:permission_admin_user|permission_super_admin']], function() {

	// 显示user页面
	Route::get('userIndex', 'UserController@userIndex')->name('admin.user.index');

	// 获取user数据信息
	// Route::get('userList', 'UserController@userList')->name('admin.user.list');

	// 创建user
	// Route::post('userCreate', 'UserController@userCreate')->name('admin.user.create');

	// 禁用user（软删除）
	// Route::post('userTrash', 'UserController@userTrash')->name('admin.user.trash');

	// 删除user
	// Route::post('userDelete', 'UserController@userDelete')->name('admin.user.delete');

	// 编辑user
	// Route::post('userUpdate', 'UserController@userUpdate')->name('admin.user.update');

	// 导出用户列表
	// Route::get('exportUser', 'UserController@exportUser')->name('admin.user.exportuser');

	// 导出用户所属角色
	// Route::get('exportroleofuser', 'UserController@exportRoleOfUser')->name('admin.user.exportroleofuser');

	// 清除user的ttl
	// Route::post('userclsttl', 'UserController@userClsttl')->name('admin.user.clsttl');

	// 角色同步到指定用户
	// Route::post('syncRoleToUser', 'UserController@syncRoleToUser')->name('admin.user.syncroletouser');

});


// RoleController路由
Route::group(['prefix'=>'role', 'namespace'=>'Admin', 'middleware'=>['jwtauth','permission:permission_admin_role|permission_super_admin']], function() {

	// 显示role页面
	Route::get('roleIndex', 'RoleController@roleIndex')->name('admin.role.index');

	// 列出所有用户
	// Route::get('userList', 'RoleController@userList')->name('admin.role.userlist');

	// 列出所有角色
	// Route::get('roleList', 'RoleController@roleList')->name('admin.role.rolelist');

	// 列出所有权限
	// Route::get('permissionList', 'RoleController@permissionList')->name('admin.role.permissionlist');

	// 列出所有待删除的角色
	// Route::get('roleListDelete', 'RoleController@roleListDelete')->name('admin.role.rolelistdelete');

	// 创建role
	// Route::post('roleCreate', 'RoleController@roleCreate')->name('admin.role.create');

	// 编辑role
	// Route::post('roleUpdate', 'RoleController@roleUpdate')->name('admin.role.update');
	
	// 删除角色
	// Route::post('roleDelete', 'RoleController@roleDelete')->name('admin.role.roledelete');

	// 列出当前用户拥有的角色
	// Route::get('userHasRole', 'RoleController@userHasRole')->name('admin.role.userhasrole');

	// 更新当前用户的角色
	// Route::post('userUpdateRole', 'RoleController@userUpdateRole')->name('admin.role.userupdaterole');

	// 列出当前用户可追加的角色
	// Route::get('userGiveRole', 'RoleController@userGiveRole')->name('admin.role.usergiverole');

	// 赋予role
	// Route::post('roleGive', 'RoleController@roleGive')->name('admin.role.give');
	// 移除role
	// Route::post('roleRemove', 'RoleController@roleRemove')->name('admin.role.remove');

	// 根据角色查看哪些用户
	// Route::get('roleToViewUser', 'RoleController@roleToViewUser')->name('admin.role.roletoviewuser');

	// 权限同步到指定角色
	// Route::post('syncPermissionToRole', 'RoleController@syncPermissionToRole')->name('admin.role.syncpermissiontorole');

	// 查询角色列表
	// Route::get('roleGets', 'RoleController@roleGets')->name('admin.role.rolegets');
	
	// 测试excelExport
	// Route::get('excelExport', 'RoleController@excelExport')->name('admin.role.excelexport');
	
});


// PermissionController路由
Route::group(['prefix'=>'permission', 'namespace'=>'Admin', 'middleware'=>['jwtauth','permission:permission_admin_permission|permission_super_admin']], function() {

	// 显示permission页面
	Route::get('permissionIndex', 'PermissionController@permissionIndex')->name('admin.permission.index');

	// 角色列表
	// Route::get('permissionGets', 'PermissionController@permissionGets')->name('admin.permission.permissiongets');

	// 创建permission
	// Route::post('permissionCreate', 'PermissionController@permissionCreate')->name('admin.permission.create');

	// 编辑permission
	// Route::post('permissionUpdate', 'PermissionController@permissionUpdate')->name('admin.permission.update');
	
	// 删除permission
	// Route::post('permissionDelete', 'PermissionController@permissionDelete')->name('admin.permission.permissiondelete');

	// 赋予permission
	// Route::post('permissionGive', 'PermissionController@permissionGive')->name('admin.permission.give');
	// 移除permission
	// Route::post('permissionRemove', 'PermissionController@permissionRemove')->name('admin.permission.remove');

	// 列出当前角色拥有的权限
	// Route::get('roleHasPermission', 'PermissionController@roleHasPermission')->name('admin.permission.rolehaspermission');

	// 更新当前角色的权限
	// Route::post('roleUpdatePermission', 'PermissionController@roleUpdatePermission')->name('admin.permission.roleupdatepermission');
	
	// 列出所有待删除的权限
	// Route::get('permissionListDelete', 'PermissionController@permissionListDelete')->name('admin.permission.permissionlistdelete');

	// 列出所有权限
	// Route::get('permissionList', 'PermissionController@permissionList')->name('admin.permission.permissionlist');

	// 根据权限查看哪些角色
	// Route::get('permissionToViewRole', 'PermissionController@permissionToViewRole')->name('admin.permission.permissiontoviewrole');

	// 角色同步到指定权限
	// Route::post('testUsersPermission', 'PermissionController@testUsersPermission')->name('admin.permission.testuserspermission');
	
	// 测试excelExport
	// Route::get('excelExport', 'PermissionController@excelExport')->name('admin.permission.excelexport');

	// 列出所有角色
	// Route::get('roleList', 'PermissionController@roleList')->name('admin.permission.rolelist');

	// 列出所有用户
	// Route::get('userList', 'PermissionController@userList')->name('admin.permission.userlist');
	
});