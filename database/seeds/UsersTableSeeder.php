<?php

use Illuminate\Database\Seeder;

use App\Models\Admin\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		$nowtime = date("Y-m-d H:i:s",time());
		$logintime = date("Y-m-d H:i:s", 86400);
		
		User::truncate();
		
		User::insert(array (
            0 => 
            array (
                // 'id' => 1,
                'name' => 'admin',
                'gendar' => '未知',
                'department' => 'admin',
                'ldapname' => 'admin',
                'email' => 'admin@xizhisoft.local',
                'displayname' => 'admin',
                'password' => '$2y$10$LZyZUTTyHugBeHGiSCumi.KKb4doF5eQYoKqIBYR03J84LLcEVVZW', // 默认密码123
                'login_time' => $logintime,
                'login_ip' => '255.255.255.255',
                'login_counts' => 0,
                'remember_token' => '',
                'created_at' => $nowtime,
                'updated_at' => $nowtime,
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                // 'id' => 2,
                'name' => 'root',
                'gendar' => '未知',
                'department' => 'root',
                'ldapname' => 'root',
                'email' => 'root@xizhisoft.local',
                'displayname' => 'root',
                'password' => '$2y$10$ihmDQIgX4hK8CPfH3PtImeeVW8mmAeP42I4Jbx0GcLtXtLiKxLaRi',
                'login_time' => $logintime,
                'login_ip' => '255.255.255.255',
                'login_counts' => 0,
                'remember_token' => '',
                'created_at' => $nowtime,
                'updated_at' => $nowtime,
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                // 'id' => 3,
                'name' => 'user1',
                'gendar' => '男',
                'department' => 'user',
                'ldapname' => 'user1',
                'email' => 'user1@xizhisoft.local',
                'displayname' => '古天乐',
                'password' => '$2y$10$ihmDQIgX4hK8CPfH3PtImeeVW8mmAeP42I4Jbx0GcLtXtLiKxLaRi',
                'login_time' => $logintime,
                'login_ip' => '255.255.255.255',
                'login_counts' => 0,
                'remember_token' => '',
                'created_at' => $nowtime,
                'updated_at' => $nowtime,
                'deleted_at' => NULL,
            ),
            3 => 
            array (
                // 'id' => 4,
                'name' => 'user2',
                'gendar' => '女',
                'department' => 'user',
                'ldapname' => 'user2',
                'email' => 'user2@xizhisoft.local',
                'displayname' => '杨幂',
                'password' => '$2y$10$ihmDQIgX4hK8CPfH3PtImeeVW8mmAeP42I4Jbx0GcLtXtLiKxLaRi',
                'login_time' => $logintime,
                'login_ip' => '255.255.255.255',
                'login_counts' => 0,
                'remember_token' => '',
                'created_at' => $nowtime,
                'updated_at' => $nowtime,
                'deleted_at' => NULL,
            ),
            4 => 
            array (
                // 'id' => 5,
                'name' => 'user3',
                'gendar' => '男',
                'department' => 'user',
                'ldapname' => 'user3',
                'email' => 'user3@xizhisoft.local',
                'displayname' => '吴彦祖',
                'password' => '$2y$10$ihmDQIgX4hK8CPfH3PtImeeVW8mmAeP42I4Jbx0GcLtXtLiKxLaRi',
                'login_time' => $logintime,
                'login_ip' => '255.255.255.255',
                'login_counts' => 0,
                'remember_token' => '',
                'created_at' => $nowtime,
                'updated_at' => $nowtime,
                'deleted_at' => NULL,
            ),
            5 => 
            array (
                // 'id' => 6,
                'name' => 'user4',
                'gendar' => '女',
                'department' => 'user',
                'ldapname' => 'user4',
                'email' => 'user4@xizhisoft.local',
                'displayname' => '迪丽热巴',
                'password' => '$2y$10$ihmDQIgX4hK8CPfH3PtImeeVW8mmAeP42I4Jbx0GcLtXtLiKxLaRi',
                'login_time' => $logintime,
                'login_ip' => '255.255.255.255',
                'login_counts' => 0,
                'remember_token' => '',
                'created_at' => $nowtime,
                'updated_at' => $nowtime,
                'deleted_at' => NULL,
            ),
            6 => 
            array (
                // 'id' => 7,
                'name' => 'user5',
                'gendar' => '男',
                'department' => 'user',
                'ldapname' => 'user5',
                'email' => 'user5@xizhisoft.local',
                'displayname' => '金城武',
                'password' => '$2y$10$ihmDQIgX4hK8CPfH3PtImeeVW8mmAeP42I4Jbx0GcLtXtLiKxLaRi',
                'login_time' => $logintime,
                'login_ip' => '255.255.255.255',
                'login_counts' => 0,
                'remember_token' => '',
                'created_at' => $nowtime,
                'updated_at' => $nowtime,
                'deleted_at' => NULL,
            ),
            7 => 
            array (
                // 'id' => 8,
                'name' => 'user6',
                'gendar' => '女',
                'department' => 'user',
                'ldapname' => 'user6',
                'email' => 'user6@xizhisoft.local',
                'displayname' => '刘亦菲',
                'password' => '$2y$10$ihmDQIgX4hK8CPfH3PtImeeVW8mmAeP42I4Jbx0GcLtXtLiKxLaRi',
                'login_time' => $logintime,
                'login_ip' => '255.255.255.255',
                'login_counts' => 0,
                'remember_token' => '',
                'created_at' => $nowtime,
                'updated_at' => $nowtime,
                'deleted_at' => NULL,
            ),
        ));
	}
}
