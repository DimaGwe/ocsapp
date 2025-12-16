<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

/**
 * Front Controller
 * All requests are routed through this file
 */
// Start output buffering
ob_start();

// Define base path
define('BASE_PATH', dirname(__DIR__));

// Load Composer autoloader
require BASE_PATH . '/vendor/autoload.php';

// Load configuration
$config = require BASE_PATH . '/config/app.php';

// Load database configuration
require BASE_PATH . '/config/database.php';

// Error handler for production
if (!$config['debug']) {
    set_error_handler(function($errno, $errstr, $errfile, $errline) {
        error_log("Error [{$errno}]: {$errstr} in {$errfile} on line {$errline}");
        return false;
    });
}

// Simple Router
class Router {
    private array $routes = [];
    
    public function __construct(array $routes) {
        $this->routes = $routes;
    }
    
    public function dispatch(): void {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Remove base path if exists
        $basePath = '/marketplace/public';
        if (strpos($uri, $basePath) === 0) {
            $uri = substr($uri, strlen($basePath));
        }
        
        // Ensure leading slash
        $uri = '/' . ltrim($uri, '/');
        
        // Remove trailing slash (except for root)
        if ($uri !== '/' && substr($uri, -1) === '/') {
            $uri = rtrim($uri, '/');
        }
        
        $routeKey = "{$method} {$uri}";

        // Debug logging
        if (env('APP_DEBUG') === 'true') {
            error_log("Route Request: {$routeKey}");
        }

        // Find matching route
        if (isset($this->routes[$routeKey])) {
            $this->executeRoute($this->routes[$routeKey]);
            return;
        }
        
        // Try dynamic routes (basic pattern matching)
        foreach ($this->routes as $route => $handler) {
            list($routeMethod, $routePath) = explode(' ', $route, 2);
            
            if ($routeMethod !== $method) {
                continue;
            }
            
            // Convert route pattern to regex
            $pattern = preg_replace('/\{[a-zA-Z0-9_]+\}/', '([^/]+)', $routePath);
            $pattern = '#^' . $pattern . '$#';
            
            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches); // Remove full match
                $this->executeRoute($handler, $matches);
                return;
            }
        }
        
        // 404 Not Found
        $this->notFound();
    }
    
    private function executeRoute($handler, array $params = []): void {
        try {
            if (is_callable($handler)) {
                // Closure/function
                call_user_func_array($handler, $params);
            } elseif (is_array($handler)) {
                // Controller@method
                list($controller, $method) = $handler;
                $controllerClass = "App\\Controllers\\" . $controller;

                if (!class_exists($controllerClass)) {
                    throw new Exception("Controller not found: {$controllerClass}");
                }

                $instance = new $controllerClass();

                if (!method_exists($instance, $method)) {
                    throw new Exception("Method not found: {$controllerClass}@{$method}");
                }

                call_user_func_array([$instance, $method], $params);
            } else {
                throw new Exception("Invalid route handler");
            }
        } catch (Exception $e) {
            error_log("Route execution error: " . $e->getMessage());
            
            if (env('APP_DEBUG') === 'true') {
                echo "<h1>Error</h1>";
                echo "<p>" . $e->getMessage() . "</p>";
                echo "<pre>" . $e->getTraceAsString() . "</pre>";
            } else {
                echo "<h1>500 - Internal Server Error</h1>";
            }
        }
    }
    
    private function notFound(): void {
        http_response_code(404);
        echo "<!DOCTYPE html>
        <html>
        <head>
            <title>404 - Not Found</title>
            <script src='https://cdn.tailwindcss.com'></script>
        </head>
        <body class='bg-gray-100 flex items-center justify-center min-h-screen'>
            <div class='text-center'>
                <h1 class='text-6xl font-bold text-gray-800 mb-4'>404</h1>
                <p class='text-xl text-gray-600 mb-8'>Page Not Found</p>
                <a href='" . url('/') . "' class='bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition inline-block'>
                    Go Home
                </a>
            </div>
        </body>
        </html>";
    }
}

// Load routes
$routes = require BASE_PATH . '/routes/web.php';

// Create and dispatch router
$router = new Router($routes);
$router->dispatch();

// Flush output buffer
ob_end_flush();