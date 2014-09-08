<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDefaultUsers extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*create the first user as super admin*/
        $user = Sentry::createUser(array(
                'email'     => 'amitavroy@gmail.com',
                'password'  => 'test1234',
                'activated' => true,
                'first_name' => 'Amitav',
                'last_name' => 'Roy'
            ));

        $group = Sentry::createGroup(array(
                'name' => 'Super Admin',
                'permissions' => array(
                    'create_users' => 1,
                    'edit_users' => 1,
                    'delete_users' => 1,
                    'manage_users' => 1,
                    'manage_permissions' => 1,
                ),
            ));

        $adminGroup = Sentry::findGroupById(1);
        $user->addGroup($adminGroup);

        /*create second user as admin*/
        $user = Sentry::createUser(array(
                'email'     => 'jhon.doe@gmail.com',
                'password'  => 'test1234',
                'activated' => true,
                'first_name' => 'Jhon',
                'last_name' => 'Doe'
            ));

        $group = Sentry::createGroup(array(
                'name' => 'Administrator',
                'permissions' => array(
                    'create_users' => 1,
                    'edit_users' => 1,
                    'delete_users' => 0,
                    'manage_users' => 0,
                    'manage_permissions' => 0,
                ),
            ));

        $adminGroup = Sentry::findGroupById(2);
        $user->addGroup($adminGroup);

        $group = Sentry::createGroup(array(
                'name' => 'Authenticated User'
            ));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }

}
