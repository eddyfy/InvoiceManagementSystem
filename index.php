<?php
declare(strict_types=1);
// index.php
// Include utility functions for input sanitization and authentication checks
// Include the configuration file to load environment variables
session_start(); // Start the session to manage user data across requests

require_once 'autoloader.php'; // Include the autoloader to automatically load class files when they are instantiated

use App\Config;
use App\Includes\Router;
use App\Controllers\HomeController;
use App\Controllers\AuthController;
use App\Controllers\InvoiceController;
use App\Controllers\UserController;

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$router = new Router(); // Create a new instance of the Router class to manage the application's routes

$router->get(Config::get('baseProjectFolder') . '/', action(HomeController::class, 'index')); // Define a GET route for the home page that calls the index method of the HomeController

$router->get(Config::get('baseProjectFolder') . '/dashboard', action(HomeController::class, 'dashboard'));

$router->get(Config::get('baseProjectFolder') . '/invoice', action(InvoiceController::class, 'showInvoiceForm'));

$router->post(Config::get('baseProjectFolder') . '/invoice', action(InvoiceController::class, 'createInvoice'));

$router->get(Config::get('baseProjectFolder') . '/login', action(AuthController::class, 'showLoginForm'));

$router->post(Config::get('baseProjectFolder') . '/login', action(AuthController::class, 'handleLogin'));

$router->get(Config::get('baseProjectFolder') . '/signup', action(AuthController::class, 'showSignupForm'));

$router->post(Config::get('baseProjectFolder') . '/signup', action(AuthController::class, 'handleSignup'));

$router->get(Config::get('baseProjectFolder') . '/logout', action(AuthController::class, 'logout'));

$router->post(Config::get('baseProjectFolder') . '/profile/update-personal-info', action(UserController::class, 'updatePersonalInfo'));

$router->post(Config::get('baseProjectFolder') . '/profile/change-password', action(UserController::class, 'changePassword'));

$router->delete(Config::get('baseProjectFolder') . '/profile/delete', action(UserController::class, 'deleteAccount'));

$router->post(Config::get('baseProjectFolder') . '/profile/update-business-details', action(UserController::class, 'updateBusinessDetails'));

$router->post(Config::get('baseProjectFolder') . '/profile/business-details', action(UserController::class, 'saveBusinessDetails'));

$router->post(Config::get('baseProjectFolder') . '/profile/update-bank-details', action(UserController::class, 'updateBankDetails'));

$router->get(Config::get('baseProjectFolder') . '/invoice/view/{id}', action(InvoiceController::class, 'showInvoice'));

$router->get(Config::get('baseProjectFolder') . '/invoice/edit/{id}', action(InvoiceController::class, 'showEditForm'));

$router->put(Config::get('baseProjectFolder') . '/invoice/update/{id}', action(InvoiceController::class, 'updateInvoice'));

$router->delete(Config::get('baseProjectFolder') . '/invoice/delete/{id}', action(InvoiceController::class, 'deleteInvoice'));

$router->get(Config::get('baseProjectFolder') . '/invoice/pdf/{id}', action(InvoiceController::class, 'downloadPdf'));

$router->post(Config::get('baseProjectFolder') . '/invoice/pdf-public', action(InvoiceController::class, 'downloadPdfPublic'));


$router->dispatch($path);

// Helper function to create a closure that instantiates the specified controller and calls the specified method
function action(string $controller, string $method): Closure { 
    return function (array $params = []) use ($controller, $method) {
        return (new $controller())->$method($params);
    };
}
?>
