<?php

declare(strict_types=1);

namespace App\Modules\Core\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;






abstract class BaseException extends Exception
{
    


    protected int $statusCode = 500;

    


    protected string $errorCode = 'INTERNAL_ERROR';

    




    final public function render(): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'error_code' => $this->errorCode,
            'message' => $this->getMessage(),
            'errors' => $this->getErrors(),
        ], $this->statusCode);
    }

    


    final public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    


    final public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    




    protected function getErrors(): array
    {
        return [];
    }
}
