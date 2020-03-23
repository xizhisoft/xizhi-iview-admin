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
use App\Exports\Admin\roleExport;
use Illuminate\Support\Facades\Cache;

class RoleController extends Controller
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
     * 列出role页面
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function roleIndex()
    {
		// 获取JSON格式的jwt-auth用户响应
		$me = response()->json(auth()->user());
		
		// 获取JSON格式的jwt-auth用户信息（$me->getContent()），就是$me的data部分
		$user = json_decode($me->getContent(), true);
		// 用户信息：$user['id']、$user['name'] 等
		
        // 获取配置值
		$config = Config::pluck('cfg_value', 'cfg_name')->toArray();
        // return view('admin.role', $config);
		
		$share = compact('config', 'user');
// dd($user);
// dd($config['SITE_TITLE']);
        return view('admin.role', $share);
			// ->with('user', $user);
			// ->with('user',$user);
    }

    /**
     * 列出用户 ajax
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

    /**
     * 列出所有待删除的角色 ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function roleListDelete(Request $request)
    {
		if (! $request->ajax()) { return null; }

		// 重置角色和权限的缓存
		app()['cache']->forget('spatie.permission.cache');

		// 1.查出全部role的id
		// $role = Role::select('id')->get()->toArray();
		// $role_tmp = array_column($role, 'id'); //变成一维数组
		$role_tmp = Role::select('id')->pluck('id')->toArray();

		// 2.查出model_has_roles表中的role_id
		$model_has_roles = DB::table('model_has_roles')
			->select('role_id as id')->get()->toArray();
		$model_has_roles_tmp = array_column($model_has_roles, 'id');

		// 3.查出role_has_permissions表中的role_id
		$role_has_permissions = DB::table('role_has_permissions')
			->select('role_id as id')->get()->toArray();
		$role_has_permissions_tmp = array_column($role_has_permissions, 'id');

		// 4.合并前删除重复，model_has_roles和role_has_permissions两个表的结果
		$role_used = array_merge($model_has_roles_tmp, $role_has_permissions_tmp);
		$role_used_tmp = array_unique($role_used);

		// 5.排除已被使用的role，剩余的既是没被使用的role的id
		$unused_role_id = array_diff($role_tmp, $role_used_tmp);
		
		// 6.查询没被使用的role
		$result = Role::whereIn('id', $unused_role_id)
			->pluck('name', 'id')->toArray();

		return $result;
    }

	
    /**
     * 列出用户拥有roles ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function userHasRole(Request $request)
    {
		if (! $request->ajax()) return null;

		$userid = $request->input('userid');
		
		// 重置角色和权限的缓存
		app()['cache']->forget('spatie.permission.cache');
		
		// 获取当前用户拥有的角色
		$userhasrole = DB::table('users')
			->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
			->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
			->where('users.id', $userid)
			// ->pluck('roles.name', 'roles.id')->toArray();
			->select('roles.id')
			->get()->toArray();
		$userhasrole = array_column($userhasrole, 'id'); //变成一维数组

		// $tmp_array = DB::table('roles')
			// ->select('id', 'name')
			// ->whereNotIn('id', array_keys($userhasrole))
			// ->get()->toArray();
			// $usernothasrole = array_column($tmp_array, 'name', 'id'); //变成一维数组
		// $usernothasrole = DB::table('roles')
			// ->select('id', 'name')
			// ->whereNotIn('id', array_keys($userhasrole))
			// ->pluck('name', 'id')->toArray();
		$allroles = DB::table('roles')
			->pluck('name', 'id')->toArray();

		$displayname = DB::table('users')
			->where('id', $userid)
			->value('displayname');

		// $result['userhasrole'] = $userhasrole;
		// $result['allroles'] = $allroles;
		$result = compact('userhasrole', 'allroles', 'displayname');

		return $result;
    }

    /**
     * 创建role
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function roleCreate(Request $request)
    {
		if (! $request->isMethod('post') || ! $request->ajax()) return null;
        $name = $request->input('name');
		// 重置角色和权限的缓存
		app()['cache']->forget('spatie.permission.cache');
		$role = Role::create(['name' => $name]);
		Cache::flush();
        return $role;
    }

    /**
     * 删除角色 ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function roleDelete(Request $request)
    {
		if (! $request->isMethod('post') || ! $request->ajax()) return false;

		$roleid = $request->input('tableselect');

		// 重置角色和权限的缓存
		app()['cache']->forget('spatie.permission.cache');
		
		// 判断是否在已被使用之列
		// 1.查出model_has_roles表中的role_id
		$model_has_roles = DB::table('model_has_roles')
			->select('role_id as id')->get()->toArray();
		$model_has_roles_tmp = array_column($model_has_roles, 'id');

		// 2.查出role_has_permissions表中的role_id
		$role_has_permissions = DB::table('role_has_permissions')
			->select('role_id as id')->get()->toArray();
		$role_has_permissions_tmp = array_column($role_has_permissions, 'id');

		// 3.合并前删除重复，model_has_roles和role_has_permissions两个表的结果
		$role_used = array_merge($model_has_roles_tmp, $role_has_permissions_tmp);
		$role_used_tmp = array_unique($role_used);

		// 4.判断是否在列
		// $flag = false;
		// foreach ($roleid as $value) {
			// if (in_array($value, $role_used_tmp)) {
				// $flag = true;
				// break;
			// }
		// }
		$flag = array_intersect($roleid, $role_used_tmp);

		// 如果在使用之列，则不允许删除
		if ($flag) return false;
		
        // 如没被使用，则可以删除
		$result = Role::whereIn('id', $roleid)->delete();
		Cache::flush();
		return $result;
    }

    /**
     * 更新当前用户的角色
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function userUpdateRole(Request $request)
    {
		if (! $request->isMethod('post') || ! $request->ajax()) return null;
		
        $userid = $request->input('userid');
        $roleid = $request->input('roleid');
		// dd($roleid);

		// 重置角色和权限的缓存
		app()['cache']->forget('spatie.permission.cache');

		$user = User::where('id', $userid)->first();
		$role = Role::whereIn('id', $roleid)->pluck('name')->toArray();
		$roleall = Role::pluck('name')->toArray();
		
		// 注意：removeRole似乎不接受数组
		foreach ($roleall as $rolename) {
			$result = $user->removeRole($rolename);
		}
		
		$result = $user->assignRole($role);
		Cache::flush();
        return $result;
    }

    /**
     * 用户赋予role
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function roleGive(Request $request)
    // {
		// if (! $request->isMethod('post') || ! $request->ajax()) return null;
		
        // $userid = $request->input('params.userid');
        // $roleid = $request->input('params.roleid');

		// 重置角色和权限的缓存
		// app()['cache']->forget('spatie.permission.cache');

		// $user = User::where('id', $userid)->first();
		// $role = Role::whereIn('id', $roleid)->pluck('name')->toArray();
		
		// $result = $user->assignRole($role);
        // return $result;
    // }

    /**
     * 用户移除role
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function roleRemove(Request $request)
    // {
		// if (! $request->isMethod('post') || ! $request->ajax()) return null;
		
        // $userid = $request->input('params.userid');
        // $roleid = $request->input('params.roleid');

		// 重置角色和权限的缓存
		// app()['cache']->forget('spatie.permission.cache');

		// $user = User::where('id', $userid)->first();
		// $role = Role::whereIn('id', $roleid)->pluck('name')->toArray();

		// 注意：removeRole似乎不接受数组
		// foreach ($role as $rolename) {
			// $result = $user->removeRole($rolename);
		// }

        // return $result;
    // }

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
			
			Cache::put($fullUrl, $result, now()->addSeconds(60));
		}

		return $result;
    }

    /**
     * 列出所有权限 ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function permissionList(Request $request)
    // {
		// if (! $request->ajax()) { return null; }
		// $permission = Permission::pluck('name', 'id')->toArray();
		// return $permission;
    // }

    /**
     * 根据角色查看哪些用户 ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function roleToViewUser(Request $request)
    {
		if (! $request->ajax()) return null;
		
		$roleid = $request->input('roleid');

		$user = DB::table('model_has_roles')
			->join('users', 'model_has_roles.model_id', '=', 'users.id')
			->where('role_id', $roleid)
			->pluck('users.name', 'users.id')->toArray();

		return $user;
    }

    /**
     * 权限同步到指定角色 ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function syncPermissionToRole(Request $request)
    {
		if (! $request->isMethod('post') || ! $request->ajax()) return null;
		
		$roleid = $request->input('roleid');
		$permissionid[] = $request->input('permissionid');

		// 重置角色和权限的缓存
		app()['cache']->forget('spatie.permission.cache');

		// 1.查询role
		$role = Role::where('id', $roleid)->first();

		// 2.查询permission
		$permissions = Permission::whereIn('id', $permissionid)
			->pluck('name')->toArray();

		$result = $role->givePermissionTo($permissions);
		Cache::flush();
		return $result;
    }

    /**
     * 角色列表 ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function roleGets(Request $request)
    {
		if (! $request->ajax()) return null;
		// 重置角色和权限的缓存
		app()['cache']->forget('spatie.permission.cache');
		
		$url = request()->url();
		$queryParams = request()->query();
		
		$perPage = $queryParams['perPage'] ?? 10000;
		$page = $queryParams['page'] ?? 1;
		
		$queryfilter_name = $request->input('queryfilter_name');
		// $queryfilter_logintime = $request->input('queryfilter_logintime');
		// $queryfilter_email = $request->input('queryfilter_email');
		// $queryfilter_loginip = $request->input('queryfilter_loginip');

		//对查询参数按照键名排序
		ksort($queryParams);

		//将查询数组转换为查询字符串
		$queryString = http_build_query($queryParams);

		$fullUrl = sha1("{$url}?{$queryString}");
		

		//首先查寻cache如果找到
		if (Cache::has($fullUrl)) {
			$result = Cache::get($fullUrl);    //直接读取cache
		} else {                                   //如果cache里面没有
			$result = Role::select('id', 'name', 'guard_name', 'created_at', 'updated_at')
				// ->when($queryfilter_logintime, function ($query) use ($queryfilter_logintime) {
					// return $query->whereBetween('login_time', $queryfilter_logintime);
				// })
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
     * 编辑角色 ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function roleUpdate(Request $request)
    {
		if (! $request->isMethod('post') || ! $request->ajax()) return null;

		$id = $request->input('id');
		$name = $request->input('name');
		// $email = $request->input('email');
		// $password = $request->input('password');
		// $created_at = $request->input('created_at');
		// $updated_at = $request->input('updated_at');

		try	{
			$result = Role::where('id', $id)
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
		
		$role = Role::select('id', 'name', 'guard_name', 'created_at', 'updated_at')
			->limit(5000)
			->orderBy('created_at', 'asc')
			->get()->toArray();		

		// Excel标题第一行，可修改为任意名字，包括中文
		$title[] = ['id', 'name', 'guard_name', 'created_at', 'updated_at'];

		// 合并Excel的标题和数据为一个整体
		$data = array_merge($title, $role);

		return Excel::download(new roleExport($data), 'roles'.date('YmdHis',time()).'.'.$EXPORTS_EXTENSION_TYPE);
    }	

}
