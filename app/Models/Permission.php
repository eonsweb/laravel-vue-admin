<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Filters\BaseFilter;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    public function scopeFilter(Builder $builder, BaseFilter $filters)
    {
        return $filters->apply($builder);
    }
}
