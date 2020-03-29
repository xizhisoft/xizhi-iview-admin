<?php

namespace App\Http\Controllers\Login;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Admin\Config;
use App\Models\Admin\User;
use Cookie;
use Validator;
use Adldap\Laravel\Facades\Adldap;

class LoginController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		$me = response()->json(auth()->user());
		$user = json_decode($me->getContent(), true);

		if (! sizeof($user)) {
			// 无有效用户登录，则认证失败，退回登录界面
			// return redirect()->route('login');
		} else {
			// 如果是已经登录，则跳转至门户页面
			return redirect()->route('portal');
		}

		$config = Config::pluck('cfg_value', 'cfg_name')->toArray();
		return view('login.login', $config);
	}

	public function checklogin(Request $request)
	{
		if (! $request->isMethod('post') || ! $request->ajax()) return null;

		// 1.判断验证码
		$rules = ['captcha' => 'required|captcha'];
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()) return null;

		$name = $request->input('name');
		$password = $request->input('password');
		$rememberme = $request->input('rememberme');

		$nowtime = date("Y-m-d H:i:s",time());
		$ip = $request->getClientIp();
		$singletoken = md5($ip . $name . $nowtime);
		$minutes = $rememberme ? config('jwt.ttl', 60 * 24 * 365) : config('jwt.jwt_cookies_ttl', 60 * 24);

		// 2.adldap判断AD认证
		$adldap = false;
		if (config('ldap.ldap_use_ldap') == 'ldap') {

			try {
				$adldap = Adldap::auth()->attempt(
					// $user['name'] . env('ADLDAP_ADMIN_ACCOUNT_SUFFIX'),
					$name,
					$password
				);
			}
			// catch (Exception $e) {
			catch (\Adldap\Auth\BindException $e) { //捕获异常
				// echo 'Message: ' .$e->getMessage();
				$adldap = false;
			}

			// 3.如果adldap认证成功，则同步本地用户的密码
			//   否则认证失败再由jwt-auth本地判断
			if ($adldap) {
				
				// 获取用户email
				$user_tmp = Adldap::search()->users()->find($name);
				$email = $user_tmp['mail'][0];
				$displayname = $user_tmp['displayname'][0];
				$ldapname = $name;

				// 同步本地用户密码
				try	{
					$result = User::where('name', $name)
						->increment('login_counts', 1, [
							'password'   => bcrypt($password),
							'ldapname'   => $ldapname,
							'email'      => $email,
							'displayname'=> $displayname,
							'login_time' => $nowtime,
							'login_ttl'	 => $minutes,
							'login_ip'   => $ip, // $_SERVER['REMOTE_ADDR'],
							'remember_token'=> $singletoken,
						]);

					// 4.如果没有这个用户，则自动新增用户
					if ($result == 0) {
						$result = User::create([
							'name'          => $name,
							'ldapname'   	=> $ldapname,
							'email'         => $email,
							'displayname'   => $displayname,
							'password'      => bcrypt($password),
							'login_time'    => $nowtime,
							'login_ttl'	 	=> $minutes,
							'login_ip'      => $ip, // $_SERVER['REMOTE_ADDR'],
							'login_counts'  => 1,
							'remember_token'=> $singletoken,
							'created_at'    => $nowtime,
							'updated_at'    => $nowtime,
							'deleted_at'    => NULL
						]);
					}
				}
				catch (Exception $e) {
					// echo 'Message: ' .$e->getMessage();
					// $result = $e->getMessage();
					$result = null;
				}

			} else {
				// 注意：adldap认证失败再由jwt-auth本地判断，不返回失败
				// return null;
			}
		}

		// 3.jwt-auth，判断用户认证
		$credentials = ['name' => $name, 'password' => $password];

		$token = auth()->attempt($credentials);
		if (! $token) {
			// 如果认证失败，则返回null
			// return response()->json(['error' => 'Unauthorized'], 401);
			return null;
		}
		
		// 如果没有经过ldap, 则更新本地用户信息
		if (! $adldap) {
			try	{
				$result = User::where('name', $name)
					->increment('login_counts', 1, [
						'login_time' => $nowtime,
						'login_ttl' => $minutes,
						'login_ip'   => $ip, // $_SERVER['REMOTE_ADDR'],
						'remember_token'   => $singletoken,
					]);
			}
			catch (Exception $e) {//捕获异常
				$result = null;
			}
		}

		Cookie::queue('token', $token, $minutes);
		Cookie::queue('singletoken', $singletoken, $minutes);
		return 1;
	
	}


}
