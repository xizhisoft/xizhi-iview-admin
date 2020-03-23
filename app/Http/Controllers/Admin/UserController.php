<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Admin\Config;
use App\Models\Admin\User;
use DB;
use Spatie\Permission\Models\Role;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Admin\userExport;
use Illuminate\Support\Facades\Cache;

// use Illuminate\Database\Eloquent\Collection;
// use Illuminate\Support\Collection;

class UserController extends Controller
{

	// public function __construct(\Maatwebsite\Excel\Exporter $excel)
	// {
		// $this->excel = $excel;
	// }


    /**
     * 列出用户页面
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function userIndex()
    {
		// 获取JSON格式的jwt-auth用户响应
		$me = response()->json(auth()->user());
		
		// 获取JSON格式的jwt-auth用户信息（$me->getContent()），就是$me的data部分
		$user = json_decode($me->getContent(), true);
		// 用户信息：$user['id']、$user['name'] 等

        // 获取配置值
		$config = Config::pluck('cfg_value', 'cfg_name')->toArray();
        // return view('admin.user', $config);
		
		$share = compact('config', 'user');
        return view('admin.user', $share);
    }
	

    /**
     * 列出用户页面 ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function userList(Request $request)
    {
		if (! $request->ajax()) return null;
		
		$url = request()->url();
		$queryParams = request()->query();
		
		$perPage = $queryParams['perPage'] ?? 10000;
		$page = $queryParams['page'] ?? 1;

        // 获取用户信息
		$queryfilter_name = $request->input('queryfilter_name');
		$queryfilter_logintime = $request->input('queryfilter_logintime');
		$queryfilter_email = $request->input('queryfilter_email');
		$queryfilter_loginip = $request->input('queryfilter_loginip');
		$queryfilter_displayname = $request->input('queryfilter_displayname');
		$queryfilter_department = $request->input('queryfilter_department');
		$queryfilter_disableduser = $request->input('queryfilter_disableduser');

		$user = User::select('id', 'name', 'ldapname', 'email', 'displayname', 'department', 'login_time', 'login_ttl', 'login_ip', 'login_counts', 'created_at', 'updated_at', 'deleted_at')
			->when($queryfilter_logintime, function ($query) use ($queryfilter_logintime) {
				return $query->whereBetween('login_time', $queryfilter_logintime);
			})
			->when($queryfilter_name, function ($query) use ($queryfilter_name) {
				return $query->where('name', 'like', '%'.$queryfilter_name.'%');
			})
			->when($queryfilter_email, function ($query) use ($queryfilter_email) {
				return $query->where('email', 'like', '%'.$queryfilter_email.'%');
			})
			->when($queryfilter_loginip, function ($query) use ($queryfilter_loginip) {
				return $query->where('login_ip', 'like', '%'.$queryfilter_loginip.'%');
			})
			->when($queryfilter_displayname, function ($query) use ($queryfilter_displayname) {
				return $query->where('displayname', 'like', '%'.$queryfilter_displayname.'%');
			})
			->when($queryfilter_department, function ($query) use ($queryfilter_department) {
				return $query->where('department', 'like', '%'.$queryfilter_department.'%');
			})
			->when($queryfilter_disableduser, function ($query) use ($queryfilter_disableduser) {
				// return $query->withTrashed();
				return $query->onlyTrashed();
			})
			->limit(1000)
			->orderBy('created_at', 'desc')
			// ->withTrashed()
			->paginate($perPage, ['*'], 'page', $page);

		return $user;
    }

    /**
     * 创建用户 ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function userCreate(Request $request)
    {
        //
		if (! $request->isMethod('post') || ! $request->ajax()) return false;

		// $newuser = $request->only('name', 'email');
		// $nowtime = date("Y-m-d H:i:s",time());
		$name = $request->input('name');
		// $ldapname = $request->input('ldapname');
		$email = $request->input('email');
		$displayname = $request->input('displayname');
		$department = $request->input('department');
		$password = $request->input('password');
		
		$logintime = date("Y-m-d H:i:s", 86400);
		
		try	{
			$result = User::create([
				'name'     		=> $name,
				// 'ldapname'     	=> $ldapname,
				'email'    		=> $email,
				'displayname'	=> $displayname,
				'department'	=> $department,
				'password' 		=> bcrypt($password),
				'login_time' 	=> $logintime,
				'login_ip' 		=> '255.255.255.255',
				'login_counts' 	=> 0,
				'remember_token'=> '',
				// 'created_at' => $nowtime,
				// 'updated_at' => $nowtime,
				// 'deleted_at' => NULL
			]);
		}
		catch (\Exception $e) {
			// echo 'Message: ' .$e->getMessage();
			$result = 0;
		}

		return $result;
    }

    /**
     * 禁用用户（软删除） ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function userTrash(Request $request)
    {
        //
		if (! $request->isMethod('post') || ! $request->ajax())  return false;

		$userid = $request->input('userid');

		// 如果是管理员id为1，则不能删除
		if ($userid == 1) return false;
		
		$usertrashed = User::select('deleted_at')
			->where('id', $userid)
			->first();

		// 如果在回收站里，则恢复它
		if ($usertrashed == null) {
			$result = User::where('id', $userid)->restore();
		} else {
			$result = User::where('id', $userid)->delete();
		}

		return $result;
    }

    /**
     * 删除用户 ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function userDelete(Request $request)
    {
        //
		if (! $request->isMethod('post') || ! $request->ajax()) return false;
		
		$userid = $request->input('tableselect');
		
		// 判断两个表（model_has_permissions和model_has_roles）中，
		// 是否已有用户被分配了角色或权限
		// 如果已经分配了，则不允许删除
		$model_has_permissions = DB::table('model_has_permissions')
			->whereIn('model_id', $userid)
			->first();
		// dd($model_has_permissions);

		$model_has_roles = DB::table('model_has_roles')
			->whereIn('model_id', $userid)
			->first();
		// dd($model_has_roles);
		
		if ($model_has_permissions != null || $model_has_roles != null) {
			return 0;
		}

		try	{
			$result = User::whereIn('id', $userid)->forceDelete();
			$result = 1;
		}
		catch (\Exception $e) {
			// echo 'Message: ' .$e->getMessage();
			$result = 0;
		}
		
		// Cache::flush();
		return $result;
		
    }

    /**
     * 编辑用户 ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function userUpdate(Request $request)
    {
		if (! $request->isMethod('post') || ! $request->ajax()) return null;

		$id = $request->input('id');
		$name = $request->input('name');
		// $ldapname = $request->input('ldapname');
		$email = $request->input('email');
		$displayname = $request->input('displayname');
		$department = $request->input('department');
		$password = $request->input('password');
		// $created_at = $request->input('created_at');
		// $updated_at = $request->input('updated_at');

		try	{
			// 如果password为空，则不更新密码
			if (isset($password)) {
				$result = User::where('id', $id)
					->update([
						'name'			=>	$name,
						// 'ldapname'		=>	$ldapname,
						'email'			=>	$email,
						'displayname'	=>	$displayname,
						'department'	=>	$department,
						'password'		=>	bcrypt($password)
					]);
			} else {
				$result = User::where('id', $id)
					->update([
						'name'			=>	$name,
						// 'ldapname'		=>	$ldapname,
						'email'			=>	$email,
						'displayname'	=>	$displayname,
						'department'	=>	$department
					]);
			}
		}
		catch (Exception $e) {//捕获异常
			// echo 'Message: ' .$e->getMessage();
			$result = 0;
		}
		
		return $result;
    }


	// 用户列表Excel文件导出
    public function exportUser()
    {
		
		// if (! $request->ajax()) { return null; }
		
		// 获取扩展名配置值
		$config = Config::select('cfg_name', 'cfg_value')
			->pluck('cfg_value', 'cfg_name')->toArray();

		$EXPORTS_EXTENSION_TYPE = $config['EXPORTS_EXTENSION_TYPE'];
		$FILTERS_USER_NAME = $config['FILTERS_USER_NAME'];
		$FILTERS_USER_EMAIL = $config['FILTERS_USER_EMAIL'];
		$FILTERS_USER_LOGINTIME = $config['FILTERS_USER_LOGINTIME'];
		$FILTERS_USER_LOGINIP = $config['FILTERS_USER_LOGINIP'];
		$FILTERS_USER_DEPARTMENT = $config['FILTERS_USER_DEPARTMENT'];
		$FILTERS_USER_DISABLEDUSER = $config['FILTERS_USER_DISABLEDUSER'];

        // 获取用户信息
		// Excel数据，最好转换成数组，以便传递过去
		$queryfilter_name = $FILTERS_USER_NAME ?: '';
		$queryfilter_email = $FILTERS_USER_EMAIL ?: '';
		$queryfilter_logintime = $FILTERS_USER_LOGINTIME ?: ['1970-01-01', '9999-12-31'];
		$queryfilter_loginip = $FILTERS_USER_LOGINIP ?: '';
		$queryfilter_department = $FILTERS_USER_DEPARTMENT ?: '';
		$queryfilter_disableduser = $FILTERS_USER_DISABLEDUSER ?: '';
		
		$user = User::select('id', 'name', 'ldapname', 'email', 'displayname', 'department', 'login_time', 'login_ip', 'login_counts', 'created_at', 'updated_at', 'deleted_at')
			->when($queryfilter_logintime, function ($query) use ($queryfilter_logintime) {
				return $query->whereBetween('login_time', $queryfilter_logintime);
			})
			->when($queryfilter_name, function ($query) use ($queryfilter_name) {
				return $query->where('name', 'like', '%'.$queryfilter_name.'%');
			})
			->when($queryfilter_email, function ($query) use ($queryfilter_email) {
				return $query->where('email', 'like', '%'.$queryfilter_email.'%');
			})
			->when($queryfilter_loginip, function ($query) use ($queryfilter_loginip) {
				return $query->where('login_ip', 'like', '%'.$queryfilter_loginip.'%');
			})
			->when($queryfilter_department, function ($query) use ($queryfilter_department) {
				return $query->where('department', 'like', '%'.$queryfilter_department.'%');
			})
			->when($queryfilter_disableduser, function ($query) use ($queryfilter_disableduser) {
				return $query->onlyTrashed();
			})
			->limit(5000)
			->orderBy('created_at', 'asc')
			// ->withTrashed()
			->get()->toArray();		

        // 示例数据，不能直接使用，只能把数组变成Exports类导出后才有数据
		// $cellData = [
            // ['学号','姓名','成绩'],
            // ['10001','AAAAA','199'],
            // ['10002','BBBBB','192'],
            // ['10003','CCCCC','195'],
            // ['10004','DDDDD','189'],
            // ['10005','EEEEE','196'],
        // ];

		// Excel标题第一行，可修改为任意名字，包括中文
		$title[] = ['id', 'name', 'ldapname', 'email', 'displayname', 'department', 'login_time', 'login_ip', 'login_counts', 'created_at', 'updated_at', 'deleted_at'];

		// 合并Excel的标题和数据为一个整体
		$data = array_merge($title, $user);

		// dd(Excel::download($user, '学生成绩', 'Xlsx'));
		// dd(Excel::download($user, '学生成绩.xlsx'));
		return Excel::download(new userExport($data), 'users'.date('YmdHis',time()).'.'.$EXPORTS_EXTENSION_TYPE);
		
    }


	// 导出用户所属角色
    public function exportRoleOfUser()
    {
		
		// if (! $request->ajax()) { return null; }

		// 获取扩展名配置值
		$config = Config::select('cfg_name', 'cfg_value')
			->pluck('cfg_value', 'cfg_name')->toArray();
		$EXPORTS_EXTENSION_TYPE = $config['EXPORTS_EXTENSION_TYPE'];
		
		// 重置角色和权限的缓存
		app()['cache']->forget('spatie.permission.cache');
		
		// 获取当前用户拥有的角色
		$userhasrole = DB::table('users')
			->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
			->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
			// ->where('users.id', $userid)
			// ->pluck('roles.name', 'roles.id')->toArray();
			->select('users.id', 'users.name', 'users.ldapname', 'users.email', 'users.displayname', 'users.department', 'users.login_time', 'users.login_ip', 'users.login_counts', 'users.created_at', 'users.updated_at', 'users.deleted_at', 'roles.id as roleid', 'roles.name as rolename')
			->get()->toArray();
		// dd($userhasrole);


		// Excel标题第一行，可修改为任意名字，包括中文
		$title[] = ['id', 'name', 'ldapname', 'email', 'displayname', 'department', 'login_time', 'login_ip', 'login_counts', 'created_at', 'updated_at', 'deleted_at', 'roleid', 'rolename'];

		// 合并Excel的标题和数据为一个整体
		$data = array_merge($title, $userhasrole);

		// dd(Excel::download($user, '学生成绩', 'Xlsx'));
		// dd(Excel::download($user, '学生成绩.xlsx'));
		return Excel::download(new userExport($data), 'roleofusers'.date('YmdHis',time()).'.'.$EXPORTS_EXTENSION_TYPE);
		
    }


    /**
     * 清除用户TTL
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function userClsttl(Request $request)
    {
		if (! $request->isMethod('post') || ! $request->ajax()) return null;

		$id = $request->input('id');

		try	{
			$result = User::where('id', $id)
				->update([
					'login_ttl'	=>	0,
				]);
			// $result = 1;
		}
		catch (Exception $e) {
			// echo 'Message: ' .$e->getMessage();
			$result = 0;
		}
		// dd($result);
		return $result;
	}
	
	
    /**
     * 权限角色到指定用户
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function syncRoleToUser(Request $request)
    {
		if (! $request->isMethod('post') || ! $request->ajax()) return null;
		
		$userid = $request->input('userid');
		$roleid = $request->input('roleid');

		// 重置角色和权限的缓存
		app()['cache']->forget('spatie.permission.cache');

		$role = Role::where('id', $roleid)->pluck('name')->toArray();

		$result = 1;
		foreach ($userid as $v) {
			$user = User::where('id', $v)->first();
			$res = $user->assignRole($role);
			if (! $res) {
				$result = 0;
				break;
			}
		}

		Cache::flush();
		return $result;
    }


}
