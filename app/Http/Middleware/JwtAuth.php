<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Admin\Config;
use Cookie;

class JwtAuth
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{

	// 请求前处理内容
	// return $next($request);
	
	// $config = Config::where('cfg_name', 'SITE_KEY')->pluck('cfg_value', 'cfg_name')->toArray();
	$config = Config::pluck('cfg_value', 'cfg_name')->toArray();

	// 判断日期
	$dateofcurrent = date("Y-m-d H:i:s",time());
	$dateofsetup = date(base64_decode(substr($config['SITE_EXPIRED_DATE'], 1)));
// dd($dateofsetup);
	if(!isDatetime($dateofsetup) || strtotime($dateofcurrent) > strtotime($dateofsetup)){
		echo '系统框架和组件已过期，请尽快联络厂商！<br>The framework and components exceed the time limit now, Please contact the manufacturer!';
		die();
	}
	
	// 验证sitekey和appkey
	$site_key = $config['SITE_KEY'];
	$app_key = substr(config('app.key'), 19, 12);
	if ($app_key != $site_key) die();
	
	// 获取JSON格式的jwt-auth用户响应
	$me = response()->json(auth()->user());
	
	// 获取JSON格式的jwt-auth用户信息（$me->getContent()），就是$me的data部分
	$user = json_decode($me->getContent(), true);
	// 用户信息：$user['id']、$user['name'] 等

	// 判断数组为空，以此来判断是否有有效用户登录
	if (! sizeof($user)) {
		// 无有效用户登录，则认证失败，退回登录界面
		// dd('credentials are invalid');

		if($request->ajax()){
			// 如果是ajax请求，则返回空数组，由axios处理返回登录页面
			// return response()->json();
			// return response()->json(['name' => 'Abigail']);
			return response()->json(['jwt' => 'logout']);
		} else {
			// 如果是正常请求，则直接返回登录页面
			// return redirect()->route('login');

			$isMobile = $this->isMobile();

			if ($isMobile) {
				return redirect()->route('logincube');
			} else {
				return redirect()->route('login');
			}

		}
	} else {
		$token_local = Cookie::get('singletoken');
		// $singletoken = md5($user['login_ip'] . $user['name'] . $user[login_time]);
		$token_remote = $user['remember_token'];

		if (empty($token_remote) || $token_local != $token_remote) {
			Cookie::queue(Cookie::forget('token'));
			Cookie::queue(Cookie::forget('singletoken'));
			return $request->ajax() ? response()->json(['jwt' => 'logout']) : redirect()->route('login');
		}

	}


	// 保存请求内容
	$response = $next($request);


	// 请求后处理内容


	// 返回请求
	return $response;
	}
		

	// 2019/4/4追加
	// 判断是否是移动端访问
	public function isMobile()
	{
			// 如果有HTTP_X_WAP_PROFILE则一定是移动设备
			if (isset ($_SERVER['HTTP_X_WAP_PROFILE'])) {
					return TRUE;
			}
			// 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
			if (isset ($_SERVER['HTTP_VIA'])) {
					return stristr($_SERVER['HTTP_VIA'], "wap") ? TRUE : FALSE;// 找不到为flase,否则为TRUE
			}
			// 判断手机发送的客户端标志,兼容性有待提高
			if (isset ($_SERVER['HTTP_USER_AGENT'])) {
					$clientkeywords = array(
							'mobile',
							'nokia',
							'sony',
							'ericsson',
							'mot',
							'samsung',
							'htc',
							'sgh',
							'lg',
							'sharp',
							'sie-',
							'philips',
							'panasonic',
							'alcatel',
							'lenovo',
							'iphone',
							'ipod',
							'blackberry',
							'meizu',
							'android',
							'netfront',
							'symbian',
							'ucweb',
							'windowsce',
							'palm',
							'operamini',
							'operamobi',
							'openwave',
							'nexusone',
							'cldc',
							'midp',
							'wap'
					);
					// 从HTTP_USER_AGENT中查找手机浏览器的关键字
					if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
							return TRUE;
					}
			}
			if (isset ($_SERVER['HTTP_ACCEPT'])) { // 协议法，因为有可能不准确，放到最后判断
					// 如果只支持wml并且不支持html那一定是移动设备
					// 如果支持wml和html但是wml在html之前则是移动设备
					if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== FALSE) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === FALSE || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
							return TRUE;
					}
			}
			return FALSE;
	}

}
