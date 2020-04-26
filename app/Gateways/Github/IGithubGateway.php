<?php

namespace App\Gateways\Github;

use App\Gateways\IBaseGateway;

interface IGithubGateway extends IBaseGateway
{

    /**
     * @param $lastModified
     * @return mixed
     */
    public function setLastModifiedHeader($lastModified);

    /**
     * @param string $module
     * @param array $params
     * @return mixed
     */
    public function search($params = [],$module = 'users');

    /**
     * @param $login
     * @param array $params
     * @return mixed
     */
    public function getUserRepos($login,$params = []);

    /**
     * @param $login
     * @param $repo
     * @return mixed
     */
    public function getRepoLanguages($login, $repo);

    /**
     * @param $login
     * @return mixed
     */
    public function getUserFullInfo($login);

}
