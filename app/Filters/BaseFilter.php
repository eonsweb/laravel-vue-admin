<?php

namespace App\Filters;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

abstract Class BaseFilter
{
    protected $builder;
    protected $request;
    protected $allowedFilters = [];
    protected $allowedSorts = [];
    protected $searchableColumns = [];

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply(Builder $builder)
    {
        $this->builder = $builder;

        foreach($this->allowedFilters as $param => $operator)
        {
            if($this->request->filled($param))
            {
                $this->applyFilter($param,$operator,$this->request->input($param));
            }
        }

         // Apply sorting after filters
        $this->sort();

        return $builder;
    }

    protected function applyFilter(string $param, string $operator, $value): void
    {
        switch ($operator) {
            case 'like':
                $this->builder->where($param, 'LIKE', "%{$value}%");
                break;

            case '=':
                $this->builder->where($param, '=', $value);
                break;

            case 'date':
                if (is_string($value) && strtolower($value) === 'null') {
                    $this->builder->whereNull($param);
                } else {
                    $this->builder->whereDate($param, $value);
                }
                break;

            case 'in':
                $values = is_array($value) ? $value : explode(',',$value);
                $this->builder->whereIn($param,$values);
                break;


            case 'relation':
                $this->builder->with(explode(',', $value)); // support comma-separated includes
                break;

            case 'search':
                if (!empty($this->searchableColumns)) {
                    $this->builder->whereAny(
                        $this->searchableColumns,
                        'LIKE',
                        "%{$value}%"
                    );
                }
                break;

        }
    }

    public function sort()
    {

        $sort = $this->request->input('sort');

        if (!$sort) {
            return;
        }

        $sortAttributes = explode(',',$sort);

        foreach($sortAttributes as $sortAttribute)
        {
            $direction = 'asc';
            $sortAttribute = trim($sortAttribute);

            if(str_starts_with($sortAttribute,'-'))
            {
                $direction = 'desc';
                $sortAttribute = substr($sortAttribute,1);
            }

            if(in_array($sortAttribute,$this->allowedSorts,true)){
                $this->builder->orderBy($sortAttribute,$direction);
            }
        }
    }

}
