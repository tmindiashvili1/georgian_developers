<?php


namespace App\Gateways\Github;

use App\Gateways\BaseGateway;
use Illuminate\Support\Arr;

class GithubGateway extends BaseGateway implements IGithubGateway
{

    /**
     * @return void
     */
    protected function setBaseCredentials()
    {
        $this->baseUrl = config('services.github_api.url');
        $this->headers['Authorization'] = 'token ' . config('services.github_api.token');
    }

    /**
     * @param $lastModified
     * @return $this
     */
    public function setLastModifiedHeader($lastModified)
    {
        $this->headers['If-Modified-Since'] = $lastModified;
        return $this;
    }

    /**
     * @param string $module
     * @param array $params
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function search($params = [],$module = 'users')
    {

        $this->endPoint = '/search/' . $module;
        $this->method = 'GET';
        $this->params = $params;
        $this->requestOption = 'query';

        // Do request
        $this->doRequest();

    }

    /**
     * @param $login
     * @param array $params
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getUserRepos($login,$params = [])
    {

        $this->endPoint = '/users/' . $login . '/repos';
        $this->method = 'GET';
        $this->params = $params;
        $this->requestOption = 'query';

        // Do request
        $this->doRequest();
    }

    /**
     * @param $login
     * @param $repo
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getRepoLanguages($login, $repo)
    {
        $this->endPoint = '/repos/' . $login . '/' . $repo . '/languages';
        $this->method = 'GET';

        // Do request
        $this->doRequest();
    }

    /**
     * @param $login
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getUserFullInfo($login)
    {
        $this->endPoint = '/users/' . $login;
        $this->method = 'GET';
        $this->requestOption = 'query';

        // Do request
        $this->doRequest();
    }

}
