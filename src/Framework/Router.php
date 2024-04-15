<?php

declare(strict_types=1);

namespace Framework;

/**
 * The Router class responsible for routing requests to appropriate controllers
 */
class Router
{
  // Array to store the route definitions
  private array $routes = [];

  // Array to stote the middleware definitions
  private array $middlewares = [];

  // Adds a route with the provided method, path, and controller.
  public function add(string $method, string $path, array $controller)
  {
    // Normalize the path
    $path = $this->normalizePath($path);

    // Add route to the routes array
    $this->routes[] = [
      'path' => $path,
      'method' => strtoupper($method),
      'controller' => $controller
    ];
  }
  // Normalizes the given path by trimming leading/trailing slashes and esuring a leading and trailing slash.
  private function normalizePath(string $path): string
  {
    $path = trim($path, '/');
    $path = "/{$path}/";
    $path = preg_replace('#[/]{2,}#', '/', $path);

    return $path;
  }
  // Dispatches the request to the appropriate controller based on the requested path and method
  public function dispatch(string $path, string $method, Container $container)
  {
    // Normalize the path and method
    $path = $this->normalizePath($path);
    $method = strtoupper($method);

    // Iterate through routes to find a matching route
    foreach ($this->routes as $route) {
      // Check if the route's path matches the requested path
      if (
        !preg_match("#^{$route['path']}$#", $path) ||
        $route['method'] !== $method
      ) {
        continue;
      }

      // Extract the class and method from the controller array
      [$class, $function] = $route['controller'];

      // Create the controller with its dependencies if container is present
      $controllerInstance = $container ?
        $container->resolve($class) :  new $class;

      // Call the controller method
      $action = fn () => $controllerInstance->{$function}();


      foreach ($this->middlewares as $middleware) {
        $middlewareInstance = $container ? $container->resolve($middleware) : new $middleware();

        $action = fn () => $middlewareInstance->process($action);
      }
      $action();

      return;
    }
  }
  // Adda a middleware to the router.
  // $middleware: The middle ware to add.
  public function addMiddleware(string $middleware)
  {
    $this->middlewares[] = $middleware;
  }
}
