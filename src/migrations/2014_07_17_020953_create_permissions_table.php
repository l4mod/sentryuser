<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePermissionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::dropIfExists('permissions');

        Schema::create('permissions', function($table){
                $table->engine = 'InnoDB';
                $table->increments('permission_id');
                $table->string('permission_name', 100);
                $table->string('permission_group_name', 100);
            });

        DB::table('permissions')->insert(array(
                'permission_name' => 'create_users',
                'permission_group_name' => 'Users',
            ));
        DB::table('permissions')->insert(array(
                'permission_name' => 'edit_users',
                'permission_group_name' => 'Users',
            ));
        DB::table('permissions')->insert(array(
                'permission_name' => 'delete_users',
                'permission_group_name' => 'Users',
            ));
        DB::table('permissions')->insert(array(
                'permission_name' => 'manage_users',
                'permission_group_name' => 'Users',
            ));
        DB::table('permissions')->insert(array(
                'permission_name' => 'manage_permissions',
                'permission_group_name' => 'Users',
            ));
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::dropIfExists('permissions');
	}

}
