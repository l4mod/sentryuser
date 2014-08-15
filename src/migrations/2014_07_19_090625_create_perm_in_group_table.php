<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePermInGroupTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('permission_in_group');

        Schema::create('permission_in_group', function($table){
                $table->engine = 'InnoDB';
                $table->increments('ping_id');
                $table->integer('permission_id');
                $table->integer('group_id');
                $table->integer('allow');

                $table->index(array('permission_id', 'group_id'));
            });

        /*for super admin by default*/
        DB::table('permission_in_group')->insert(array(
                'permission_id' => 1,
                'group_id' => 1,
                'allow' => 1,
            ));
        DB::table('permission_in_group')->insert(array(
                'permission_id' => 2,
                'group_id' => 1,
                'allow' => 1,
            ));
        DB::table('permission_in_group')->insert(array(
                'permission_id' => 3,
                'group_id' => 1,
                'allow' => 1,
            ));
        DB::table('permission_in_group')->insert(array(
                'permission_id' => 4,
                'group_id' => 1,
                'allow' => 1,
            ));
        DB::table('permission_in_group')->insert(array(
                'permission_id' => 5,
                'group_id' => 1,
                'allow' => 1,
            ));


        /*for admin by default*/
        DB::table('permission_in_group')->insert(array(
                'permission_id' => 1,
                'group_id' => 2,
                'allow' => 1,
            ));
        DB::table('permission_in_group')->insert(array(
                'permission_id' => 2,
                'group_id' => 2,
                'allow' => 1,
            ));
        DB::table('permission_in_group')->insert(array(
                'permission_id' => 3,
                'group_id' => 2,
                'allow' => 0,
            ));
        DB::table('permission_in_group')->insert(array(
                'permission_id' => 4,
                'group_id' => 2,
                'allow' => 0,
            ));
        DB::table('permission_in_group')->insert(array(
                'permission_id' => 5,
                'group_id' => 2,
                'allow' => 0,
            ));

        /*for authneticated user*/
        DB::table('permission_in_group')->insert(array(
                'permission_id' => 1,
                'group_id' => 3,
                'allow' => 0,
            ));
        DB::table('permission_in_group')->insert(array(
                'permission_id' => 2,
                'group_id' => 3,
                'allow' => 0,
            ));
        DB::table('permission_in_group')->insert(array(
                'permission_id' => 3,
                'group_id' => 3,
                'allow' => 0,
            ));
        DB::table('permission_in_group')->insert(array(
                'permission_id' => 4,
                'group_id' => 3,
                'allow' => 0,
            ));
        DB::table('permission_in_group')->insert(array(
                'permission_id' => 5,
                'group_id' => 3,
                'allow' => 0,
            ));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('permission_in_group');
    }

}
