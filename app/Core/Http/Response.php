<?php

namespace LightMVC\Core\Http;

use Symfony\Component\HttpFoundation\Response as SymphonyResponse;

class Response extends SymphonyResponse
{
    protected $contentType = 'text/html';

    public function __construct($content = '', int $statusCode = 200, array $headers = [])
    {
        parent::__construct($content, $statusCode, $headers);

        $this->headers->add($this->getDefaultHeaders());

    }

    protected function getDefaultHeaders(): array
    {
        return [
            'Content-Type' => $this->contentType . '; charset=UTF-8'
        ];
    }

    public function removeHeader(string $name): self
    {
        unset($this->headers[$name]);
        return $this;
    }

    public function setContentType(string $contentType): self
    {
        $this->contentType = $contentType;
        $this->headers->set('Content-Type', $contentType . '; charset=UTF-8');
        return $this;
    }
}
