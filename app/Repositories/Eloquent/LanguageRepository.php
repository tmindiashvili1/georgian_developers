<?php


namespace App\Repositories\Eloquent;


use App\Models\GithubUserRepo;
use App\Models\Language;
use App\Repositories\Contracts\ILanguageRepository;

class LanguageRepository extends BaseRepository implements ILanguageRepository
{

    /**
     * LanguageRepository constructor.
     * @param Language $language
     */
    public function __construct
    (
        Language $language
    )
    {
        parent::__construct($language);
    }



}
