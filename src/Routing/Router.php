<?php

namespace Garaekz\Routing;

class Router
{
    protected $routes = [];
    protected $groupStack = [];
    protected $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function group($attributes, $callback)
    {
        $this->updateGroupStack($attributes);
        call_user_func($callback, $this);
        array_pop($this->groupStack);
    }

    protected function updateGroupStack($attributes)
    {
        $this->groupStack[] = $attributes;
    }

    protected function prefix($uri)
    {
        $prefix = end($this->groupStack)['prefix'] ?? '';
        return rtrim($prefix, '/') . '/' . ltrim($uri, '/');
    }

    protected function resolveAction($action)
    {
        $namespace = end($this->groupStack)['namespace'] ?? '';
        return $namespace . '\\' . $action;
    }

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


    public function get($uri, $action)
    {
        $this->addRoute('GET', $uri, $action);
    }

    public function post($uri, $action)
    {
        $this->addRoute('POST', $uri, $action);
    }

    public function put($uri, $action)
    {
        $this->addRoute('PUT', $uri, $action);
    }

    public function patch($uri, $action)
    {
        $this->addRoute('PATCH', $uri, $action);
    }

    public function delete($uri, $action)
    {
        $this->addRoute('DELETE', $uri, $action);
    }

    public function run()
    {
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $requestMethod = $_SERVER['REQUEST_METHOD'];

        foreach ($this->routes as $route) {
            $parameters = $this->matchRoute($route, $requestUri, $requestMethod);
            if ($parameters !== false) { // significa que hay una coincidencia
                $this->handleRoute($route, $parameters);
                return;
            }
        }

        // Si no se encuentra ninguna ruta que coincida, enviar una respuesta 404
        header("HTTP/1.1 404 Not Found");
        echo "404 Not Found";
    }

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
                // Es un parámetro, captura el valor del parámetro de la URI
                $parameters[$matches[1]] = $uriParts[$index];
            } elseif ($part !== $uriParts[$index]) {
                return false;
            }
        }

        return $parameters;
    }

    protected function handleRoute($route)
    {
        $parameters = $this->matchRoute($route, parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), $_SERVER['REQUEST_METHOD']);

        if ($parameters === false) {
            return; // No hubo coincidencia en la ruta
        }

        // Ejecutar middlewares si los hay
        if (!empty($route['middleware'])) {
            foreach ($route['middleware'] as $middleware) {
                $middlewareInstance = new $middleware();
                if (!$middlewareInstance->handle()) {
                    return; // Si el middleware falla, detener la ejecución.
                }
            }
        }

        // Llamar al callback o al método del controlador
        $action = $route['action'];
        if (is_callable($action)) {
            call_user_func_array($action, $parameters);
        } else {
            list($controllerName, $method) = explode('@', $action);
            if (class_exists($controllerName) && method_exists($controllerName, $method)) {
                $controllerInstance = new $controllerName();
                call_user_func_array([$controllerInstance, $method], $parameters);
            } else {
                // Controlador o método no existente
                header("HTTP/1.1 500 Internal Server Error");
                echo "500 Internal Server Error: Controller or method not found.";
            }
        }
    }
}
