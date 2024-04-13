<?php

namespace Garaekz;

use Garaekz\Exceptions\ErrorHandler;
use Garaekz\Routing\Router;

/**
 * Represents the application class.
 */
class Application
{
    /**
     * The router instance.
     *
     * @var Router
     */
    public $router;

    /**
     * Initializes a new instance of the Application class.
     */
    public function __construct()
    {
        $this->registerErrorHandler();
        $this->router = new Router($this);
    }

    /**
     * Registers the error handler.
     */
    protected function registerErrorHandler()
    {
        ErrorHandler::register();
    }

    /**
     * Runs the application and dispatches the router.
     */
    public function run()
    {
        $this->router->run();
    }
}
