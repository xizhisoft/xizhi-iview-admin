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
    public function systemIndex()
    {
		$me = response()->json(auth()->user());
		$user = json_decode($me->getContent(), true);

        // 获取配置值
		$config = Config::pluck('cfg_value', 'cfg_name')->toArray();
		
		$share = compact('config', 'user');
		return view('admin.system', $share);
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
     * 获取系统信息
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function systemList(Request $request)
    {
		if (! $request->ajax()) return null;
		
		// 获取系统信息
		$systeminfo = array(
			'os'=>PHP_OS,
			'operating_environment'=>$_SERVER["SERVER_SOFTWARE"],
			'php_sapi_name'=>php_sapi_name(),
			// 'thinkphp_version'=>THINK_VERSION.' [ <a href="http://thinkphp.cn" target="_blank">查看最新版本</a> ]',
			'upload_max_filesize'=>ini_get('upload_max_filesize'),
			'max_execution_time'=>ini_get('max_execution_time').'秒',
			'server_date'=>date("Y年n月j日 H:i:s"),
			'beijing_time'=>gmdate("Y年n月j日 H:i:s",time()+8*3600),
			'server_name'=>$_SERVER['SERVER_NAME'].' [ '.gethostbyname($_SERVER['SERVER_NAME']).' ]',
			'server_addr'=>$_SERVER["SERVER_ADDR"],
			'http_host'=>$_SERVER['HTTP_HOST'],
			'document_root'=>$_SERVER['DOCUMENT_ROOT'],
			'disk_free_space'=>round((disk_free_space(".")/(1024*1024)),2).'M',
			'register_globals'=>get_cfg_var("register_globals")=='1' ? 'ON' : 'OFF',
			'magic_quotes_gpc'=>(1===get_magic_quotes_gpc()) ? 'YES' : 'NO',
			'magic_quotes_runtime'=>(1===get_magic_quotes_runtime()) ? 'YES' : 'NO',
			'http_user_agent'=>$_SERVER["HTTP_USER_AGENT"],
			// 'boottime'=> exec('uptime'),
		);
	// dd(exec('uptime'));
		return $systeminfo;
		}
		

    /**
     * 获取配置信息
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function configList(Request $request)
    {
		if (! $request->ajax()) return null;
		
        // 获取用户信息
		// $perPage = $request->input('perPage');
		// $page = $request->input('page');
		// if (null == $page) $page = 1;

		$config = Config::select('cfg_id', 'cfg_name', 'cfg_value', 'cfg_description')
			->orderBy('cfg_id', 'asc')
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
		if (! $request->isMethod('post') || ! $request->ajax()) return null;

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
