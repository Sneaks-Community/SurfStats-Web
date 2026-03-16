<?php

/**
 * Surf Stats Configuration File
 * 
 * This file contains database connection settings and application configuration.
 * Sensitive data should be stored in .env file (excluded from version control).
 */

// Environment variable loader
function loadEnv($filePath) {
    if (!file_exists($filePath)) {
        return;
    }
    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        $parts = explode('=', $line, 2);
        if (count($parts) === 2) {
            $_ENV[trim($parts[0])] = trim($parts[1]);
        }
    }
}

// Load environment variables from .env file
loadEnv(__DIR__ . '/.env');

// Database configuration from environment
$db_server = $_ENV['DB_SERVER'] ?? 'localhost';
$db_name = $_ENV['DB_NAME'] ?? '';
$db_user = $_ENV['DB_USER'] ?? '';
$db_passwd = $_ENV['DB_PASSWD'] ?? '';
$db_prefix = $_ENV['DB_PREFIX'] ?? 'ck_';

// Application settings
$stat_name = $_ENV['STAT_NAME'] ?? 'Surf Stats';
$group_name = $_ENV['GROUP_NAME'] ?? 'Website';
$group_url = $_ENV['GROUP_URL'] ?? '/';
$local_timezone = $_ENV['LOCAL_TIMEZONE'] ?? 'America/Chicago';
$conf_language = $_ENV['CONF_LANGUAGE'] ?? 'eng';
$conf_record_stats = $_ENV['CONF_RECORD_STATS'] ?? '1';
$map_images_url = $_ENV['MAP_IMAGES_URL'] ?? '../bans/images/maps/';

// Set timezone
date_default_timezone_set($local_timezone);

// ============================================================================
// SECURITY HELPER FUNCTIONS
// ============================================================================

/**
 * Create a secure database connection
 * @return mysqli Database connection object
 */
function dbConnect() {
    global $db_server, $db_user, $db_passwd, $db_name;
    
    $conn = new mysqli($db_server, $db_user, $db_passwd, $db_name);
    
    if ($conn->connect_error) {
        error_log("Database connection failed: " . $conn->connect_error);
        die("Database connection error. Please try again later.");
    }
    
    // Set UTF-8 encoding
    $conn->set_charset("utf8mb4");
    
    return $conn;
}

/**
 * Escape output for HTML display (XSS prevention)
 * @param mixed $string The string to escape
 * @return string Escaped string
 */
function esc($string) {
    if ($string === null) {
        return '';
    }
    return htmlspecialchars((string)$string, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/**
 * Generate a CSRF token
 * @return string CSRF token
 */
function csrfToken() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validate a CSRF token
 * @param string $token The token to validate
 * @return bool True if valid, false otherwise
 */
function validateCsrf($token) {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// ============================================================================
// INPUT VALIDATION FUNCTIONS
// ============================================================================

/**
 * Validate Steam ID format
 * Expected format: STEAM_0:0:12345678 or STEAM_0:1:12345678
 * @param string $steamid The Steam ID to validate
 * @return bool True if valid, false otherwise
 */
function validateSteamId($steamid) {
    return preg_match('/^STEAM_[0-9]:[0-1]:[0-9]+$/', $steamid);
}

/**
 * Validate map name
 * Map names should only contain alphanumeric characters, underscores, and hyphens
 * @param string $mapname The map name to validate
 * @return bool True if valid, false otherwise
 */
function validateMapName($mapname) {
    return preg_match('/^[a-zA-Z0-9_-]+$/', $mapname);
}

/**
 * Validate page number
 * @param mixed $page The page number to validate
 * @return bool True if valid, false otherwise
 */
function validatePageNumber($page) {
    return is_numeric($page) && intval($page) >= 0;
}

/**
 * Sanitize search input
 * @param string $input The search input
 * @return string Sanitized input
 */
function sanitizeSearchInput($input) {
    // Remove any potentially dangerous characters
    return preg_replace('/[^a-zA-Z0-9_\-\s]/', '', $input);
}

/**
 * Generate security headers
 * Call this function at the beginning of index.php
 */
function setSecurityHeaders() {
    if (headers_sent()) {
        return;
    }
    
    // Prevent MIME type sniffing
    header('X-Content-Type-Options: nosniff');
    
    // Prevent clickjacking
    header('X-Frame-Options: SAMEORIGIN');
    
    // XSS Protection
    header('X-XSS-Protection: 1; mode=block');
    
    // HSTS (HTTPS only)
    // header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    
    // Content Security Policy
    header("Content-Security-Policy: default-src 'self'; " .
           "script-src 'self' 'unsafe-inline' https://code.jquery.com https://static.cloudflareinsights.com; " .
           "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdnjs.cloudflare.com; " .
           "img-src 'self' data: https:; " .
           "font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com; " .
           "frame-ancestors 'self';");
    
    // Referrer Policy
    header('Referrer-Policy: strict-origin-when-cross-origin');
}

?>
