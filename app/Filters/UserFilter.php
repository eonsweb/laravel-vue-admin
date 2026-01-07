<?php

namespace App\Filters;

use App\Filters\BaseFilter;

class UserFilter extends BaseFilter
{
    protected $allowedFilters = [
            'id' => '=',
            'name'    => 'like',
            'email'   => 'like',
            'username'   => 'like',
        ];
    protected $allowedSorts = [
            'id',
            'name',
            'email',
            'username',
            'created_at',
            'updated_at',
            'include',
        ];

        protected $searchableColumns = [
            'id',
            'name',
            'email',
            'username',
    ];
}
