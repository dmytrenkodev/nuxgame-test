<?php

namespace Core;

use Random\RandomException;

class Helpers
{
    /**
     * @throws RandomException
     */
    public static function generateToken(int $length = 32): string
    {
        return bin2hex(random_bytes($length / 2));
    }

    /**
     * @param string $token
     * @return string
     */
    public static function generateLink(string $token): string
    {
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost:8080';
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        return "{$scheme}://{$host}/page.php?token={$token}";
    }
}
