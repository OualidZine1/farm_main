<?php

namespace App\Exceptions;

class AppException extends \Exception
{
    public function __construct($message = "", $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function report()
    {
        \Log::error($this->getMessage(), [
            'code' => $this->getCode(),
            'user' => \Auth::id() ?? 'guest',
            'ip' => request()->ip(),
        ]);
    }

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
