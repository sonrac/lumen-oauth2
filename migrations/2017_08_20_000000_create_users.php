<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateUsers
 * Create user table migration.
 *
 * @author Donii Sergii <doniysa@gmail.com>
 */
class CreateUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('password')->notNUll();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->tinyInteger('is_active')->nullable()->defaultValue(1);
            $table->bigInteger('last_login')->nullable();
            $table->bigInteger('register_at')->notNull();
            $table->bigInteger('created_at')->nullable();
            $table->bigInteger('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
