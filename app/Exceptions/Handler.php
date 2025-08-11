<?php

namespace App\Exceptions;

use GuzzleHttp\Psr7\Request;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use NunoMaduro\Collision\Adapters\Laravel\ExceptionHandler;

class Handler
{
    protected function unauthenticated(Request $request, AuthenticationException $exception): JsonResponse
    {
        return response()->json(['message' => 'Non authentifié'], 401);
    }
}
