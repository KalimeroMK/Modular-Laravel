<?php

declare(strict_types=1);

namespace App\Modules\Core\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GeneralException extends Exception
{
    /**
     * Any extra data to send with the response.
     *
     * @var array<string, mixed>
     */
    public $data = [];

    /**
     * @var int
     */
    protected $code = 500;

    /**
     * @var string
     */
    protected $message = 'Internal system error';

    protected string $logMessage = 'Internal system error';

    protected bool $log = true;

    /**
     * @var Exception|null
     */
    protected $exception = null;

    /**
     * GeneralException constructor.
     *
     * @param Exception|null $exception
     * @param array<string, mixed> $data
     */
    public function __construct(Exception|null $exception = null, array $data = [])
    {
        $this->setException($exception);
        $this->setData($data);

        parent::__construct((string) $this->message());
    }

    public function message(): ?string
    {
        return $this->message;
    }

    /**
     * @return Exception|null
     */
    public function getException(): Exception|null
    {
        return $this->exception;
    }

    /**
     * @param Exception|null $exception
     */
    public function setException(Exception|null $exception): void
    {
        $this->exception = $exception;
    }

    /**
     * @return array<string, mixed>
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Set the extra data to send with the response.
     *
     * @param array<string, mixed> $data
     * @return $this
     */
    public function setData(array $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function setCode(int $code): void
    {
        $this->code = $code;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    /**
     * @param \Illuminate\Http\Request $request
     */
    public function render(Request $request): JsonResponse
    {
        $this->isLog() ? $this->renderLog() : null;

        return $this->prepareResponse();
    }

    public function isLog(): bool
    {
        return $this->log;
    }

    public function setLog(bool $log): void
    {
        $this->log = $log;
    }

    /**
     * Log error
     */
    public function renderLog(): void
    {
        Log::error(print_r($this->getLogResponse(), true));
    }

    /**
     * @return array<string, mixed>
     */
    public function getLogResponse(): array
    {
        return [
            'message' => $this->getLogMessage(),
            'code' => $this->getCode(),
            'line' => $this->line(),
            'file' => $this->file(),
        ];
    }

    public function getLogMessage(): string
    {
        return $this->exception ? $this->exception->getMessage() : '';
    }

    public function setLogMessage(string $logMessage): void
    {
        $this->logMessage = $logMessage;
    }

    public function line(): int|string
    {
        return $this->exception ? $this->exception->getLine() : 'none';
    }

    public function file(): int|string
    {
        return $this->exception ? $this->exception->getFile() : 'none';
    }

    /**
     * @return array<string, mixed>
     */
    public function getResponse(): array
    {
        return [
            'code' => $this->getCode(),
            'message' => $this->message(),
        ];
    }

    /**
     * Handle an ajax response.
     */
    protected function prepareResponse(): JsonResponse
    {
        return response()->json($this->getResponse());
    }
}
