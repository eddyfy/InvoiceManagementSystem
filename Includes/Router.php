<?php
declare(strict_types=1);
namespace App\Includes;
use Closure;
use App\Csrf;

class Router{
    private $routes = []; //contains all the active routes iin the application key is path and value is the callback function
    
    // Specific method helpers
    public function get(string $path, Closure $callback): void {
        $this->add('GET', $path, $callback);
    }

    public function post(string $path, Closure $callback): void {
        $this->add('POST', $path, $callback);
    }

    public function put(string $path, Closure $callback): void {
        $this->add('PUT', $path, $callback);
    }

    public function delete(string $path, Closure $callback): void {
        $this->add('DELETE', $path, $callback);
    }

    // Core method — stores route under method + path
 private function add(string $method, string $path, Closure $callback): void {
    $pattern = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $path);
    $pattern = '#^' . $pattern . '$#';
    $this->routes[$method][] = [
        'pattern' => $pattern,
        'callback' => $callback
    ];
}

    public function dispatch(string $path): void {
        $method = $_SERVER['REQUEST_METHOD'];

         if ($method === 'POST') {
            if (!Csrf::validate($_POST['csrf_token'] ?? null)) {
                http_response_code(419);
                echo "Invalid CSRF token...";
                exit();
            }
            if (isset($_POST['_method'])) {
                $method = strtoupper($_POST['_method']);
            }
        }
        
        // Match against registered routes for this method
        if (isset($this->routes[$method])) {
            foreach ($this->routes[$method] as $route) {
                if (preg_match($route['pattern'], $path, $matches)) {
                    $params = array_filter($matches, fn($k) => !is_int($k), ARRAY_FILTER_USE_KEY);
                    call_user_func($route['callback'], $params);
                    return;
                }
            }
        }

        // Check other methods for 405
        foreach ($this->routes as $registeredMethod => $routes) {
            if ($registeredMethod === $method) continue;
            foreach ($routes as $route) {
                if (preg_match($route['pattern'], $path)) {
                    http_response_code(405);
                    echo '405 Method Not Allowed';
                    return;
                }
            }
        }

        http_response_code(404);
        echo '404 Not Found';
    }
}
?>