<?php


namespace App\Repositories\Eloquent;


use App\Models\GithubUserRepo;
use App\Repositories\Contracts\IGithubUserRepoRepository;

class GithubUserRepoRepository extends BaseRepository implements IGithubUserRepoRepository
{

    /**
     * GithubUserRepoRepository constructor.
     * @param GithubUserRepo $githubUserRepo
     */
    public function __construct
    (
        GithubUserRepo $githubUserRepo
    )
    {
        parent::__construct($githubUserRepo);
    }

}
