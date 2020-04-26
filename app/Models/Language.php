<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Language extends BaseModel
{

    use SoftDeletes;

    /**
     * @var string
     */
    protected $table = 'languages';

    /**
     * @var array
     */
    protected $fillable = [
      'name'
    ];

    /**
     * @var array
     */
    protected $dates = [
        'deleted_at'
    ];

}
