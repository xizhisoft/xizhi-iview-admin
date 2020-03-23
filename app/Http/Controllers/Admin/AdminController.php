<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Admin\Config;
use App\Models\Admin\User;
use Cookie;
use DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AdminController extends Controller
{
	// logout
	public function logout()
	{
		// 删除cookie
		Cookie::queue(Cookie::forget('token'));

		// 重置login_ttl为0
		$me = response()->json(auth()->user());
		$user = json_decode($me->getContent(), true);

		try	{
			User::where('id', $user['id'])
			->update([
				'login_ttl'	=> 0
			]);
		}
		catch (Exception $e) {
			// echo 'Message: ' .$e->getMessage();
			// $result = 0;
		}

		// Pass true to force the token to be blacklisted "forever"
		// auth()->logout(true);
		auth()->logout();

		// 返回登录页面
		return redirect()->route('login');
	}
	
	
    /**
     * 列出配置页面
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function configIndex()
    {
		// 获取JSON格式的jwt-auth用户响应
		$me = response()->json(auth()->user());
		
		// 获取JSON格式的jwt-auth用户信息（$me->getContent()），就是$me的data部分
		$user = json_decode($me->getContent(), true);
		// 用户信息：$user['id']、$user['name'] 等

        // 获取配置值
		$config = Config::pluck('cfg_value', 'cfg_name')->toArray();
        // return view('admin.config', $config);
		
		$share = compact('config', 'user');
        return view('admin.config', $share);
    }

    /**
     * 列出配置页面 ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function configList(Request $request)
    {
		if (! $request->ajax()) { return null; }
		
        // 获取用户信息
		// $perPage = $request->input('perPage');
		// $page = $request->input('page');
		// if (null == $page) $page = 1;

		$config = Config::select('cfg_id', 'cfg_name', 'cfg_value', 'cfg_description')
			->get();
		
		foreach ($config as $key=>$value) {
			if ($value['cfg_name']=='SITE_KEY') {
				unset($config[$key]);
			}
		}
			
		return $config;
    }

    /**
     * 修改配置 ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function configChange(Request $request)
    {
		if (! $request->isMethod('post') || ! $request->ajax()) { return false; }

		// $up2data = $request->all();
		$up2data = $request->only('cfg_data');
		// dd($up2data['cfg_data']);
		foreach ($up2data['cfg_data'] as $key => $value) {
			// dd($key . '|' . $value);
			// $result = Config::where('cfg_name', $up2data['cfg_name'])->update(['cfg_value'=>$up2data['cfg_value']]);
			$result = Config::where('cfg_name', $key)->update(['cfg_value'=>$value]);
		}
		return $result;
    }


	
    /**
     * 修改用户密码（自助）
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function passwordChange(Request $request)
    {
		if (! $request->isMethod('post') || ! $request->ajax()) return null;

		$password_old = $request->input('password_old');
		$password_new = $request->input('password_new');
		$password_confirm = $request->input('password_confirm');

		if ($password_new != $password_confirm) return 0;
		if (! isset($password_new)) return 0;
		if (strlen($password_new) < 8) return 0;
		if ($password_old == $password_new) return 0;

		$me = response()->json(auth()->user());
		$user = json_decode($me->getContent(), true);

		if ($user['id'] <= 10) return 0;

		// jwt-auth，判断用户认证
		$credentials = ['name' => $user['name'], 'password' => $password_old];
		$token = auth()->attempt($credentials);
		if (! $token) return 0;
		
		try	{
			$result = User::where('id', $user['id'])
				->update([
					// 'name'			=>	$name,
					// 'ldapname'		=>	$ldapname,
					'password' 		=> bcrypt($password_new),
				]);
			}
		catch (Exception $e) {//捕获异常
			// dd('Message: ' .$e->getMessage());
			$result = 0;
		}

		return $result;
    }

}
