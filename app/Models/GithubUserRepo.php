<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
        'pushed_at'
    ];

    /**
     * @var array
     */
    protected $dates = [
        'pushed_at',
        'deleted_at'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function owner()
    {
        return $this->hasOne(GithubUser::class, 'owner_id', 'id');
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
