<?php

namespace App\Services\Contracts;

interface IGithubUserParserService
{

    /**
     * @return mixed
     */
    public function updateUsersUpdateInfo();

    /**
     * @param $login
     * @param null $githubUser
     * @return mixed
     */
    public function parseUserInfo($login, $githubUser = null);

    /**
     * @return mixed
     */
    public function parseGithubUsersByFilter();

    /**
     * @param $userItem
     * @return mixed
     */
    public function saveGithubUserBaseData($userItem);



}
