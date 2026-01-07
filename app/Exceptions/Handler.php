<?php

namespace App\Exceptions;

use App\Traits\ApiResponses;

use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\QueryException;
use BadMethodCallException;
use TypeError;

use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    use ApiResponses;

    protected $handlers = [
        ValidationException::class => 'handleValidation',
        ModelNotFoundException::class => 'handleModelNotFound',
        AuthenticationException::class => 'handleAuthentication',
         AuthorizationException::class => 'handleAuthorization',
         BadMethodCallException::class      => 'handleBadMethodCall',
        TypeError::class                   => 'handleTypeError',
         QueryException::class => 'handleQuery',
    ];

    private function handleValidation(ValidationException $exception):array
    {
        $errors = [];
        foreach($exception->errors() as $field => $messages){
            foreach($messages as $message){
                $errors[] = [
                    'status' => 422,
                    'field' => $field,
                    'message' => $message
                ];
            }
        }
        return [
            'type' => 'ValidationException',
            'status' => 422,
            'errors' => $errors
        ];
    }

    private function handleModelNotFound(ModelNotFoundException $exception)
    {
        return [
            [
                'status' => 404,
                'message' => 'The resource cannot be found',
                'source' => $exception->getModel()
            ]
            ];
    }
    private function handleAuthentication(AuthenticationException $exception)
    {
         return [
            'type'   => 'AuthenticationException',
            'status' => 401,
            'errors' => [
                [
                    'message' => 'You are not authenticated.',
                    'source'  => 'auth',
                ]
            ],
        ];
    }

    private function handleAuthorization(AuthorizationException $exception): array
    {
        return [
            'type'    => 'AuthorizationException',
            'status'  => 403,
            'message' => $exception->getMessage() ?: 'You are not authorized to perform this action.',
            'source'  => $exception->getFile() . ':' . $exception->getLine(),
        ];
    }

    private function handleBadMethodCall(BadMethodCallException $exception): array
    {
        return [
            'type'    => 'BadMethodCallException',
            'status'  => 500,
            'message' => 'You tried to call a method that does not exist: ' . $exception->getMessage(),
            'source'  => $exception->getFile() . ':' . $exception->getLine(),
        ];
    }

    private function handleTypeError(TypeError $exception): array
    {
        return [
            'type'    => 'TypeError',
            'status'  => 500,
            'message' => $exception->getMessage(), // usually "Argument #1 ($foo) must be of type..."
            'source'  => $exception->getFile() . ':' . $exception->getLine(),
        ];
    }


    private function handleQuery(QueryException $exception): array
    {
        return [
            'type'    => 'QueryException',
            'status'  => 500,
            'message' => 'A database error occurred. Please try again later.',
            // Optional: only show details in local/dev mode
            'debug'   => app()->isLocal() ? $exception->getMessage() : null,
            'source'  => $exception->getFile() . ':' . $exception->getLine(),
        ];
    }



    public function report(Throwable $exception): void
    {
        parent::report($exception);
    }

    private function formatSource(Throwable $exception): ?string
    {
        if (app()->isLocal() || config('app.debug')) {
            return $exception->getFile() . ':' . $exception->getLine();
        }

        return null;
    }


    public function render($request, Throwable $exception)
    {
        $className = get_class($exception);

        if(array_key_exists($className,$this->handlers)){
            $method = $this->handlers[$className];
            return $this->error($this->$method($exception));
        }

        return $this->error([
            'type'    => class_basename($exception),
            'status'  => 0,
            'message' => $exception->getMessage(),
            'source' =>  $this->formatSource($exception),
        ], 500);
    }
}
