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
            return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
        }
        return '';
    }

    /**
     * Sanitizes a filename to prevent directory traversal attacks.
     *
     * @param string $filename The filename to sanitize.
     * @return string The sanitized filename.
     */
    public static function sanitizeFilename($filename)
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
                if($value === null) {
                    $sanitizedData[$key] = [];
                } else {
                    $sanitizedData[$key] = array_map([self::class, 'sanitize'], $value);
                }
            } else {
                if($value === null) {
                    $sanitizedData[$key] = '';
                } else {
                    $sanitizedData[$key] = self::sanitize($value);
                }
            }
        }
        return $sanitizedData;
    }
}