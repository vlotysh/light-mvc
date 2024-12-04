<?php

namespace LightMVC\Core;

class ErrorHandler
{
    public function __construct(
        private Logger $logger,
        private View $view,
        private bool $debug = false
    ) {
        set_error_handler([$this, 'handleError']);
        set_exception_handler([$this, 'handleException']);
    }

    public function handleError($level, $message, $file, $line)
    {
        $this->logger->error($message, [
            'level' => $level,
            'file' => $file,
            'line' => $line
        ]);

        if ($this->debug) {
            throw new \ErrorException($message, 0, $level, $file, $line);
        }

        $this->renderError('Произошла ошибка');
    }

    public function handleException(\Throwable $e)
    {
        $this->logger->error($e->getMessage(), [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);

        $message = $this->debug ? $e->getMessage() : 'Произошла ошибка';
        $this->renderError($message);
    }

    private function renderError($message)
    {
        http_response_code(500);
        echo $this->view->render('errors/500', ['message' => $message]);
        exit;
    }
}
