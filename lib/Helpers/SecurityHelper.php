<?php

namespace KirbyEmailManager\Helpers;

use Kirby\Filesystem\F;
use Kirby\Toolkit\V;

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
     * @param string $string The input string to escape.
     * @return string The escaped string.
     */
    public static function escapeHtml($string): string 
    {
        if (!is_string($string)) {
            return '';
        }
        return \Kirby\Toolkit\Escape::html($string);
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
        
        // Use Kirby's proven F::safeName() method as base
        $safeName = F::safeName($filename);
        
        // Additional security measures for contact forms:
        
        // Remove leading dots to prevent hidden files
        $safeName = ltrim($safeName, '.');
        
        // Ensure filename is not empty after sanitization
        if (empty($safeName)) {
            $safeName = 'uploaded_file';
        }
        
        // Limit filename length to prevent buffer overflow attacks
        if (strlen($safeName) > 255) {
            $extension = pathinfo($safeName, PATHINFO_EXTENSION);
            $name = pathinfo($safeName, PATHINFO_FILENAME);
            $safeName = substr($name, 0, 255 - strlen($extension) - 1) . '.' . $extension;
        }
        
        return $safeName;
    }

    /**
     * Sanitizes an input string by removing potentially harmful characters.
     *
     * @param string|array|null $input The input to sanitize.
     * @return string|array The sanitized input.
     */
    public static function sanitize($input)
    {
        if ($input === null) {
            return '';
        }

        if (is_array($input)) {
            return array_map([self::class, 'sanitize'], $input);
        }

        return htmlspecialchars((string)$input, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Validates an email address.
     *
     * @param string|null $email The email address to validate.
     * @return bool True if the email is valid, false otherwise.
     */
    public static function validateEmail($email): bool
    {
        if ($email === null) {
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
        
        // Use Kirby's robust URL validation
        if (!V::url($url)) {
            return false;
        }
        
        // Additional security: only allow http/https protocols
        $urlParts = parse_url($url);
        $allowedProtocols = ['http', 'https'];
        
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
    public static function validateCSRFToken($token): bool 
    {
        if (!$token) {
            return false;
        }
        
        try {
            return csrf($token) === true;
        } catch (\Exception $e) {
            LogHelper::logError('CSRF validation failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Sanitizes and validates an array of form data.
     *
     * @param array|null $data The form data to sanitize and validate.
     * @return array The sanitized and validated data.
     */
    public static function sanitizeAndValidateFormData($data): array
    {
        if ($data === null) {
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
     * Validates file extension against a list of allowed extensions
     *
     * @param string $filename The filename to check
     * @param array $allowedExtensions Array of allowed file extensions
     * @return bool True if extension is allowed, false otherwise
     */
    public static function validateFileExtension(?string $filename, array $allowedExtensions = []): bool
    {
        if ($filename === null || empty($allowedExtensions)) {
            return false;
        }

        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        return in_array($extension, array_map('strtolower', $allowedExtensions));
    }
}