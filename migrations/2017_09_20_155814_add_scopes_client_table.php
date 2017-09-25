<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddScopesClientTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_scopes', function (Blueprint $table) {
            $table->string('scope_name');
            $table->unsignedInteger('client_id');
            $table->unsignedInteger('user_id');
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
     */
    public function down()
    {
        Schema::table('client_scopes', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['client_id']);
        });

        Schema::drop('client_scopes');
    }
}
