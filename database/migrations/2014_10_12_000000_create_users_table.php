<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->unique()->comment('登录名称');
            $table->string('displayname')->nullable()->comment('显示名称');
            $table->string('ldapname')->nullable()->comment('LDAP用户名');
            $table->string('department')->comment('部门');
            $table->string('email')->nullable()->comment('电子邮件');
            $table->string('password')->comment('用户密码');
            $table->jsonb('configs')->nullable()->comment('用户配置');
            $table->timestamp('login_time')->comment('登录时间');
			$table->integer('login_ttl')->default(0)->comment('登录有效时间');
			$table->string('login_ip',15)->default(null)->comment('登录ip');
			$table->integer('login_counts')->default(0)->comment('登录次数');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
