<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReposLanguagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('repos_languages', function (Blueprint $table) {

            $table->bigInteger('language_id')->unsigned();
            $table->bigInteger('repo_id')->unsigned();
            $table->bigInteger('quantity')->unsigned()->nullable();

            $table->foreign('repo_id')->references('id')->on('github_user_repos')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('language_id')->references('id')->on('languages')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('repos_languages');
    }
}
