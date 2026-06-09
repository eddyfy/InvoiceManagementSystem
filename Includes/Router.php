<?php
declare(strict_types=1);
namespace App\Includes;
use Closure;

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

//     public function dispatch(string $path):void { //function to run the callback function associated with the given path called from index.php
//         $method = $_SERVER['REQUEST_METHOD']; //get the HTTP method of the request

//          // Check for method override
//         if ($method === 'POST' && isset($_POST['_method'])) {
//             $method = strtoupper($_POST['_method']);
//         }

//           // Check if the path exists under the current method
//         if (isset($this->routes[$method][$path])) {
//             $handler = $this->routes[$method][$path];
//             call_user_func($handler); //call the callback function associated with the path
//             return;
//         }

//         // If not found, check for regex patterns (for dynamic routes)
//         if (isset($this->routes[$method])) {
//             foreach ($this->routes[$method] as $pattern => $handler) {
//                 if (preg_match($pattern, $path, $matches)) {
//                     $params = array_filter($matches, fn($k) => !is_int($k), ARRAY_FILTER_USE_KEY);
//                     call_user_func($handler, $params);
//                     return;
//                 }
//             }
//         }

//         // Path exists but not for this method
//         if (isset($this->routes[$method === 'GET' ? 'POST' : 'GET'][$path])) {
//             http_response_code(405);
//             echo '405 Method Not Allowed';
//             return;
//         }
        
//         // Path does not exist at all
//         http_response_code(404);
//         echo '404 Not Found';
//     }
// }
    public function dispatch(string $path): void {
        $method = $_SERVER['REQUEST_METHOD'];

        // if ($method === 'POST' && isset($_POST['_method'])) {
        //     $method = strtoupper($_POST['_method']);
        // }
        if($method === 'POST'){
            if(!isset($_POST['csrf_token'], $_SESSION['csrf_token']) && !hash_equals($_SESSION['csrf_token'], $_POST['csrf_tokem'])){ //middleware handling csrf validation
                echo "Invalid CSRF token...";
                exit();
            }
            unset($_SESSION['csrf_token']);
            if(isset($_POST['_method'])){
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