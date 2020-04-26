<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGithubUserReposTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('github_user_repos', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Repos owner.
            $table->bigInteger('owner_id')->unsigned()->nullable();

            // Repo unique fields.
            $table->string('repo_id')->unique()->index()->nullable();
            $table->string('node_id')->unique()->nullable();
            $table->string('name')->nullable();
            $table->string('full_name')->nullable();

            // Repo info.
            $table->text('description')->nullable();
            $table->boolean('fork')->default(0)->nullable();
            $table->string('homepage')->nullable();
            $table->integer('stargazers_count')->default(0)->nullable();
            $table->integer('watchers_count')->default(0)->nullable();
            $table->bigInteger('main_language_id')->unsigned()->nullable();
            $table->integer('fork_count')->default(0)->nullable();
            $table->boolean('archived')->default(0)->nullable();
            $table->boolean('disabled')->default(0)->nullable();
            $table->integer('subscribers_count')->default(1)->nullable();
            $table->string('etag')->nullable();
            $table->string('last_language_modified')->nullable();

            $table->dateTimeTz('pushed_at')->nullable();
            $table->dateTime('language_update_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('owner_id')->references('id')->on('github_users')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('main_language_id')->references('id')->on('languages')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('github_user_repos');
    }
}
