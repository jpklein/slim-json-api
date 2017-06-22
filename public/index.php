<?php declare(strict_types=1);

require_once '../src/App.php';

// Creates application
$router = \RestSample\App::withConfig()->getRouter();

// Handles request
$router->run();
