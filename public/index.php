<?php
/**
 * @author    Philippe Klein <jpklein@gmail.com>
 * @copyright Copyright (c) 2017 Philippe Klein
 * @version   0.3
 */
declare(strict_types=1);

// Creates application
$app = new \Slim\App(['settings' => APP_CONFIG]);

// Creates dependency injection container
$container = $app->getContainer();

var_dump($app);
