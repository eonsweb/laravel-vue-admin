<?php

namespace App\Exceptions;

use App\Traits\ApiResponses;
use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    use ApiResponses;

    public function report(Throwable $exception): void
    {
        parent::report($exception);
    }

    public function render($request, Throwable $exception)
    {
        // Example: handle user-related errors specifically
        if ($exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException &&
            $exception->getModel() === \App\Models\User::class) {
            return $this->error([
                'type'    => 'UserNotFound',
                'status'  => 0,
                'message' => 'The requested user does not exist.',
            ], 404);
        }

        return $this->error([
            'type'    => class_basename($exception),
            'status'  => 0,
            'message' => $exception->getMessage(),
            'source'  => 'Line: ' . $exception->getLine() . ' in ' . $exception->getFile(),
        ], 500);
    }
}
