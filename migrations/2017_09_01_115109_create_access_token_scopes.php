<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccessTokenScopes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('access_token_scopes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('access_token');
            $table->string('scope');

            $table->foreign(['access_token'])
                ->references(['access_token'])
                ->on('access_tokens')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign(['scope'])
                ->references(['name'])
                ->on('scopes')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->unique(['access_token', 'scope']);
            $table->bigInteger('created_at')->nullable();
            $table->bigInteger('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('access_token_scopes', function (Blueprint $table) {
            $table->dropForeign(['access_token']);
            $table->dropForeign(['scope']);
        });
        Schema::drop('access_token_scopes');
    }
}
