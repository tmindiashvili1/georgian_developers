<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGithubUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('github_users', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Unique columns.
            $table->string('login')->unique();
            $table->string('github_user_id')->unique()->index()->nullable();
            $table->string('node_id')->unique()->nullable();

            // User info
            $table->string('avatar_url')->nullable();
            $table->string('name')->nullable();
            $table->string('company')->nullable();
            $table->string('website')->nullable();
            $table->string('location')->nullable();
            $table->string('email')->nullable();
            $table->boolean('hireable')->default(0)->nullable();
            $table->text('bio')->nullable();
            $table->integer('followers')->nullable();
            $table->integer('following')->nullable();

            // Additional info.
            $table->string('etag')->nullable();
            $table->string('last_modified')->nullable();

            //Dates
            $table->dateTime('user_info_update_at')->nullable();


            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('github_users');
    }
}
