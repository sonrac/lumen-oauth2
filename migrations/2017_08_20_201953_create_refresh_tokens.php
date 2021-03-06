<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateRefreshToken
 * Refresh token table migration.
 *
 * @author Donii Sergii <doniysa@gmail.com>
 */
class CreateRefreshTokens extends Migration
{
    /**
     * Run the migrations.
     *
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    public function up()
    {
        Schema::create('refresh_tokens', function (Blueprint $table) {
            $table->increments('id');
            $table->string('access_token');
            $table->string('refresh_token');
            $table->string('revoked')->defaultValue(false);
            $table->bigInteger('expires_at');
            $table->bigInteger('created_at')->nullable();
            $table->bigInteger('updated_at')->nullable();

            $table->foreign('access_token')
                ->references('access_token')
                ->on('access_tokens')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    public function down()
    {
        Schema::dropIfExists('refresh_tokens');
    }
}
