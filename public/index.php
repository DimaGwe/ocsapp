<?php
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

// Load bootstrap (helper functions and environment)
require BASE_PATH . '/bootstrap/init.php';

// Load configuration
$config = require BASE_PATH . '/config/app.php';

// Load database configuration
require BASE_PATH . '/config/database.php';

// Configure error display based on environment
if ($config['debug']) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    ini_set('log_errors', 1);
    // Write PHP errors to our daily app log instead of the system default
    ini_set('error_log', BASE_PATH . '/storage/logs/' . date('Y-m-d') . '.log');
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

        // HEAD is identical to GET but without a response body — treat as GET for routing
        if ($method === 'HEAD') {
            $method = 'GET';
        }

        // Handle CORS preflight for API routes (mobile app)
        if ($method === 'OPTIONS') {
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
            header('Access-Control-Allow-Headers: Authorization, Content-Type, Accept');
            header('Access-Control-Max-Age: 86400');
            http_response_code(204);
            exit;
        }

        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';

        // Remove base path if exists (only if basePath is not empty)
        $basePath = env('BASE_PATH', '');
        if (!empty($basePath) && strpos($uri, $basePath) === 0) {
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
            
            if (env('APP_DEBUG', false)) {
                echo "<h1>Error</h1>";
                echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
                echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
            } else {
                http_response_code(500);
                echo "<h1>500 - Internal Server Error</h1>";
                echo "<p>Something went wrong. Please try again later.</p>";
            }
        }
    }
    
    private function notFound(): void {
        http_response_code(404);
        echo "<!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>404 - Page Not Found | OCS Marketplace</title>
            <link rel='preconnect' href='https://fonts.googleapis.com'>
            <link rel='preconnect' href='https://fonts.gstatic.com' crossorigin>
            <link href='https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap' rel='stylesheet'>
            <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css'>
            <style>
                :root {
                    --primary: #00b207;
                    --primary-600: #009206;
                    --dark: #1a1a1a;
                    --gray-50: #fafafa;
                    --gray-100: #f5f5f5;
                    --gray-500: #6b7280;
                    --gray-600: #4b5563;
                    --gray-700: #374151;
                }
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }
                body {
                    font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
                    background: linear-gradient(135deg, #00b207 0%, #007a05 100%);
                    min-height: 100vh;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    padding: 20px;
                }
                .container {
                    background: white;
                    border-radius: 24px;
                    padding: 60px 40px;
                    max-width: 600px;
                    width: 100%;
                    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
                    text-align: center;
                }
                .error-code {
                    font-size: 120px;
                    font-weight: 800;
                    color: var(--primary);
                    line-height: 1;
                    margin-bottom: 20px;
                    text-shadow: 2px 2px 0 rgba(0, 178, 7, 0.1);
                }
                .error-icon {
                    font-size: 80px;
                    color: var(--primary);
                    margin-bottom: 24px;
                    animation: bounce 2s infinite;
                }
                @keyframes bounce {
                    0%, 100% { transform: translateY(0); }
                    50% { transform: translateY(-10px); }
                }
                h1 {
                    font-size: 32px;
                    font-weight: 700;
                    color: var(--dark);
                    margin-bottom: 16px;
                }
                p {
                    font-size: 16px;
                    color: var(--gray-600);
                    margin-bottom: 40px;
                    line-height: 1.6;
                }
                .button-group {
                    display: flex;
                    gap: 16px;
                    justify-content: center;
                    flex-wrap: wrap;
                }
                .btn {
                    display: inline-flex;
                    align-items: center;
                    gap: 8px;
                    padding: 14px 28px;
                    border-radius: 12px;
                    font-size: 16px;
                    font-weight: 600;
                    text-decoration: none;
                    transition: all 0.3s ease;
                    border: none;
                    cursor: pointer;
                }
                .btn-primary {
                    background: var(--primary);
                    color: white;
                    box-shadow: 0 4px 14px rgba(0, 178, 7, 0.3);
                }
                .btn-primary:hover {
                    background: var(--primary-600);
                    transform: translateY(-2px);
                    box-shadow: 0 6px 20px rgba(0, 178, 7, 0.4);
                }
                .btn-secondary {
                    background: var(--gray-100);
                    color: var(--gray-700);
                }
                .btn-secondary:hover {
                    background: var(--gray-50);
                    transform: translateY(-2px);
                }
                .helpful-links {
                    margin-top: 40px;
                    padding-top: 40px;
                    border-top: 1px solid var(--gray-100);
                }
                .helpful-links h3 {
                    font-size: 18px;
                    font-weight: 600;
                    color: var(--dark);
                    margin-bottom: 20px;
                }
                .link-grid {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
                    gap: 12px;
                }
                .link-item {
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    padding: 12px 16px;
                    background: var(--gray-50);
                    border-radius: 8px;
                    text-decoration: none;
                    color: var(--gray-700);
                    font-size: 14px;
                    font-weight: 500;
                    transition: all 0.2s ease;
                }
                .link-item:hover {
                    background: var(--primary);
                    color: white;
                    transform: translateX(4px);
                }
                .link-item i {
                    font-size: 16px;
                }
                @media (max-width: 640px) {
                    .container {
                        padding: 40px 24px;
                    }
                    .error-code {
                        font-size: 80px;
                    }
                    .error-icon {
                        font-size: 60px;
                    }
                    h1 {
                        font-size: 24px;
                    }
                    .button-group {
                        flex-direction: column;
                        width: 100%;
                    }
                    .btn {
                        width: 100%;
                        justify-content: center;
                    }
                    .link-grid {
                        grid-template-columns: 1fr;
                    }
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='error-icon'>
                    <i class='fas fa-search'></i>
                </div>
                <div class='error-code'>404</div>
                <h1>Oops! Page Not Found</h1>
                <p>
                    The page you're looking for seems to have wandered off.
                    Don't worry, let's get you back on track!
                </p>

                <div class='button-group'>
                    <a href='" . url('/') . "' class='btn btn-primary'>
                        <i class='fas fa-home'></i>
                        Back to Home
                    </a>
                    <button onclick='history.back()' class='btn btn-secondary'>
                        <i class='fas fa-arrow-left'></i>
                        Go Back
                    </button>
                </div>

                <div class='helpful-links'>
                    <h3>Quick Links</h3>
                    <div class='link-grid'>
                        <a href='" . url('/') . "' class='link-item'>
                            <i class='fas fa-home'></i>
                            <span>Home</span>
                        </a>
                        <a href='" . url('shops') . "' class='link-item'>
                            <i class='fas fa-store'></i>
                            <span>Shops</span>
                        </a>
                        <a href='" . url('categories') . "' class='link-item'>
                            <i class='fas fa-th-large'></i>
                            <span>Categories</span>
                        </a>
                        <a href='" . url('deals') . "' class='link-item'>
                            <i class='fas fa-tags'></i>
                            <span>Deals</span>
                        </a>
                    </div>
                </div>
            </div>
        </body>
        </html>";
    }
}

// Load routes
$routes = require BASE_PATH . '/routes/web.php';

// Restore session from remember-me cookie (buyer / business / supplier portals)
require_once BASE_PATH . '/app/Helpers/RememberMeHelper.php';
\App\Helpers\RememberMeHelper::restoreAll();

// Track user/supplier presence (passive — silently updates last_seen_at)
require_once BASE_PATH . '/app/Helpers/PresenceHelper.php';
if (isset($_SESSION['user']) || isset($_SESSION['supplier_id'])) {
    PresenceHelper::track();
}

// Create and dispatch router
$router = new Router($routes);
$router->dispatch();

// Flush output buffer
ob_end_flush();