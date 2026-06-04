<?php
declare(strict_types=1);
// index.php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once 'utils.php'; // Include utility functions for input sanitization and authentication checks
require_once 'Config.php'; // Include the configuration file to load environment variables
session_start(); // Start the session to manage user data across requests

require_once 'autoloader.php'; // Include the autoloader to automatically load class files when they are instantiated

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);


require_once 'includes/Router.inc.php';

$router = new Router(); // Create a new instance of the Router class to manage the application's routes

$router->get(Config::get('baseProjectFolder') . '/', action(HomeController::class, 'index')); // Define a GET route for the home page that calls the index method of the HomeController

$router->get(Config::get('baseProjectFolder') . '/dashboard', action(HomeController::class, 'dashboard'));

$router->get(Config::get('baseProjectFolder') . '/invoice', action(InvoiceController::class, 'showInvoiceForm'));

$router->post(Config::get('baseProjectFolder') . '/invoice', action(InvoiceController::class, 'handleInvoiceSubmission'));

$router->get(Config::get('baseProjectFolder') . '/login', action(AuthController::class, 'showLoginForm'));

$router->post(Config::get('baseProjectFolder') . '/login', action(AuthController::class, 'handleLogin'));

$router->get(Config::get('baseProjectFolder') . '/signup', action(AuthController::class, 'showSignupForm'));

$router->post(Config::get('baseProjectFolder') . '/signup', action(AuthController::class, 'handleSignup'));

$router->get(Config::get('baseProjectFolder') . '/logout', action(AuthController::class, 'logout'));

$router->post(Config::get('baseProjectFolder') . '/profile/update', action(UserController::class, 'updateProfile'));

$router->post(Config::get('baseProjectFolder') . '/profile/change-password', action(UserController::class, 'changePassword'));

$router->delete(Config::get('baseProjectFolder') . '/profile/delete', action(UserController::class, 'deleteAccount'));

$router->post(Config::get('baseProjectFolder') . '/profile/bank-details', action(UserController::class, 'saveBankDetails'));

$router->post(Config::get('baseProjectFolder') . '/profile/update-bank-details', action(UserController::class, 'updateBankDetails'));

$router->get(Config::get('baseProjectFolder') . '/invoice/view/{id}', action(InvoiceController::class, 'showInvoice'));

$router->get(Config::get('baseProjectFolder') . '/invoice/edit/{id}', action(InvoiceController::class, 'showEditForm'));

$router->put(Config::get('baseProjectFolder') . '/invoice/update/{id}', action(InvoiceController::class, 'updateInvoice'));

$router->delete(Config::get('baseProjectFolder') . '/invoice/delete/{id}', action(InvoiceController::class, 'deleteInvoice'));


$router->dispatch($path);

// Helper function to create a closure that instantiates the specified controller and calls the specified method
function action(string $controller, string $method): Closure { 
    return function (array $params = []) use ($controller, $method) {
        return (new $controller())->$method($params);
    };
}
?>
