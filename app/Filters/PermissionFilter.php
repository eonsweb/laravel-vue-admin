<?php

namespace App\Filters;

class PermissionFilter extends BaseFilter
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
