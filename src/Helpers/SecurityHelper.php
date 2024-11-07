<?php

namespace KirbyEmailManager\Helpers;

/**
 * SecurityHelper class for managing security-related functions
 * 
 * This class provides methods to escape HTML, sanitize filenames, validate email addresses, and generate CSRF tokens.
 * 
 * @author Philipp Oehrlein
 * @version 1.0.0
 */
class SecurityHelper
{
    /**
     * Escapes HTML special characters in a string.
     *
     * @param string $input The input string to escape.
     * @return string The escaped string.
     */
    public static function escapeHtml($string): string 
    {
        if (is_string($string)) {
            return htmlspecialchars($string, ENT_QUOTES | ENT_HTML5 | ENT_SUBSTITUTE, 'UTF-8', true);
        }
        return '';
    }

    /**
     * Sanitizes a filename to prevent directory traversal attacks.
     *
     * @param string $filename The filename to sanitize.
     * @return string The sanitized filename.
     */
    public static function sanitizeFilename(?string $filename): string
    {
        if($filename === null) {
            return '';
        }
        return basename($filename);
    }

    /**
     * Sanitizes an input string by removing potentially harmful characters.
     *
     * @param string $input The input string to sanitize.
     * @return string The sanitized string.
     */
    public static function sanitize($input)
    {
        if ($input === null) {
            return '';
        }
        return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Validates an email address.
     *
     * @param string $email The email address to validate.
     * @return bool True if the email is valid, false otherwise.
     */
    public static function validateEmail($email)
    {
        if($email === null) {
            return false;
        }
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validates a URL for security and format.
     *
     * @param string $url The URL to validate
     * @return bool True if the URL is valid and secure, false otherwise
     */
    public static function validateUrl($url): bool
    {
        if (empty($url)) {
            return false;
        }

        $sanitizedUrl = filter_var($url, FILTER_SANITIZE_URL);
        if (!filter_var($sanitizedUrl, FILTER_VALIDATE_URL)) {
            return false;
        }
        
        // Erlaubte Protokolle
        $allowedProtocols = ['http', 'https'];
        $urlParts = parse_url($sanitizedUrl);
        
        return isset($urlParts['scheme']) && 
            in_array(strtolower($urlParts['scheme']), $allowedProtocols);
    }

    /**
     * Generates a CSRF token.
     *
     * @return string The generated CSRF token.
     */
    public static function generateCSRFToken()
    {
        return csrf();
    }

    /**
     * Validates a CSRF token.
     *
     * @param string $token The token to validate.
     * @return bool True if the token is valid, false otherwise.
     */
    public static function validateCSRFToken($token)
    {
        if($token === null) {
            return false;
        }
        return csrf($token);
    }

    /**
     * Sanitizes and validates an array of form data.
     *
     * @param array $data The form data to sanitize and validate.
     * @return array The sanitized and validated data.
     */
    public static function sanitizeAndValidateFormData($data)
    {
        if($data === null) {
            return [];
        }
        $sanitizedData = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $sanitizedData[$key] = array_map([self::class, 'sanitize'], $value);
            } else {
                $sanitizedData[$key] = self::sanitize($value);
            }
        }
        return $sanitizedData;
    }

    /**
     * Sets security headers.
     *
     * @return void
     */
    public static function setSecurityHeaders(): void
    {
        header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self'");
        header("X-XSS-Protection: 1; mode=block");
        header("X-Content-Type-Options: nosniff");
    }
}