<?php

namespace App\Providers;

use App\Repositories\Contracts\IGithubUserRepoRepository;
use App\Repositories\Contracts\IGithubUserRepository;
use App\Repositories\Contracts\ILanguageRepository;
use App\Repositories\Eloquent\BaseRepository;
use App\Repositories\Contracts\IBaseRepository;
use App\Repositories\Eloquent\GithubUserRepoRepository;
use App\Repositories\Eloquent\GithubUserRepository;
use App\Repositories\Eloquent\LanguageRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{

    /**
     * Register any repository services.
     *
     * @return void
     */
    public function register()
    {
        parent::register();

        $this->app->bind(IGithubUserRepoRepository::class, GithubUserRepoRepository::class);
        $this->app->bind(IGithubUserRepository::class, GithubUserRepository::class);
        $this->app->bind(ILanguageRepository::class, LanguageRepository::class);

    }

}
