<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserDetailsTbl extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('user_details');
        
        Schema::create('user_details', function ($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('user_detail_id');
            $table->integer('user_id');
            $table->integer('user_profile_img')->default(0); // user profile image file id
        });
        
        DB::table('user_details')->insert(array(
            'user_id' => 1,
        ));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_details');
    }
}
