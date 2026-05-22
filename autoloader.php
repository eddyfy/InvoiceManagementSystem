<?php
spl_autoload_register(function ($class) { // Register an autoloader function to automatically include class files when they are instantiated
    $paths = ['', 'controllers/', 'models/', 'includes/', 'requests/']; // Define the directories to search for class files
    foreach ($paths as $path) {
        $file = __DIR__ . '/' . $path . $class . '.php'; // Construct the file path for the class
        if (file_exists($file)) { // Check if the file exists
            require_once $file; // Include the class file if it exists
            return; // Exit the loop once the class is loaded
        }
    }
    throw new Exception("Class $class not found."); // Throw an exception if the class file cannot
}); 
