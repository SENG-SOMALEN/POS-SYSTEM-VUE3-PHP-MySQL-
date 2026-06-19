<?php

class Router
{
    public static function getRoute()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        $basePath = '/MINI_POS_SYSTEM/Backend/public';

        return str_replace($basePath, '', $uri);
    }
}