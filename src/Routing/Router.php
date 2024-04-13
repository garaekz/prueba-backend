<?php

namespace Garaekz\Routing;

class Router {
    protected $routes = [];
    protected $groupStack = [];
    protected $app;

    public function __construct($app) {
        $this->app = $app;
    }

    public function group($attributes, $callback) {
        $this->updateGroupStack($attributes);
        call_user_func($callback, $this);
        array_pop($this->groupStack);
    }

    protected function updateGroupStack($attributes) {
        $this->groupStack[] = $attributes;
    }

    protected function prefix($uri) {
        $prefix = end($this->groupStack)['prefix'] ?? '';
        return rtrim($prefix, '/') . '/' . ltrim($uri, '/');
    }

    protected function resolveAction($action) {
        $namespace = end($this->groupStack)['namespace'] ?? '';
        return $namespace . '\\' . $action;
    }

    public function addRoute($method, $uri, $action) {
        $uri = $this->prefix($uri);
        $action = $this->resolveAction($action);
        $this->routes[] = ['method' => strtoupper($method), 'uri' => $uri, 'action' => $action];
    }

    public function get($uri, $action) {
        $this->addRoute('GET', $uri, $action);
    }

    public function post($uri, $action) {
        $this->addRoute('POST', $uri, $action);
    }

    public function put($uri, $action) {
        $this->addRoute('PUT', $uri, $action);
    }

    public function patch($uri, $action) {
        $this->addRoute('PATCH', $uri, $action);
    }

    public function delete($uri, $action) {
        $this->addRoute('DELETE', $uri, $action);
    }

    public function run() {
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        
        foreach ($this->routes as $route) {
            if ($this->matchRoute($route, $requestUri, $requestMethod)) {
                $this->handleRoute($route);
                return;
            }
        }
    
        // Si no se encuentra ninguna ruta que coincida, enviar una respuesta 404
        header("HTTP/1.1 404 Not Found");
        echo "404 Not Found";
    }
    
    protected function matchRoute($route, $requestUri, $requestMethod) {
        return $route['uri'] === $requestUri && $route['method'] === $requestMethod;
    }
    
    protected function handleRoute($route) {
        if (!empty($route['middleware'])) {
            foreach ($route['middleware'] as $middleware) {
                $middlewareInstance = new $middleware();
                if (!$middlewareInstance->handle()) {
                    return; // Si el middleware falla, detener la ejecución.
                }
            }
        }
    
        list($controllerName, $method) = explode('@', $route['action']);
        if (class_exists($controllerName) && method_exists($controllerName, $method)) {
            $controllerInstance = new $controllerName();
            $controllerInstance->$method();
        } else {
            // Controlador o método no existente
            header("HTTP/1.1 500 Internal Server Error");
            echo "500 Internal Server Error: Controller or method not found.";
        }
    }
    
}
