<?php

namespace LightMVC\Core\Routing;

use LightMVC\Core\Http\Request;
use LightMVC\Core\Http\Response;
use LightMVC\Core\Container\Container;

class Router
{
    private static ?Router $instance = null;
    private array $routes = [];
    private array $patterns = [
        'int' => '\d+',
        'string' => '[a-zA-Z]+',
        'slug' => '[a-zA-Z0-9-]+',
        'any' => '[^/]+'
    ];

    private string $namespace = 'LightMVC\Controllers';

    private Container $container;
    private array $currentGroupAttributes = [];

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Add a GET route
     */
    public function get(string $uri, string|callable $action): Route
    {
        return $this->addRoute('GET', $uri, $action);
    }

    /**
     * Add a POST route
     */
    public function post(string $uri, string|callable $action): Route
    {
        return $this->addRoute('POST', $uri, $action);
    }

    /**
     * Add a PUT route
     */
    public function put(string $uri, string|callable $action): Route
    {
        return $this->addRoute('PUT', $uri, $action);
    }

    /**
     * Add a DELETE route
     */
    public function delete(string $uri, string|callable $action): Route
    {
        return $this->addRoute('DELETE', $uri, $action);
    }

    /**
     * Add a route to the router
     */
    private function addRoute(string $method, string $uri, string|callable $action): Route
    {
        $uri = $this->prefix($uri);
        $route = new Route($method, $uri, $action);

        if (!empty($this->currentGroupAttributes)) {
            $route->addGroupAttributes($this->currentGroupAttributes);
        }

        $this->routes[] = $route;
        return $route;
    }

    /**
     * Add prefix to URI based on current group
     */
    private function prefix(string $uri): string
    {
        if (isset($this->currentGroupAttributes['prefix'])) {
            return '/' . trim($this->currentGroupAttributes['prefix'], '/') . '/' . trim($uri, '/');
        }
        return '/' . trim($uri, '/');
    }

    /**
     * Create a route group with shared attributes
     */
    public function group(array $attributes, callable $callback): void
    {
        $previousGroupAttributes = $this->currentGroupAttributes;

        $this->currentGroupAttributes = $this->mergeGroupAttributes(
            $previousGroupAttributes,
            $attributes
        );

        $callback($this);

        $this->currentGroupAttributes = $previousGroupAttributes;
    }

    public function namespace(string $namespace)
    {
        $this->namespace = $namespace;
    }

    /**
     * Merge group attributes
     */
    private function mergeGroupAttributes(array $previous, array $new): array
    {
        if (isset($new['prefix'])) {
            $new['prefix'] = isset($previous['prefix'])
                ? $previous['prefix'] . '/' . trim($new['prefix'], '/')
                : trim($new['prefix'], '/');
        }

        if (isset($new['middleware'])) {
            $new['middleware'] = isset($previous['middleware'])
                ? array_merge((array) $previous['middleware'], (array) $new['middleware'])
                : (array) $new['middleware'];
        }

        return array_merge($previous, $new);
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self(Container::getInstance());
        }
        return self::$instance;
    }

    /**
     * Dispatch the request to the appropriate route handler
     */
    public function dispatch(Request $request): Response
    {
        $method = $request->getMethod();
        $uri = $request->getRequestUri();

        foreach ($this->routes as $route) {
            if ($route->matches($method, $uri)) {
                return $this->handleRoute($route, $request);
            }
        }

        throw new \Exception('Route not found', 404);
    }

    /**
     * Handle the matched route
     */
    private function handleRoute(Route $route, Request $request): Response
    {
        $action = $route->getAction();
        $parameters = $route->getParameters($request->getUri());

        // If action is a closure
        if ($action instanceof \Closure) {
            return $action($request, ...$parameters);
        }

        // If action is Controller@method
        [$controller, $method] = explode('@', $action);

        $controller = $this->namespace . '\\' . $controller;

        if (!class_exists($controller)) {
            throw new \Exception("Controller {$controller} not found");
        }

        $controllerInstance = $this->container->make($controller);

        if (!method_exists($controllerInstance, $method)) {
            throw new \Exception("Method {$method} not found in controller {$controller}");
        }

        return $controllerInstance->$method($request, ...$parameters);
    }
}
