<?php

class Router
{
    public static function getRoute()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';

        $basePath = rtrim(
            str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')),
            '/'
        );

        if ($basePath !== '' && $basePath !== '/' && str_starts_with($uri, $basePath)) {
            $uri = substr($uri, strlen($basePath));
        }

        return '/' . ltrim($uri, '/');
    }
}
