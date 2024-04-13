<?php

namespace Garaekz;

use Garaekz\Routing\Router;

class Application {
    public $router;

    public function __construct() {
        $this->registerErrorHandler();
        $this->router = new Router($this);
    }

    protected function registerErrorHandler() {
        // TODO: Error handler robusto? Tal vez usar algo ya hecho
    }

    public function run() {
        $this->router->run();
    }
}
