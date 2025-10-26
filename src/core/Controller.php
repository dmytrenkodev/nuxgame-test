<?php

namespace Core;

use PDO;

abstract class Controller
{
    protected PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }
}
