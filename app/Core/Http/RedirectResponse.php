<?php

namespace LightMVC\Core\Http;

class RedirectResponse extends Response
{
    public function __construct(string $url, int $statusCode = 302, array $headers = [])
    {
        parent::__construct('', $statusCode, $headers);
        $this->addHeader('Location', $url);
    }

    public function with(string $key, $value): self
    {
        Session::flash($key, $value);
        return $this;
    }
}
