<?php

namespace Garaekz\Routing;

/**
 * Class Router
 * 
 * The Router class handles routing and request handling for the application.
 */
class Router
{
    protected $routes = [];
    protected $groupStack = [];
    protected $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * Add a route group with shared attributes.
     *
     * @param array $attributes The attributes for the route group.
     * @param callable $callback The callback function to define routes within the group.
     * @return void
     */
    public function group($attributes, $callback)
    {
        $this->updateGroupStack($attributes);
        call_user_func($callback, $this);
        array_pop($this->groupStack);
    }

    /**
     * Update the group stack with the given attributes.
     *
     * @param array $attributes The attributes to update the group stack.
     * @return void
     */
    protected function updateGroupStack($attributes)
    {
        $this->groupStack[] = $attributes;
    }

    /**
     * Add a prefix to the given URI.
     *
     * @param string $uri The URI to add the prefix to.
     * @return string The modified URI with the prefix.
     */
    protected function prefix($uri)
    {
        $prefix = end($this->groupStack)['prefix'] ?? '';
        return rtrim($prefix, '/') . '/' . ltrim($uri, '/');
    }

    /**
     * Resolve the action by adding the namespace to the given action.
     *
     * @param string $action The action to resolve.
     * @return string The resolved action with the namespace.
     */
    protected function resolveAction($action)
    {
        $namespace = end($this->groupStack)['namespace'] ?? '';
        return $namespace . '\\' . $action;
    }

    /**
     * Add a route to the routes array.
     *
     * @param string $method The HTTP method of the route.
     * @param string $uri The URI of the route.
     * @param mixed $action The action of the route.
     * @return void
     */
    protected function addRoute($method, $uri, $action)
    {
        $uri = $this->prefix($uri);

        if (!is_callable($action) && is_string($action)) {
            $action = $this->resolveAction($action);
        }

        $this->routes[] = [
            'method' => $method,
            'uri' => $uri,
            'action' => $action
        ];
    }

    /**
     * Add a GET route.
     *
     * @param string $uri The URI of the route.
     * @param mixed $action The action of the route.
     * @return void
     */
    public function get($uri, $action)
    {
        $this->addRoute('GET', $uri, $action);
    }

    /**
     * Add a POST route.
     *
     * @param string $uri The URI of the route.
     * @param mixed $action The action of the route.
     * @return void
     */
    public function post($uri, $action)
    {
        $this->addRoute('POST', $uri, $action);
    }

    /**
     * Add a PUT route.
     *
     * @param string $uri The URI of the route.
     * @param mixed $action The action of the route.
     * @return void
     */
    public function put($uri, $action)
    {
        $this->addRoute('PUT', $uri, $action);
    }

    /**
     * Add a PATCH route.
     *
     * @param string $uri The URI of the route.
     * @param mixed $action The action of the route.
     * @return void
     */
    public function patch($uri, $action)
    {
        $this->addRoute('PATCH', $uri, $action);
    }

    /**
     * Add a DELETE route.
     *
     * @param string $uri The URI of the route.
     * @param mixed $action The action of the route.
     * @return void
     */
    public function delete($uri, $action)
    {
        $this->addRoute('DELETE', $uri, $action);
    }

    /**
     * Run the router and handle the current request.
     *
     * @return void
     */
    public function run()
    {
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $requestMethod = $_SERVER['REQUEST_METHOD'];

        foreach ($this->routes as $route) {
            $parameters = $this->matchRoute($route, $requestUri, $requestMethod);
            if ($parameters !== false) {
                $this->handleRoute($route, $parameters);
                return;
            }
        }

        header("HTTP/1.1 404 Not Found");
        echo "404 Not Found";
    }

    /**
     * Match the route with the current request URI and method.
     *
     * @param array $route The route to match.
     * @param string $requestUri The current request URI.
     * @param string $requestMethod The current request method.
     * @return array|false The matched route parameters or false if no match.
     */
    protected function matchRoute($route, $requestUri, $requestMethod)
    {
        if ($route['method'] !== $requestMethod) {
            return false;
        }

        $routeParts = explode('/', trim($route['uri'], '/'));
        $uriParts = explode('/', trim($requestUri, '/'));

        if (count($routeParts) !== count($uriParts)) {
            return false;
        }

        $parameters = [];

        foreach ($routeParts as $index => $part) {
            if (preg_match('/^\{(.+)\}$/', $part, $matches)) {
                $parameters[$matches[1]] = $uriParts[$index];
            } elseif ($part !== $uriParts[$index]) {
                return false;
            }
        }

        return $parameters;
    }

    /**
     * Handle the matched route.
     *
     * @param array $route The matched route.
     * @return void
     */
    public function handleRoute($route)
    {
        $parameters = $this->matchRoute($route, parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), $_SERVER['REQUEST_METHOD']);

        if ($parameters === false) {
            return;
        }

        if (!empty($route['middleware'])) {
            foreach ($route['middleware'] as $middleware) {
                $middlewareInstance = new $middleware();
                if (!$middlewareInstance->handle()) {
                    return;
                }
            }
        }

        $action = $route['action'];
        if (is_callable($action)) {
            call_user_func_array($action, $parameters);
        } else {
            list($controllerName, $method) = explode('@', $action);
            if (class_exists($controllerName) && method_exists($controllerName, $method)) {
                $controllerInstance = new $controllerName();
                $dependencies = DependencyResolver::resolveMethodDependencies($controllerInstance, $method, $parameters);
                call_user_func_array([$controllerInstance, $method], $dependencies);
            } else {
                header("HTTP/1.1 500 Internal Server Error");
                echo "500 Internal Server Error: Controller or method not found.";
            }
        }
    }
}
