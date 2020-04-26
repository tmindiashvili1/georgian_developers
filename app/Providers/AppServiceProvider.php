<?php

namespace App\Providers;

use App\Services\Contracts\IGithubUserParserService;
use App\Services\Objects\GithubUserParserService;
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
