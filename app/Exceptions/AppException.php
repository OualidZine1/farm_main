<?php

namespace App\Exceptions;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class AppException extends \Exception
{
    public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Report the exception
     */
    public function report(): void
    {
        Log::error($this->getMessage(), [
            'code' => $this->getCode(),
            'user' => Auth::id() ?? 'guest',
            'ip' => request()->ip(),
            'file' => $this->getFile(),
            'line' => $this->getLine(),
        ]);
    }

    /**
     * Render the exception as an HTTP response
     */
    public function render()
    {
        if (request()->expectsJson()) {
            return response()->json([
                'error' => $this->getMessage(),
                'code' => $this->getCode(),
            ], $this->getCode() ?: 500);
        }

        return response()->view('errors.custom', [
            'message' => $this->getMessage(),
            'code' => $this->getCode(),
        ], $this->getCode() ?: 500);
    }
}
