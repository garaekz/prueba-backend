<?php

namespace Garaekz;

use Garaekz\Exceptions\ErrorHandler;
use Garaekz\Routing\Router;

class Application
{
    public $router;

    public function __construct()
    {
        $this->registerErrorHandler();
        $this->router = new Router($this);
    }

    protected function registerErrorHandler()
    {
        ErrorHandler::register();
    }

    public function run()
    {
        $this->router->run();
    }
}
