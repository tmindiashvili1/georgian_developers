<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property mixed last_modified
 */
class GithubUser extends BaseModel
{

    use SoftDeletes;

    /**
     * @var string
     */
    protected $table = 'github_users';

    /**
     * @var array
     */
    protected $fillable = [
        'login',
        'github_user_id',
        'node_id',

        'avatar_url',
        'name',
        'company',
        'website',
        'location',
        'email',
        'hireable',
        'bio',
        'followers',
        'following',

        // Last modified.
        'etag',
        'last_modified',

        // Date
        'user_info_update_at'
    ];

    /**
     * @var array
     */
    protected $dates = [
        'deleted_at', 'user_info_update_at'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function repos()
    {
        return $this->hasMany(GithubUserRepo::class, 'id', 'owner_id');
    }

}
