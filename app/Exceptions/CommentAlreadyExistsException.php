<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class CommentAlreadyExistsException extends Exception
{
    public function __construct(string $message = 'Vous avez déjà commenté ce profil', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage()
        ], 422);
    }
}
