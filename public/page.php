<?php
require __DIR__ . '/../src/core/Database.php';
require __DIR__ . '/../src/core/Controller.php';
require __DIR__ . '/../src/core/Helpers.php';
require __DIR__ . '/../src/controllers/PageController.php';

use Controllers\PageController;

$token = $_GET['token'] ?? null;

if (!$token) {
    die("Unique link not found");
}

$controller = new PageController($token);
$controller->handleRequest();
