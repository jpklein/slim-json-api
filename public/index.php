<?php
/**
 * @author    Philippe Klein <jpklein@gmail.com>
 * @copyright Copyright (c) 2017 Philippe Klein
 * @version   0.3
 */
declare(strict_types=1);

require_once '../src/App.php';

// Creates application
$router = \RestSample\App::withConfig()->getRouter();

// Handles request
$router->run();
