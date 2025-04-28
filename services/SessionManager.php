<?php

namespace App\Services;

class SessionManager
{
    /**
     * Start a session if one doesn't exist already
     *
     * @return bool True if session started successfully, false otherwise
     */
    public static function ensureSessionStarted(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            if (headers_sent()) {
                // Log or handle the error more gracefully
                trigger_error('Session cannot be started - headers already sent', E_USER_WARNING);
                return false;
            }
            session_start();
        }
        return true;
    }

    /**
     * Set a session variable
     *
     * @param string $key The session key
     * @param mixed $value The value to store
     * @return bool True if successful, false otherwise
     */
    public static function set(string $key, $value): bool
    {
        if (!self::ensureSessionStarted()) {
            return false;
        }
        $_SESSION[$key] = $value;
        return true;
    }

    /**
     * Get a session variable
     *
     * @param string $key The session key
     * @param mixed $default Default value if key doesn't exist
     * @return mixed The session value or default
     */
    public static function get(string $key, $default = null)
    {
        self::ensureSessionStarted();
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Check if a session variable exists
     *
     * @param string $key The session key
     * @return bool True if the key exists
     */
    public static function has(string $key): bool
    {
        self::ensureSessionStarted();
        return isset($_SESSION[$key]);
    }

    /**
     * Remove a session variable
     *
     * @param string $key The session key
     * @return bool True if successful, false otherwise
     */
    public static function remove(string $key): bool
    {
        if (!self::ensureSessionStarted()) {
            return false;
        }
        unset($_SESSION[$key]);
        return true;
    }

    /**
     * Set the logged in user ID
     *
     * @param int|string $userId The user ID to set
     * @return bool True if successful, false otherwise
     */
    public static function setLoggedInUserId($userId): bool
    {
        return self::set('logged_in_user_id', $userId);
    }

    /**
     * Get the logged in user ID
     *
     * @return mixed The logged in user ID or null if not set
     */
    public static function getLoggedInUserId()
    {
        return self::get('logged_in_user_id');
    }

    /**
     * Clear all session data
     *
     * @return bool True if successful, false otherwise
     */
    public static function clear(): bool
    {
        if (!self::ensureSessionStarted()) {
            return false;
        }
        session_unset();
        session_destroy();
        return true;
    }
} 