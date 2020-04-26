<?php

namespace App\Providers;

use App\Services\Contracts\IGithubRepoParserService;
use App\Services\Contracts\IGithubUserParserService;
use App\Services\Contracts\IReposLanguageParserService;
use App\Services\Objects\GithubRepoParserService;
use App\Services\Objects\GithubUserParserService;
use App\Services\Objects\ReposLanguageParserService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(IGithubUserParserService::class, GithubUserParserService::class);
        $this->app->bind(IGithubRepoParserService::class, GithubRepoParserService::class);
        $this->app->bind(IReposLanguageParserService::class, ReposLanguageParserService::class);

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
