<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Carbon\Carbon;

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
            $table->increments('id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('title')->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->integer('client_id')->default(0)->index();
            $table->integer('editor')->default(0)->index();
            $table->integer('admin')->default(0)->index();
            $table->rememberToken();
            $table->dateTime('last_login')->nullable();
            $table->integer('login_count')->default(0);
            $table->timestamps();
        });
        \DB::update("ALTER TABLE users AUTO_INCREMENT = 1000;");
        \App\User::create(['first_name'=>'Erica', 'last_name'=>'Pauly','email'=>'erica@trackthatadvisor.com','title'=>'owner','password'=>\Hash::make('temp123'),'admin'=>1,'editor'=>1,'last_login'=>\DB::raw("NOW()"),'login_count'=>10]);
        \App\User::create(['first_name'=>'Blaine', 'last_name'=>'Jones','email'=>'blainecjones@gmail.com','title'=>'webmaster','password'=>\Hash::make('temp123'),'admin'=>0,'editor'=>1,'last_login'=>\DB::raw("SUBDATE(NOW(),1)"),'login_count'=>8]);
        \App\User::create(['first_name'=>'John', 'last_name'=>'Doe','email'=>'johndoe@gmail.com','title'=>'account manager','password'=>\Hash::make('temp123'),'client_id'=>2000,'admin'=>0,'editor'=>0,'last_login'=>\DB::raw("SUBDATE(NOW(),4)"),'login_count'=>7]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('users');
    }
}
