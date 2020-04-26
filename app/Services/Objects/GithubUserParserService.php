<?php

namespace App\Services\Objects;

use App\Gateways\Github\IGithubGateway;
use App\Models\GithubUser;
use App\Repositories\Contracts\IGithubUserRepository;
use App\Services\Contracts\IGithubUserParserService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * @property IGithubGateway githubGateway
 * @property IGithubUserRepository githubUserRepository
 */
class GithubUserParserService implements IGithubUserParserService
{

    /**
     * @var IGithubGateway
     */
    protected $githubGateway;

    /**
     * @var IGithubUserRepository
     */
    protected $githubUserRepository;

    /**
     * GithubUserParserService constructor.
     * @param IGithubGateway $githubGateway
     * @param IGithubUserRepository $githubUserRepository
     */
    public function __construct
    (
        IGithubGateway $githubGateway,
        IGithubUserRepository $githubUserRepository
    )
    {
        $this->githubGateway = $githubGateway;
        $this->githubUserRepository = $githubUserRepository;
    }

    /**
     * Update Some users update info.
     *
     * @throws \Exception
     */
    public function updateUsersUpdateInfo()
    {

        try {

            $this->githubUserRepository
                ->where('user_info_update_at', '<=', now())
                ->chunk(100, function($githubUsers){

                    foreach($githubUsers as $githubUser) {

                        // Parse user info.
                        $this->parseUserInfo($githubUser->login, $githubUser);

                    }

                });

        } catch (\Exception $ex) {
            Log::error('ERROR_UPDATE_USERS_INFO', [ 'message' => $ex->getMessage()]);
            throw new \Exception('ERROR_UPDATE_USERS_INFO', $ex->getCode());
        }

    }

    /**
     * @param $login
     * @param null $githubUser
     * @return mixed|void
     * @throws \Exception
     */
    public function parseUserInfo($login, $githubUser = null)
    {


        if (is_null($githubUser)) {

            /**
             * @var $githubUser GithubUser
             */
            $githubUser = $this->githubUserRepository->where('login', $login)->first();

        }

        // Last modified user.
        $lastModified = $githubUser ? $githubUser->last_modified : '';

        /**
         * Parse github user info.
         */
        $this->githubGateway->setLastModifiedHeader($lastModified)->getUserFullInfo($login);

        /**
         * Response data.
         *
         * @var $response array
         */
        $response = $this->githubGateway->getResponse();

        if (!$response['status']) {
            throw new \Exception($response['message'], $response['code']);
        }

        try {
            DB::beginTransaction();

            if ( !empty($response['headers']['Last-Modified']) && $response['headers']['Last-Modified'][0] == $lastModified) {
                return;
            }

            // Save github user full info.
            $this->saveGithubUserFullInfo($response['data'], $response['headers']);

            DB::commit();
        } catch (\Exception $ex) {
            DB::rollBack();
            Log::error('ERROR_SAVE_GITHUB_USER_FULL_INFO', [ 'user_response' => $response['data']]);
            throw new \Exception('ERROR_SAVE_GITHUB_USER_FULL_INFO', $ex->getCode());
        }

    }

    /**
     *
     * Save github user full information.
     *
     * @param $data
     * @param $headers
     *
     * @return void
     * @throws \Exception
     */
    public function saveGithubUserFullInfo($data, $headers = [])
    {

        try {

            /**
             *
             * Save github user full info.
             *
             * @var $githubUser GithubUser
             */
            $githubUser = $this->githubUserRepository->updateOrCreate([
                'login'                 => $data['login'],
                'github_user_id'        => $data['id'],
                'node_id'               => $data['node_id']
            ],[
                'avatar_url'            => $data['avatar_url'],
                'name'                  => $data['name'],
                'company'               => $data['company'],
                'website'               => $data['blog'],
                'location'              => $data['location'],
                'email'                 => $data['email'],
                'hireable'              => $data['hireable'],
                'bio'                   => $data['bio'],
                'followers'             => $data['followers'],
                'following'             => $data['following'],
                'user_info_update_at'   => now()->addWeeks(2)
            ]);

            if (!empty($headers['ETag'])) {
                $githubUser->update(['etag' => trim($headers["ETag"][0],'"')]);
            }

            if (!empty($headers['Last-Modified'])) {
                $githubUser->update(['last_modified' => $headers['Last-Modified'][0]]);
            }

            $githubUser->created_at = Carbon::parse($data['created_at']);
            $githubUser->updated_at = Carbon::parse($data['updated_at']);
            $githubUser->save();

        } catch (\Exception $ex) {
            Log::error('ERROR_SAVE_GITHUB_USER_FULL_INFORMATION', [ 'data' => $data]);
            throw new \Exception('ERROR_SAVE_GITHUB_USER_FULL_INFORMATION', $ex->getCode());
        }


    }

    /**
     * @return mixed|void
     */
    public function parseGithubUsersByFilter()
    {

        foreach(config('github.locations') as $locationName) {

            $page = 1;

            /**
             * While exist data, filter location per page.
             */
            while(true) {

                $filterData = [
                    'q'         => 'location:' . $locationName,
                    'page'      => $page,
                    'per_page'  => 100
                ];

                // Search user.
                $this->githubGateway->search($filterData,'users');

                /**
                 * Response data from Github API.
                 *
                 * @var $response array
                 */
                $response = $this->githubGateway->getResponse();

                if (!$response['status']) {
                    break;
                }

                // Pass items for parse and save our DB.
                $this->githubUsersParse($response['data']['items']);

                // Increase page.
                $page++;

            }

        }

    }

    /**
     * Parse github users.
     * Save User our DB.
     *
     * @param $items
     *
     * @return void
     */
    protected function githubUsersParse($items)
    {

        foreach ( $items as $item ) {


            try {

                // Save github user.
                $this->saveGithubUserBaseData($item);

            } catch (\Exception $ex) {
                continue;
            }

        }

    }

    /**
     * Save github user by his data.
     *
     * @param $userItem
     *
     * @return GithubUser
     * @throws \Exception
     */
    public function saveGithubUserBaseData($userItem)
    {

        try {

            DB::beginTransaction();

            /**
             * @var $githubUser GithubUser
             */
            $githubUser = $this->githubUserRepository->updateOrCreate([
                'login'                 => $userItem['login'],
                'github_user_id'        => $userItem['id'],
                'node_id'               => $userItem['node_id']
            ],[
                'avatar_url'            => $userItem['avatar_url']
            ]);

            if ( $githubUser->wasRecentlyCreated ) {
                $githubUser->update([
                    'repo_update_at'            => now()->addMinutes(30),
                    'user_info_update_at'       => now()->addMinutes(10)
                ]);
            }

            DB::commit();

            return $githubUser;

        } catch (\Exception $ex)  {
            DB::rollBack();
            Log::error('ERROR_SAVE_GITHUB_USER_BASE_DATA', [ 'user_item' => $userItem]);
            throw new \Exception('ERROR_SAVE_GITHUB_USER_BASE_DATA', $ex->getCode());
        }


    }

}
