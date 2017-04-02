<?php
require_once __DIR__ . '/../vendor/autoload.php';

// Instantiate the app
$container = new \Slim\Container;
$settings = require __DIR__ . '/../src/settings.php';
$app = new \Slim\App($settings);

// Set up dependencies
require __DIR__ . '/../src/dependencies.php';
// Register middleware

// Register routes
require __DIR__ . '/../src/routes.php';

// Run app
$app->run();
?>
