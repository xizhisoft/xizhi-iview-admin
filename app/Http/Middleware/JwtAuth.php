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
	

	// 获取JSON格式的jwt-auth用户响应
	$me = response()->json(auth()->user());
	$user = json_decode($me->getContent(), true);

	// 判断数组为空，以此来判断是否有有效用户登录
	if (! sizeof($user)) {
		return $request->ajax() ? response()->json(['jwt' => 'logout']) : redirect()->route('login');
	} else {
		$token_local = Cookie::get('singletoken');
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
		

}
