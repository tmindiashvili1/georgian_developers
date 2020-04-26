<?php


namespace App\Providers;

use App\Gateways\Github\GithubGateway;
use App\Gateways\Github\IGithubGateway;
use Illuminate\Support\ServiceProvider;

class GatewayServiceProvider extends ServiceProvider
{

    /**
     * Register any gateway services.
     *
     * @return void
     */
    public function register()
    {
        parent::register();

        $this->app->bind(IGithubGateway::class, GithubGateway::class);

    }

}
