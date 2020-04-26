<?php


namespace App\Services\Objects;


use App\Gateways\Github\IGithubGateway;
use App\Models\GithubUserRepo;
use App\Repositories\Contracts\IGithubUserRepoRepository;
use App\Repositories\Contracts\ILanguageRepository;
use App\Services\Contracts\IReposLanguageParserService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * @property IGithubGateway githubGateway
 * @property ILanguageRepository languageRepository
 * @property IGithubUserRepoRepository githubUserRepoRepository
 */
class ReposLanguageParserService implements IReposLanguageParserService
{

    /**
     * @var IGithubGateway
     */
    protected $githubGateway;

    /**
     * @var ILanguageRepository
     */
    protected $languageRepository;

    /**
     * @var IGithubUserRepoRepository
     */
    protected $githubUserRepoRepository;

    /**
     * ReposLanguageParserService constructor.
     * @param IGithubGateway $githubGateway
     * @param ILanguageRepository $languageRepository
     * @param IGithubUserRepoRepository $githubUserRepoRepository
     */
    public function __construct
    (
        IGithubGateway $githubGateway,
        ILanguageRepository $languageRepository,
        IGithubUserRepoRepository $githubUserRepoRepository
    )
    {
        $this->githubGateway = $githubGateway;
        $this->languageRepository = $languageRepository;
        $this->githubUserRepoRepository = $githubUserRepoRepository;
    }

    /**
     * Repos languages update.
     */
    public function reposLanguagesUpdate()
    {

        $this->githubUserRepoRepository
            ->where('id', '!=', null)
                ->chunk(100, function($githubRepos) {

                    foreach ($githubRepos as $githubRepo) {

                        // Parse repo language data.
                        $this->parseRepoLanguageData($githubRepo);

                    }
            });

    }

    /**
     * @param GithubUserRepo $githubRepo
     * @throws \Exception
     */
    public function parseRepoLanguageData(GithubUserRepo $githubRepo)
    {

        try {

            DB::beginTransaction();

            // Last modified repos.
            $lastModified = $githubRepo ? $githubRepo->last_language_modified : '';

            // Get repo languages.
            $this->githubGateway->setLastModifiedHeader($lastModified)
                            ->getRepoLanguages($githubRepo->owner->login, $githubRepo->name);

            /**
             * Get repos languages.
             *
             * @var $response array
             */
            $response = $this->githubGateway->getResponse();

            if ( !empty($response['headers']['Last-Modified']) && $response['headers']['Last-Modified'][0] == $lastModified) {
                return;
            }

            // Save repo languages.
            $this->saveRepoLanguages($githubRepo,$response['data']);

            // Save last language modified.
            $githubRepo->update(['last_language_modified' => $response['headers']['Last-Modified'][0]]);

            DB::commit();
        } catch (\Exception $ex) {
            DB::rollBack();
            Log::error('ERROR_REPO_LANGUAGE_PARSE', [ 'message' => $ex->getMessage(),'repo' => $githubRepo]);
            throw new \Exception('ERROR_REPO_LANGUAGE_PARSE', $ex->getCode());
        }

    }

    /**
     * @param GithubUserRepo $githubRepo
     * @param array $languages
     * @throws \Exception
     */
    public function saveRepoLanguages(GithubUserRepo $githubRepo, array $languages)
    {

        try {

            DB::beginTransaction();

            // Detach all languages.
            if (!empty($languages)) {
                $githubRepo->languages()->detach();
            }

            $allLanguages = [];

            foreach($languages as $lang => $value) {

                $language = $this->languageRepository->updateOrCreate([
                    'name'  => $lang
                ]);

                $allLanguages[$language->id] = [
                    'quantity'  => $value
                ];

            }

            // Sync languages.
            $githubRepo->languages()->sync($allLanguages);
            $githubRepo->language_update_at = now()->addWeeks(3);
            $githubRepo->save();

            DB::commit();

        } catch (\Exception $ex) {
            DB::rollBack();
            Log::error('ERROR_REPO_LANGUAGE_SAVE', [ 'message' => $ex->getMessage(),'languages' => $languages, 'repo' => $githubRepo]);
            throw new \Exception('ERROR_REPO_LANGUAGE_SAVE', $ex->getCode());
        }


    }


}
