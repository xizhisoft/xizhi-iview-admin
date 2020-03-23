<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Admin\Config;
use App\Models\Admin\User;
use DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Admin\permissionExport;
use Illuminate\Support\Facades\Cache;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

	
    /**
     * 列出permission页面
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function permissionIndex()
    {
		// 获取JSON格式的jwt-auth用户响应
		$me = response()->json(auth()->user());

		// 获取JSON格式的jwt-auth用户信息（$me->getContent()），就是$me的data部分
		$user = json_decode($me->getContent(), true);
		// 用户信息：$user['id']、$user['name'] 等

        // 获取配置值
		$config = Config::pluck('cfg_value', 'cfg_name')->toArray();
        // return view('admin.permission', $config);
		
		$share = compact('config', 'user');
        return view('admin.permission', $share);
    }

    /**
     * 权限列表 ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function permissionGets(Request $request)
    {
		if (! $request->ajax()) return null;

		// 重置角色和权限的缓存
		app()['cache']->forget('spatie.permission.cache');

		$url = request()->url();
		$queryParams = request()->query();
		
		$perPage = $queryParams['perPage'] ?? 10000;
		$page = $queryParams['page'] ?? 1;
		
		$queryfilter_name = $request->input('queryfilter_name');

		//对查询参数按照键名排序
		ksort($queryParams);

		//将查询数组转换为查询字符串
		$queryString = http_build_query($queryParams);

		$fullUrl = sha1("{$url}?{$queryString}");
		
		
		//首先查寻cache如果找到
		if (Cache::has($fullUrl)) {
			$result = Cache::get($fullUrl);    //直接读取cache
		} else {                                   //如果cache里面没有
			$result = Permission::select('id', 'name', 'guard_name', 'created_at', 'updated_at')
				->when($queryfilter_name, function ($query) use ($queryfilter_name) {
					return $query->where('name', 'like', '%'.$queryfilter_name.'%');
				})
				->limit(1000)
				->orderBy('created_at', 'desc')
				->paginate($perPage, ['*'], 'page', $page);

			Cache::put($fullUrl, $result, now()->addSeconds(60));
		}
		
		return $result;
    }
	
    /**
     * 创建permission ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function permissionCreate(Request $request)
    {
		if (! $request->isMethod('post') || ! $request->ajax()) return null;
        $permissionname = $request->input('name');

		// 重置角色和权限的缓存
		app()['cache']->forget('spatie.permission.cache');
		$permission = Permission::create(['name' => $permissionname]);
		Cache::flush();
        return $permission;
    }

    /**
     * 删除permission ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function permissionDelete(Request $request)
    {
		if (! $request->isMethod('post') || ! $request->ajax()) return null;

		$permissionid = $request->input('tableselect');

		// 重置角色和权限的缓存
		app()['cache']->forget('spatie.permission.cache');
		
		// 判断是否在已被使用之列
		// 1.查出model_has_permissions表中的permission_id
		$model_has_permissions = DB::table('model_has_permissions')
			->select('permission_id as id')->pluck('id')->toArray();
		// $model_has_permissions_tmp = array_column($model_has_roles, 'id');

		// 2.查出role_has_permissions表中的permission_id
		$role_has_permissions = DB::table('role_has_permissions')
			->select('permission_id as id')->pluck('id')->toArray();
		$role_has_permissions_tmp = array_column($role_has_permissions, 'id');

		// 3.合并前删除重复，model_has_permissions和role_has_permissions两个表的结果
		$permission_used = array_merge($model_has_permissions, $role_has_permissions_tmp);
		$permission_used_tmp = array_unique($permission_used);

		// 4.判断是否在列
		// $flag = false;
		// foreach ($permissionid as $value) {
			// if (in_array($value, $permission_used_tmp)) {
				// $flag = true;
				// break;
			// }
		// }
		$flag = array_intersect($permissionid, $permission_used_tmp);
		// dd($flag);
		// 如果在使用之列，则不允许删除
		if ($flag) return false;
		
        // 如没被使用，则可以删除
		$result = Permission::whereIn('id', $permissionid)->delete();
		Cache::flush();
		return $result;
    }
	
	
    /**
     * 更新当前角色的权限
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function roleUpdatePermission(Request $request)
    {
		if (! $request->isMethod('post') || ! $request->ajax()) return null;
		
        $roleid = $request->input('roleid');
        $permissionid = $request->input('permissionid');

		// 重置角色和权限的缓存
		app()['cache']->forget('spatie.permission.cache');
		
		// 1.查询role
		$role = Role::where('id', $roleid)->first();

		// 2.查询permission
		$permissions = Permission::whereIn('id', $permissionid)
			->pluck('name')->toArray();

		$result = $role->syncPermissions($permissions);

		// $role = Role::where('id', $roleid)->first();
		// $permission = Permission::whereIn('id', $permissionid)->pluck('name')->toArray();
		// $permissionall = Permission::pluck('name')->toArray();

		// 注意：revokePermissionTo似乎不接受数组
		// foreach ($permissionall as $permissionname) {
			// $result = $role->revokePermissionTo($permissionname);
		// }

		// foreach ($permission as $permissionname) {
			// $result = $role->givePermissionTo($permissionname);
		// }
		Cache::flush();
        return $result;
    }	

    /**
     * 角色赋予permission
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function permissionGive(Request $request)
    // {
		// if (! $request->isMethod('post') || ! $request->ajax()) { return null; }
		
        // $roleid = $request->input('params.roleid');
        // $permissionid = $request->input('params.permissionid');

		// 重置角色和权限的缓存
		// app()['cache']->forget('spatie.permission.cache');

		// $role = Role::where('id', $roleid)->first();
		// $permission = Permission::whereIn('id', $permissionid)->pluck('name')->toArray();
		
		// foreach ($permission as $permissionname) {
			// $result = $role->givePermissionTo($permissionname);
		// }
		
        // return $result;
    // }

    /**
     * 角色移除permission
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function permissionRemove(Request $request)
    // {
		// if (! $request->isMethod('post') || ! $request->ajax()) { return null; }
		
        // $roleid = $request->input('params.roleid');
        // $permissionid = $request->input('params.permissionid');

		// 重置角色和权限的缓存
		// app()['cache']->forget('spatie.permission.cache');

		// $role = Role::where('id', $roleid)->first();
		// $permission = Permission::whereIn('id', $permissionid)->pluck('name')->toArray();

		// 注意：revokePermissionTo似乎不接受数组
		// foreach ($permission as $permissionname) {
			// $result = $role->revokePermissionTo($permissionname);
		// }

        // return $result;
    // }

    /**
     * 列出角色拥有permissions ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function roleHasPermission(Request $request)
    {
		if (! $request->ajax()) return null;

		$roleid = $request->input('roleid');

		// 重置角色和权限的缓存
		app()['cache']->forget('spatie.permission.cache');
		
		// 获取当前角色拥有的权限
		// $rolehaspermission = DB::table('users')
			// ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
			// ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
			// ->where('users.id', $roleid)
			// ->pluck('roles.name', 'roles.id')->toArray();
		$rolehaspermission = Role::join('role_has_permissions', 'roles.id', '=', 'role_has_permissions.role_id')
			->join('permissions', 'role_has_permissions.permission_id', '=', 'permissions.id')
			->where('roles.id', $roleid)
			// ->pluck('permissions.name', 'permissions.id')->toArray();
			->select('permissions.id')
			->get()->toArray();
		$rolehaspermission = array_column($rolehaspermission, 'id'); //变成一维数组

		// $rolenothaspermission = Permission::select('id', 'name')
			// ->whereNotIn('id', array_keys($rolehaspermission))
			// ->pluck('name', 'id')->toArray();
		
		$allpermissions = Permission::pluck('name', 'id')->toArray();

		// $result['rolehaspermission'] = $rolehaspermission;
		// $result['rolenothaspermission'] = $rolenothaspermission;
		$result = compact('rolehaspermission', 'allpermissions');

		return $result;
    }

    /**
     * 列出所有待删除的权限 ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function permissionListDelete(Request $request)
    {
		if (! $request->ajax()) { return null; }
		
		// 重置角色和权限的缓存
		app()['cache']->forget('spatie.permission.cache');

		// 1.查出全部permission的id
		// $role = Role::select('id')->get()->toArray();
		// $role_tmp = array_column($role, 'id'); //变成一维数组
		$permission_tmp = Permission::select('id')->pluck('id')->toArray();

		// 2.查出model_has_roles表中的role_id
		$model_has_permissions = DB::table('model_has_permissions')
			->select('permission_id as id')->pluck('id')->toArray();
		// $model_has_roles_tmp = array_column($model_has_permissions, 'id');

		// 3.查出role_has_permissions表中的role_id
		$role_has_permissions = DB::table('role_has_permissions')
			->select('permission_id as id')->pluck('id')->toArray();
		// $role_has_permissions_tmp = array_column($role_has_permissions, 'id');

		// 4.合并前删除重复，model_has_roles和role_has_permissions两个表的结果
		$permission_used = array_merge($model_has_permissions, $role_has_permissions);
		$permission_used_tmp = array_unique($permission_used);

		// 5.排除已被使用的role，剩余的既是没被使用的role的id
		$unused_permission_id = array_diff($permission_tmp, $permission_used_tmp);
		
		// 6.查询没被使用的role
		$result = Permission::whereIn('id', $unused_permission_id)
			->pluck('name', 'id')->toArray();

		return $result;
    }

    /**
     * 列出所有权限 ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function permissionList(Request $request)
    {
		if (! $request->ajax()) return null;
		// 重置角色和权限的缓存
		app()['cache']->forget('spatie.permission.cache');
		
		$url = request()->url();
		$queryParams = request()->query();

		$queryfilter_name = $request->input('queryfilter_name');

		//对查询参数按照键名排序
		ksort($queryParams);

		//将查询数组转换为查询字符串
		$queryString = http_build_query($queryParams);

		$fullUrl = sha1("{$url}?{$queryString}");
		

		//首先查寻cache如果找到
		if (Cache::has($fullUrl)) {
			$result = Cache::get($fullUrl);    //直接读取cache
		} else {                                   //如果cache里面没有
			$result = Permission::when($queryfilter_name, function ($query) use ($queryfilter_name) {
					return $query->where('name', 'like', '%'.$queryfilter_name.'%');
				})
				->limit(10)
				->orderBy('created_at', 'desc')
				->pluck('name', 'id')->toArray();

			Cache::put($fullUrl, $result, now()->addSeconds(60));
		}
		
		return $result;
    }

    /**
     * 根据权限查看哪些角色 ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function permissionToViewRole(Request $request)
    {
		if (! $request->ajax()) return null;
		
		$permissionid = $request->input('permissionid');

		$role = Role::join('role_has_permissions', 'roles.id', '=', 'role_has_permissions.role_id')
			->where('role_has_permissions.permission_id', $permissionid)
			->pluck('roles.name', 'roles.id')->toArray();

		return $role;
    }

    /**
     * 测试用户是否有权限
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function testUsersPermission(Request $request)
    {
		if (! $request->isMethod('post') || ! $request->ajax()) return null;
		
		$userid = $request->input('userid');
		$permissionid[] = $request->input('permissionid');

		// 重置角色和权限的缓存
		app()['cache']->forget('spatie.permission.cache');

		// 1.查询User
		$user = User::where('id', $userid)->first();
		
		// 2.查询Permission
		$permissions = Permission::whereIn('id', $permissionid)
			->pluck('name')->toArray();

		// 3.测试用户是否有权限
		$result = $user->hasAnyPermission($permissions);

		return $result ? 1 : 0;
    }

	
    /**
     * 编辑权限 ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function permissionUpdate(Request $request)
    {
		if (! $request->isMethod('post') || ! $request->ajax()) return null;

		$id = $request->input('id');
		$name = $request->input('name');

		try	{
			$result = Permission::where('id', $id)
				->update([
					'name'	=>	$name,
				]);
		}
		catch (Exception $e) {//捕获异常
			// echo 'Message: ' .$e->getMessage();
			$result = 0;
		}
		Cache::flush();
		return $result;
    }
	
	
	
	// 角色列表Excel文件导出
    public function excelExport()
    {
		// if (! $request->ajax()) { return null; }
		
		// 获取扩展名配置值
		$config = Config::select('cfg_name', 'cfg_value')
			->pluck('cfg_value', 'cfg_name')->toArray();

		$EXPORTS_EXTENSION_TYPE = $config['EXPORTS_EXTENSION_TYPE'];

        // 获取用户信息
		// Excel数据，最好转换成数组，以便传递过去
		
		$permission = Permission::select('id', 'name', 'guard_name', 'created_at', 'updated_at')
			->limit(5000)
			->orderBy('created_at', 'asc')
			->get()->toArray();		

		// Excel标题第一行，可修改为任意名字，包括中文
		$title[] = ['id', 'name', 'guard_name', 'created_at', 'updated_at'];

		// 合并Excel的标题和数据为一个整体
		$data = array_merge($title, $permission);

		return Excel::download(new permissionExport($data), 'permissions'.date('YmdHis',time()).'.'.$EXPORTS_EXTENSION_TYPE);
    }
	
    /**
     * 列出所有角色，用于查看哪些用户正在使用
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function roleList(Request $request)
    {
		if (! $request->ajax()) return null;

		// 重置角色和权限的缓存
		app()['cache']->forget('spatie.permission.cache');
		
		$url = request()->url();
		$queryParams = request()->query();
		
		$queryfilter_name = $request->input('queryfilter_name');

		//对查询参数按照键名排序
		ksort($queryParams);

		//将查询数组转换为查询字符串
		$queryString = http_build_query($queryParams);

		$fullUrl = sha1("{$url}?{$queryString}");
		
		
		//首先查寻cache如果找到
		if (Cache::has($fullUrl)) {
			$result = Cache::get($fullUrl);    //直接读取cache
		} else {                                   //如果cache里面没有
			$result = Role::when($queryfilter_name, function ($query) use ($queryfilter_name) {
					return $query->where('name', 'like', '%'.$queryfilter_name.'%');
				})
				->limit(10)
				->orderBy('created_at', 'desc')
				->pluck('name', 'id')->toArray();

			Cache::put($fullUrl, $result, now()->addSeconds(30));
		}

		return $result;
    }
	
	
    /**
     * 列出所有用户，用于测试是否有权限
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function userList(Request $request)
    {
		if (! $request->ajax()) return null;
		
		// 重置角色和权限的缓存
		app()['cache']->forget('spatie.permission.cache');

		$url = request()->url();
		$queryParams = request()->query();

		$queryfilter_name = $request->input('queryfilter_name');

		//对查询参数按照键名排序
		ksort($queryParams);

		//将查询数组转换为查询字符串
		$queryString = http_build_query($queryParams);

		$fullUrl = sha1("{$url}?{$queryString}");
		

		//首先查寻cache如果找到
		if (Cache::has($fullUrl)) {
			$result = Cache::get($fullUrl);    //直接读取cache
		} else {                                   //如果cache里面没有
			$result = User::when($queryfilter_name, function ($query) use ($queryfilter_name) {
					return $query->where('name', 'like', '%'.$queryfilter_name.'%');
				})
				->limit(10)
				->orderBy('created_at', 'desc')
				->pluck('name', 'id')->toArray();

			Cache::put($fullUrl, $result, now()->addSeconds(60));
		}

		return $result;
    }	
	
}
