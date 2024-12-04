<?php

namespace LightMVC\Core\Routing;

class Route
{
    private string $method;
    private string $uri;
    private $action;
    private array $parameters = [];
    private array $middleware = [];

    public function __construct(string $method, string $uri, $action)
    {
        $this->method = strtoupper($method);
        $this->uri = $uri;
        $this->action = $action;
    }

    /**
     * Check if route matches request
     */
    public function matches(string $method, string $uri): bool
    {
        if ($this->method !== $method) {
            return false;
        }

        $pattern = $this->getRoutePattern();
        return preg_match($pattern, $uri, $this->parameters);
    }

    /**
     * Get route pattern for matching
     */
    private function getRoutePattern(): string
    {
        return '#^' . preg_replace('/\{([a-zA-Z]+)\}/', '([^/]+)', $this->uri) . '$#';
    }

    /**
     * Get route parameters
     */
    public function getParameters(string $uri): array
    {
        preg_match($this->getRoutePattern(), $uri, $matches);
        array_shift($matches);
        return $matches;
    }

    /**
     * Get route action
     */
    public function getAction(): string|callable
    {
        return $this->action;
    }

    /**
     * Add middleware to route
     */
    public function middleware(string|array $middleware): self
    {
        $this->middleware = array_merge(
            $this->middleware,
            (array) $middleware
        );
        return $this;
    }

    /**
     * Add group attributes to route
     */
    public function addGroupAttributes(array $attributes): void
    {
        if (isset($attributes['middleware'])) {
            $this->middleware((array) $attributes['middleware']);
        }
    }
}
