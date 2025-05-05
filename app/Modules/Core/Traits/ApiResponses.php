<?php

namespace App\Modules\Core\Traits;

use App\Exceptions\Handler;
use App\Modules\Core\Http\Controllers\ApiController;
use Exception;
use Illuminate\Http\JsonResponse;

trait ApiResponses
{
    public int $responseCode = 200;

    public string $message = 'OK';

    public string $title = 'Success';

    /**
     * @return Handler|ApiController|ApiResponses
     */
    public function setCode(int $code = 200): self
    {
        $this->responseCode = $code;

        return $this;
    }

    /**
     * @return Handler|ApiController|ApiResponses
     */
    public function setMessage($message): self
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return Handler|ApiController|ApiResponses
     */
    public function setTitle($title): self
    {
        $this->title = $title;

        return $this;
    }

    public function respond($data): JsonResponse
    {
        return response()
            ->json(
                [
                    'message' => $this->message,
                    'code' => $this->responseCode,
                    'data' => $data,
                ],
                $this->responseCode
            );
    }

    public function exceptionRespond(Exception $exception, array $data = [], string $title = 'Error'): JsonResponse
    {
        return response()->json(
            [
                'title' => $title,
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
            ],
            $exception->getCode()
        );
    }

    public function respondWithExceptionError(Exception $exception, string $title = 'Error'): JsonResponse
    {
        return response()
            ->json(
                [
                    'title' => $this->title,
                    'message' => $this->message,
                ],
                $exception->getCode()
            );
    }

    protected function errorResponse($message, $code): JsonResponse
    {
        return response()->json(['message' => $message, 'code' => $code], $code);
    }

    private function successResponse($data, $code): JsonResponse
    {
        return response()->json($data, $code);
    }
}
