<?php

require __DIR__ . '/../src/core/Database.php';
require __DIR__ . '/../src/core/Helpers.php';
require __DIR__ . '/../src/core/Controller.php';
require __DIR__ . '/../src/controllers/RegistrationController.php';

use Controllers\RegistrationController;
use Random\RandomException;

$controller = new RegistrationController();

try {
    $controller->handleRequest();
} catch (RandomException $e) {
    // logging
}
