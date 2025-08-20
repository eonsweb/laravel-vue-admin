<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;
use Illuminate\Database\Eloquent\Builder;
use App\Filters\BaseFilter;

class Role extends SpatieRole
{
    public function scopeFilter(Builder $builder, BaseFilter $filters)
    {
        return $filters->apply($builder);
    }
}
