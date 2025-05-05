<?php

namespace App\Modules\Core\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class GeneralException extends Exception
{
    /**
     * Any extra data to send with the response.
     *
     * @var array
     */
    public $data = [];

    protected $code = 500;

    protected $message = 'Internal system error';

    protected string $logMessage = 'Internal system error';

    protected bool $log = true;

    protected null $exception = null;

    /**
     * GeneralException constructor.
     *
     * @param  array  $data
     */
    public function __construct(?Exception $exception = null, $data = [])
    {
        $this->setException($exception);
        $this->setData($data);

        parent::__construct($this->message());
    }

    public function message(): ?string
    {
        return $this->message;
    }

    public function getException(): null
    {
        return $this->exception;
    }

    public function setException(null $exception): void
    {
        $this->exception = $exception;
    }

    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Set the extra data to send with the response.
     *
     *
     * @return $this
     */
    public function setData(array $data): GeneralException
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

    public function render($request): JsonResponse
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
     * Handle an ajax response.
     */
    protected function prepareResponse(): JsonResponse
    {
        return response()->json($this->getResponse());
    }

    public function getResponse(): array
    {
        return [
            'code' => $this->getCode(),
            'message' => $this->message(),
        ];
    }
}
