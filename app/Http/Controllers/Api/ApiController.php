<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller; // must extend Laravel's base
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ApiController extends Controller
{
    use AuthorizesRequests;

    /**
     * Helper to check if a relationship should be included from the request.
     */
    public function include(string $relationship): bool
    {
        $param = request()->get('include');

        if (!$param) {
            return false;
        }

        $includeValues = explode(',', strtolower($param));
        return in_array(strtolower($relationship), $includeValues);
    }
}
