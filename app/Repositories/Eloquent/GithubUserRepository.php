<?php


namespace App\Repositories\Eloquent;


use App\Models\GithubUser;
use App\Repositories\Contracts\IGithubUserRepository;

class GithubUserRepository extends BaseRepository implements IGithubUserRepository
{

    /**
     * GithubUserRepository constructor.
     * @param GithubUser $githubUser
     */
    public function __construct
    (
        GithubUser $githubUser
    )
    {
        parent::__construct($githubUser);
    }

}
