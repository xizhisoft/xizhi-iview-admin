<?php

namespace App\Http\Controllers\Main;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Admin\Config;
use App\Models\Admin\User;
use Cookie;
use DB;
use App\Models\Main\Smt_config;
use App\Models\Main\Release;

class mainController extends Controller
{
	// logout
	public function logout()
	{
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

		// 删除cookie
		Cookie::queue(Cookie::forget('singletoken'));
		Cookie::queue(Cookie::forget('token'));

		// 返回登录页面
		// return redirect()->route('login');

		$isMobile = $this->isMobile();

		if ($isMobile) {
			return redirect()->route('logincube');
		} else {
			return redirect()->route('login');
		}

	}
	
	
    //
	public function mainPortal () {
		
		// 获取JSON格式的jwt-auth用户响应
		$me = response()->json(auth()->user());
		
		// 获取JSON格式的jwt-auth用户信息（$me->getContent()），就是$me的data部分
		$user = json_decode($me->getContent(), true);
		// 用户信息：$user['id']、$user['name'] 等

        // 获取配置值
		$config = Config::pluck('cfg_value', 'cfg_name')->toArray();
        // return view('admin.config', $config);
		
		$share = compact('config', 'user');
        return view('main.portal', $share);
		
	}
	
    // cube
	public function mainPortalcube () {
		
		// 获取JSON格式的jwt-auth用户响应
		$me = response()->json(auth()->user());
		
		// 获取JSON格式的jwt-auth用户信息（$me->getContent()），就是$me的data部分
		$user = json_decode($me->getContent(), true);
		// 用户信息：$user['id']、$user['name'] 等

        // 获取配置值
		$config = Config::pluck('cfg_value', 'cfg_name')->toArray();
        // return view('admin.config', $config);
		
		$share = compact('config', 'user');
        return view('main.portalcube', $share);
		
	}

	/**
     * 获取登录用户信息 cube
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function portalcubeUser()
    {
	// 获取JSON格式的jwt-auth用户响应
	$me = response()->json(auth()->user());

	// 获取JSON格式的jwt-auth用户信息（$me->getContent()），就是$me的data部分
	$user = json_decode($me->getContent(), true);
	// 用户信息：$user['id']、$user['name'] 等

	// 获取配置值
	// $config = Config::pluck('cfg_value', 'cfg_name')->toArray();
	
	// $share = compact('config', 'user');
	// return view('renshi.jiaban_cube_applicant', $share);
	return $user;
	}


	public function mainConfig () {

		// 获取JSON格式的jwt-auth用户响应
		$me = response()->json(auth()->user());
		
		// 获取JSON格式的jwt-auth用户信息（$me->getContent()），就是$me的data部分
		$user = json_decode($me->getContent(), true);
		// 用户信息：$user['id']、$user['name'] 等

        // 获取配置值
		$config = Config::pluck('cfg_value', 'cfg_name')->toArray();
        // return view('admin.config', $config);
		
		$share = compact('config', 'user');
        return view('main.config', $share);
		
	}

	public function mainRelease () {

		// 获取JSON格式的jwt-auth用户响应
		$me = response()->json(auth()->user());
		
		// 获取JSON格式的jwt-auth用户信息（$me->getContent()），就是$me的data部分
		$user = json_decode($me->getContent(), true);
		// 用户信息：$user['id']、$user['name'] 等

        // 获取配置值
		$config = Config::pluck('cfg_value', 'cfg_name')->toArray();
        // return view('admin.config', $config);
		
		$share = compact('config', 'user');
        return view('main.release', $share);
		
	}
	
	public function configGets (Request $request) {

		if (! $request->ajax()) return null;

		// $configgets = Smt_config::pluck('value', 'name');
		$configgets = Smt_config::select('title', 'name', 'value')->get();
			
		return $configgets;
	}

	public function configCreate (Request $request) {

		if (! $request->ajax()) return null;
		
		$position = $request->input('position');
		$name = $request->input('name');
		$value = $request->input('value');
		// dd($value);
		
		try	{
			DB::beginTransaction();
			
			$data_old = Smt_config::select('value')
				->where('name', $name)
				->first();
			// dd($data_old['value']);
			
			$arr = explode('---', $data_old['value']);
			array_splice($arr, $position, 0, $value);
			// dd($arr);
			$data_new = implode('---', $arr);
			// dd($data_new);
			
			 Smt_config::where('name', $name)
			 ->update(['value' => $data_new]);

			$result = 1;
		}
		catch (\Exception $e) {
			// echo 'Message: ' .$e->getMessage();
			DB::rollBack();
			// return 'Message: ' .$e->getMessage();
			return 0;
		}

		DB::commit();
		return $result;				
		
	}

	public function configUpdate (Request $request) {

		if (! $request->ajax()) return null;
		
		$name = $request->input('name');
		$value = $request->input('value');
		// dd($value);
		
		try	{
			DB::beginTransaction();
			
			 Smt_config::where('name', $name)
				 ->update(['value' => $value]);

			$result = 1;
		}
		catch (\Exception $e) {
			// echo 'Message: ' .$e->getMessage();
			DB::rollBack();
			// return 'Message: ' .$e->getMessage();
			return 0;
		}

		DB::commit();
		return $result;				
		
	}


	public function mainReleasegets (Request $request) {

		if (! $request->ajax()) return null;

		$offset = $request->input('offset');

		$releasegets = Release::select('title', 'content')
			->offset($offset)
			->limit(10)
			->orderBy('id', 'desc')
			->get()
			->toArray();
		
		return $releasegets;
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
