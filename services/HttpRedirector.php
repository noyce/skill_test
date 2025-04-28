<?php

namespace App\Services;

class HttpRedirector
{
    /**
     * Redirect to the specified URL
     * 
     * @param string $url The URL to redirect to
     * @param int $statusCode HTTP status code for the redirect (default: 302)
     * @return void
     */
    public static function redirect(string $url, int $statusCode = 302): void
    {
        header('Location: ' . $url, true, $statusCode);
        exit();
    }
}
