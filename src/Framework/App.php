<?php

declare(strict_types=1);

namespace Framework;

/**
 * Class App
 * 
 * The central class responsible for handling application logic
 */
class App
{
    // The router instance for routing requests
    private Router $router;

    // Thd dependency injection container for managing dependencies
    private Container $container;

    // Constructor for the App class
    // $containerDefinitionsPath: The path to the container definitions file, if provided.
    public function __construct(string $containerDefinitionsPath = null)
    {
        $this->router = new Router();
        $this->container = new Container();

        // If container definitions path is provided, load and add definitions to the container.
        if ($containerDefinitionsPath) {
            $containerDefinitions = include $containerDefinitionsPath;
            $this->container->addDefinitions($containerDefinitions);
        }
    }

    // Runs the application, dispatching the request to the appropriate controller
    public function run()
    {
        // Retrieve path and method form the server request
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];

        //Dispatch the request ot the router for handlning
        $this->router->dispatch($path, $method, $this->container);
    }

    // Register a GET route with the provided path and controller.
    // $path: The route path
    // $controller: An array that contains the controller class with the corresponding method
    public function get(string $path, array $controller)
    {
        $this->router->add('GET', $path, $controller);
    }
    // Register a POST route with the providie path and controller
    public function post(string $path, array $controller)
    {
        $this->router->add('POST', $path, $controller);
    }

    // Adds middleware to the application's router.
    // $middleware: The middleware to add.
    public function addMiddleware(string $middleware)
    {
        $this->router->addMiddleware($middleware);
    }
}
