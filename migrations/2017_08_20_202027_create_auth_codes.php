<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateAuthCodes
 * Auth codes table migration
 *
 * @author Donii Sergii <doniysa@gmail.com>
 */
class CreateAuthCodes extends Migration
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
        Schema::create('auth_codes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 1000);
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('client_id')->notNull();
            $table->unsignedInteger('revoked')->defaultValue(false);
            $table->string('redirect_uri')->nullable();
            $table->string('code_scopes');
            $table->bigInteger('expires_at')->notNull();
            $table->bigInteger('created_at')->nullable();
            $table->bigInteger('updated_at')->nullable();

            $table->foreign(['user_id'])
                ->references(['id'])
                ->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign(['client_id'])
                ->references(['id'])
                ->on('clients')
                ->onDelete('cascade')
                ->onUpdate('cascade');
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
        Schema::table('auth_codes', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->dropForeign(['user_id']);
        });
        Schema::dropIfExists('auth_codes');
    }
}
