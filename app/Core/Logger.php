<?php

namespace LightMVC\Core;

class Logger
{
    private string $logFile;

    public function __construct(string $logFile = 'storage/logs/app.log')
    {
        $this->logFile = $logFile;
    }

    public function error($message, array $context = [])
    {
        $this->log('ERROR', $message, $context);
    }

    public function info($message, array $context = [])
    {
        $this->log('INFO', $message, $context);
    }

    private function log($level, $message, array $context)
    {
        $date = date('Y-m-d H:i:s');
        $contextJson = json_encode($context);
        $log = "[$date] $level: $message $contextJson\n";

        $path = storage_path('logs/app.log');

        file_put_contents($path, $log, FILE_APPEND);
    }
}
