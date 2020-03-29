<?php

use Illuminate\Database\Seeder;

use App\Models\Admin\Config;

class ConfigsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		$nowtime = date("Y-m-d H:i:s",time());
		
        // DB::table('configs')->delete();
		// Let's clear the users table first
		Config::truncate();
		
		// DB::table('configs')->insert(array (
		Config::insert(array (
            0 => 
            array (
                'cfg_id' => 1,
                'cfg_name' => 'SITE_TITLE',
                'cfg_value' => 'Xyz Managerment System',
                'cfg_description' => '站点名称',
                'created_at' => $nowtime,
                'updated_at' => $nowtime,
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'cfg_id' => 2,
                'cfg_name' => 'SITE_VERSION',
                'cfg_value' => '1902.16.0.2053',
				'cfg_description' => '站点版本号',
                'created_at' => $nowtime,
                'updated_at' => $nowtime,
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'cfg_id' => 3,
                'cfg_name' => 'SITE_COPYRIGHT',
                'cfg_value' => '© 2018-2019 GaoFenghua',
				'cfg_description' => '站点版权信息',
                'created_at' => $nowtime,
                'updated_at' => $nowtime,
                'deleted_at' => NULL,
            ),
            3 => 
            array (
                'cfg_id' => 4,
                'cfg_name' => 'SITE_KEY',
                'cfg_value' => '',
				'cfg_description' => '站点KEY',
                'created_at' => $nowtime,
                'updated_at' => $nowtime,
                'deleted_at' => NULL,
            ),
            4 => 
            array (
                'cfg_id' => 5,
                'cfg_name' => 'PERPAGE_RECORDS_FOR_USER',
                'cfg_value' => '5',
				'cfg_description' => '用户页每页记录数',
                'created_at' => $nowtime,
                'updated_at' => $nowtime,
                'deleted_at' => NULL,
            ),
            5 => 
            array (
                'cfg_id' => 6,
                'cfg_name' => 'PERPAGE_RECORDS_FOR_ROLE',
                'cfg_value' => '5',
				'cfg_description' => '角色页每页记录数',
                'created_at' => $nowtime,
                'updated_at' => $nowtime,
                'deleted_at' => NULL,
            ),
            6 => 
            array (
                'cfg_id' => 7,
                'cfg_name' => 'PERPAGE_RECORDS_FOR_PERMISSION',
                'cfg_value' => '5',
				'cfg_description' => '权限页每页记录数',
                'created_at' => $nowtime,
                'updated_at' => $nowtime,
                'deleted_at' => NULL,
            ),
            7 => 
            array (
                'cfg_id' => 8,
                'cfg_name' => 'EXPORTS_EXTENSION_TYPE',
                'cfg_value' => 'xlsx',
				'cfg_description' => '导出文件扩展名（xlsx、xls、csv）',
                'created_at' => $nowtime,
                'updated_at' => $nowtime,
                'deleted_at' => NULL,
            ),
            8 => 
            array (
                'cfg_id' => 9,
                'cfg_name' => 'FILTERS_USER_NAME',
                'cfg_value' => '',
				'cfg_description' => '过滤器，用户名称',
                'created_at' => $nowtime,
                'updated_at' => $nowtime,
                'deleted_at' => NULL,
            ),
            9 => 
            array (
                'cfg_id' => 10,
                'cfg_name' => 'FILTERS_USER_EMAIL',
                'cfg_value' => '',
				'cfg_description' => '过滤器，邮箱',
                'created_at' => $nowtime,
                'updated_at' => $nowtime,
                'deleted_at' => NULL,
            ),
            10 => 
            array (
                'cfg_id' => 11,
                'cfg_name' => 'FILTERS_USER_LOGINTIME',
                'cfg_value' => '',
				'cfg_description' => '过滤器，用户登录时间区间',
                'created_at' => $nowtime,
                'updated_at' => $nowtime,
                'deleted_at' => NULL,
            ),
            11 => 
            array (
                'cfg_id' => 12,
                'cfg_name' => 'FILTERS_USER_LOGINIP',
                'cfg_value' => '',
				'cfg_description' => '过滤器，用户登录IP',
                'created_at' => $nowtime,
                'updated_at' => $nowtime,
                'deleted_at' => NULL,
            ),
            12 => 
            array (
                'cfg_id' => 13,
                'cfg_name' => 'FILTERS_USER_DEPARTMENT',
                'cfg_value' => '',
				'cfg_description' => '过滤器，部门',
                'created_at' => $nowtime,
                'updated_at' => $nowtime,
                'deleted_at' => NULL,
            ),
            13 => 
            array (
                'cfg_id' => 14,
                'cfg_name' => 'FILTERS_USER_DISABLEDUSER',
                'cfg_value' => '',
				'cfg_description' => '过滤器，已禁用用户',
                'created_at' => $nowtime,
                'updated_at' => $nowtime,
                'deleted_at' => NULL,
            ),
            14 => 
            array (
                'cfg_id' => 15,
                'cfg_name' => 'PERPAGE_RECORDS_FOR_APPLICANT',
                'cfg_value' => '5',
				'cfg_description' => 'APPLICANT每页记录数（配置跟随用户）',
                'created_at' => $nowtime,
                'updated_at' => $nowtime,
                'deleted_at' => NULL,
            ),
            15 => 
            array (
                'cfg_id' => 16,
                'cfg_name' => 'PERPAGE_RECORDS_FOR_TODO',
                'cfg_value' => '5',
				'cfg_description' => 'TODO每页记录数（配置跟随用户）',
                'created_at' => $nowtime,
                'updated_at' => $nowtime,
                'deleted_at' => NULL,
            ),
            16 => 
            array (
                'cfg_id' => 17,
                'cfg_name' => 'PERPAGE_RECORDS_FOR_ARCHIVED',
                'cfg_value' => '5',
				'cfg_description' => 'ARCHIVE每页记录数（配置跟随用户）',
                'created_at' => $nowtime,
                'updated_at' => $nowtime,
                'deleted_at' => NULL,
            ),
            17 => 
            array (
                'cfg_id' => 18,
                'cfg_name' => 'PERPAGE_RECORDS_FOR_ANALYTICS',
                'cfg_value' => '5',
				'cfg_description' => 'ANALYTICS每页记录数（配置跟随用户）',
                'created_at' => $nowtime,
                'updated_at' => $nowtime,
                'deleted_at' => NULL,
            ),
            18 => 
            array (
                'cfg_id' => 19,
                'cfg_name' => 'SITE_MAINTENANCE_ALLOWED',
                'cfg_value' => '127.0.0.1',
				'cfg_description' => '站点系统维护允许IP，逗号分隔',
                'created_at' => $nowtime,
                'updated_at' => $nowtime,
                'deleted_at' => NULL,
            ),
            19 => 
            array (
                'cfg_id' => 20,
                'cfg_name' => 'SITE_MAINTENANCE_MESSAGE',
                'cfg_value' => '',
				'cfg_description' => '站点系统维护消息',
                'created_at' => $nowtime,
                'updated_at' => $nowtime,
                'deleted_at' => NULL,
            ),
            20 => 
            array (
                'cfg_id' => 21,
                'cfg_name' => 'EMAIL_ENABLED',
                'cfg_value' => '0',
				'cfg_description' => '是否开启邮件通知，1为开启，0为关闭',
                'created_at' => $nowtime,
                'updated_at' => $nowtime,
                'deleted_at' => NULL,
            ),
            23 => 
            array (
                'cfg_id' => 24,
                'cfg_name' => 'SITE_EXPIRED_DATE',
                'cfg_value' => 'AMjAyMC0wMS0wMSAwMDowMDowMA==',
				'cfg_description' => '站点系统维护日期',
                'created_at' => $nowtime,
                'updated_at' => $nowtime,
                'deleted_at' => NULL,
            ),
        ));
    }
}
