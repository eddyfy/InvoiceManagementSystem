<?php
declare(strict_types=1);

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
        $this->routes[$method][$path] = $callback;
    }

    public function dispatch(string $path):void { //function to run the callback function associated with the given path called from index.php
        $method = $_SERVER['REQUEST_METHOD']; //get the HTTP method of the request

          // Check if the path exists under the current method
        if (isset($this->routes[$method][$path])) {
            $handler = $this->routes[$method][$path];
            call_user_func($handler); //call the callback function associated with the path
            return;
        }

        // Path exists but not for this method
        if (isset($this->routes[$method === 'GET' ? 'POST' : 'GET'][$path])) {
            http_response_code(405);
            echo '405 Method Not Allowed';
            return;
        }
        
        // Path does not exist at all
        http_response_code(404);
        echo '404 Not Found';
    }
}

?>