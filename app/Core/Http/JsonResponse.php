<?php

namespace LightMVC\Core\Http;

class JsonResponse extends Response
{
    public function __construct($data = null, int $statusCode = 200, array $headers = [])
    {
        $content = $data !== null ? json_encode($data, JSON_UNESCAPED_UNICODE) : null;

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('Failed to encode data as JSON: ' . json_last_error_msg());
        }

        parent::__construct($content, $statusCode, $headers);
        $this->setContentType('application/json');
        $this->headers->set('Content-Length', strlen($this->content));

    }
}
