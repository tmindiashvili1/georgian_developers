<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property bool language_update_at
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property mixed last_language_modified
 * @property mixed owner
 * @property mixed name
 */
class GithubUserRepo extends BaseModel
{

    use SoftDeletes;

    /**
     * @var string
     */
    protected $table = 'github_user_repos';

    /**
     * @var array
     */
    protected $fillable = [
        'repo_id',
        'node_id',
        'name',
        'full_name',
        'description',
        'fork',
        'homepage',
        'stargazers_count',
        'watchers_count',
        'fork_count',
        'archived',
        'disabled',
        'subscribers_count',
        'pushed_at',
        'language_update_at',
        'last_language_modified'
    ];

    /**
     * @var array
     */
    protected $dates = [
        'pushed_at',
        'deleted_at',
        'language_update_at'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner()
    {
        return $this->belongsTo(GithubUser::class, 'owner_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function mainLanguage()
    {
        return $this->belongsTo(Language::class, 'main_language_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function languages()
    {
        return $this->belongsToMany(Language::class, 'repos_languages', 'language_id', 'repo_id');
    }

}
