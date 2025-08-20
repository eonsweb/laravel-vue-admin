<?php

namespace App\Filters;

class RoleFilter extends BaseFilter
{
     protected $allowedFilters = [
            'id' => '=',
            'name'    => 'like',

        ];
    protected $allowedSorts = [
            'id',
            'name',

        ];

        protected $searchableColumns = [
            'id',
            'name',

    ];
}
