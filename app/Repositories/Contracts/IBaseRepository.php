<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Model;

interface IBaseRepository
{


    /**
     * @param $method
     * @param $parameters
     * @return mixed
     */
    public function __call($method, $parameters);

}
