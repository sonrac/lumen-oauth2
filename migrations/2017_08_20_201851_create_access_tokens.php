<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateAccessToken
 * Access token table migration
 *
 * @author Donii Sergii <doniysa@gmail.com>
 */
class CreateAccessTokens extends Migration
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
        Schema::create('access_tokens', function (Blueprint $table) {
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('client_id')->nullable();
            $table->string('access_token', 1024)->notNull();
            $table->string('grant_type', 50)->notNull();
            $table->bigInteger('expire_date_time')->notNull();
            $table->boolean('revoked')->notNull()->default(false);
            $table->primary(['access_token']);
            $table->bigInteger('created_at')->nullable();
            $table->bigInteger('updated_at')->nullable();

            $table->foreign(['user_id'])
                ->references(['id'])
                ->on('users')
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
        Schema::table('access_tokens', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
        Schema::dropIfExists('access_tokens');
    }
}
