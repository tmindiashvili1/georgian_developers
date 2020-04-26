<?php

namespace App\Services\Objects;

use App\Gateways\Github\IGithubGateway;
use App\Models\GithubUser;
use App\Models\GithubUserRepo;
use App\Repositories\Contracts\IGithubUserRepoRepository;
use App\Repositories\Contracts\IGithubUserRepository;
use App\Repositories\Contracts\ILanguageRepository;
use App\Services\Contracts\IGithubRepoParserService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * @property IGithubUserRepository githubUserRepository
 * @property IGithubUserRepoRepository githubUserRepoRepository
 * @property IGithubGateway githubGateway
 * @property ILanguageRepository languageRepository
 */
class GithubRepoParserService implements IGithubRepoParserService
{

    /**
     * @var IGithubUserRepository
     */
    protected $githubUserRepository;

    /**
     * @var IGithubUserRepoRepository
     */
    protected $githubUserRepoRepository;

    /**
     * @var ILanguageRepository
     */
    protected $languageRepository;

    /**
     * GithubRepoParserService constructor.
     * @param IGithubUserRepository $githubUserRepository
     * @param IGithubUserRepoRepository $githubUserRepoRepository
     * @param ILanguageRepository $languageRepository
     * @param IGithubGateway $githubGateway
     */
    public function __construct
    (
        IGithubUserRepository $githubUserRepository,
        IGithubUserRepoRepository $githubUserRepoRepository,
        ILanguageRepository $languageRepository,
        IGithubGateway $githubGateway
    )
    {
        $this->githubUserRepository = $githubUserRepository;
        $this->githubUserRepoRepository = $githubUserRepoRepository;
        $this->githubGateway = $githubGateway;
        $this->languageRepository = $languageRepository;
    }

    /**
     * Users repos update.
     */
    public function usersRepoUpdate()
    {

        $this->githubUserRepository
            ->where('repo_update_at', '<=', now())
            ->chunk(100, function($githubUsers){

                foreach($githubUsers as $githubUser) {

                    $page = 1;

                    while(true) {

                        $params = [
                            'page'      => $page,
                            'per_page'  => 100
                        ];

                        // Get user repos.
                        $this->githubGateway->getUserRepos($githubUser->login,$params);

                        /**
                         * User repo response.
                         *
                         * @var array
                         */
                        $response = $this->githubGateway->getResponse();

                        if (!$response['status']) {
                            break;
                        }

                        if(empty($response['data'])) {
                            break;
                        }

                        // Parse user repos.
                        $this->parseUserRepo($response['data'], $githubUser);
                        
                        // Increase page.
                        $page++;

                    }

                }

            });



    }

    /**
     * @param array $repoItems
     * @param GithubUser $githubUser
     * @throws \Exception
     */
    public function parseUserRepo(array $repoItems,GithubUser $githubUser)
    {

        foreach($repoItems as $repoItem) {

            try {

                // Save user repo item.
                $this->saveUserRepoItem($repoItem, $githubUser);

            } catch (\Exception $ex) {
                continue;
            }

            $githubUser->update(['repo_update_at' => now()->addWeeks(2)]);

        }


    }

    /**
     * @param array $repoItem
     * @param GithubUser $githubUser
     * @throws \Exception
     */
    public function saveUserRepoItem(array $repoItem,GithubUser $githubUser)
    {

        try {

            DB::beginTransaction();

            /**
             * @var $repo GithubUserRepo
             */
            $repo = $this->githubUserRepoRepository->updateOrCreate([
                'repo_id'   => $repoItem['id'],
                'node_id'   => $repoItem['node_id'],
            ],[
                'name'                  => $repoItem['name'],
                'full_name'             => $repoItem['full_name'],
                'description'           => $repoItem['description'],
                'fork'                  => $repoItem['fork'],
                'homepage'              => $repoItem['homepage'],
                'stargazers_count'      => $repoItem['stargazers_count'],
                'watchers_count'        => $repoItem['watchers_count'],
                'fork_count'            => $repoItem['forks_count'],
                'archived'              => $repoItem['archived'],
                'disabled'              => $repoItem['disabled'],
                'subscribers_count'     => !empty($repoItem['subscribers_count']) ? $repoItem['subscribers_count'] : null,
                'pushed_at'             => Carbon::parse($repoItem['pushed_at'])
            ]);

            // Set/Unset main repo language.
            if (!empty($repoItem['language'])) {

                $language = $this->languageRepository->updateOrCreate([
                    'name'  => $repoItem['language']
                ]);

                $repo->mainLanguage()->associate($language);

            } else {
                $repo->mainLanguage()->dissociate();
            }

            // Associate repo owner.
            $repo->owner()->associate($githubUser);

            if ( $repo->wasRecentlyCreated ) {
                $repo->language_update_at = now()->addHours(1);
            }

            // Save repo update/create dates.
            $repo->created_at = Carbon::parse($repoItem['created_at']);
            $repo->updated_at = Carbon::parse($repoItem['updated_at']);

            // Save repo.
            $repo->save();

            DB::commit();

        } catch(\Exception $ex) {
            dd($ex->getMessage());
            DB::rollBack();
            Log::error('ERROR_UPDATE_GITHUB_REPO', [ 'message' => $ex->getMessage(),'github' => $repoItem, 'user' => $githubUser]);
            throw new \Exception('ERROR_UPDATE_GITHUB_REPO', $ex->getCode());
        }



    }


}
