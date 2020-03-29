<?php

use Illuminate\Database\Seeder;

use App\Models\Admin\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

		// 重置角色和权限的缓存
        app()['cache']->forget('spatie.permission.cache');

		// 创建权限
		Permission::create(['guard_name' => 'api', 'name' => 'permission_super_admin']);
		Permission::create(['guard_name' => 'api', 'name' => 'permission_admin_config']);
		Permission::create(['guard_name' => 'api', 'name' => 'permission_admin_user']);
		Permission::create(['guard_name' => 'api', 'name' => 'permission_admin_role']);
		Permission::create(['guard_name' => 'api', 'name' => 'permission_admin_permission']);

		// 创建角色，并赋予权限
		$role = Role::create(['guard_name' => 'api', 'name' => 'role_super_admin']);
		$role->givePermissionTo('permission_super_admin');
		// $role->givePermissionTo('permission_admin_config');
		// $role->givePermissionTo('permission_admin_user');
		// $role->givePermissionTo('permission_admin_role');
		// $role->givePermissionTo('permission_admin_permission');

		$role = Role::create(['guard_name' => 'api', 'name' => 'role_admin_config']);
		$role->givePermissionTo('permission_admin_config');
		
		$role = Role::create(['guard_name' => 'api', 'name' => 'role_admin_user']);
		$role->givePermissionTo('permission_admin_user');
		
		$role = Role::create(['guard_name' => 'api', 'name' => 'role_admin_role']);
		$role->givePermissionTo('permission_admin_role');
		
		$role = Role::create(['guard_name' => 'api', 'name' => 'role_admin_permission']);
		$role->givePermissionTo('permission_admin_permission');
		
		// 赋予用户角色（管理员id为1和2）
		$user = User::where('id', 1)->first();
		$user->assignRole('role_super_admin');
		$user = User::where('id', 2)->first();
		$user->assignRole('role_super_admin');

    }
}
