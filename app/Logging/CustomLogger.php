<?php

namespace App\Logging;

use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Config;

class CustomLogger
{
    protected $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function info($message, array $context = [])
    {
        $this->log('info', $message, $context);
    }

    public function error($message, array $context = [])
    {
        $this->log('error', $message, $context);
    }

    protected function log($level, $message, array $context)
    {
        $context = array_merge($context, [
            'user_id' => auth()->id() ?? 'guest',
            'ip' => request()->ip(),
            'url' => request()->fullUrl(),
            'method' => request()->method(),
        ]);

        $this->logger->log($level, $message, $context);
    }
}
