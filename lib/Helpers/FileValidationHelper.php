<?php

namespace KirbyEmailManager\Helpers;
use KirbyEmailManager\Helpers\LanguageHelper;
use Exception;

/**
 * Helper class for file validation.
 * @author: Philipp Oehrlein
 * @version: 1.0.0
 */
class FileValidationHelper
{
    private static $allowedMimeTypes = [
        'image/jpeg' => ['jpg', 'jpeg'],
        'image/png' => ['png'],
        'image/gif' => ['gif'],
        'image/webp' => ['webp'],
        'image/svg+xml' => ['svg'],
        'application/pdf' => ['pdf'],
        'application/msword' => ['doc'],
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => ['docx'],
        'application/vnd.ms-excel' => ['xls'],
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => ['xlsx'],
        'application/vnd.ms-powerpoint' => ['ppt'],
        'application/vnd.openxmlformats-officedocument.presentationml.presentation' => ['pptx'],
        'text/plain' => ['txt'],
        'text/csv' => ['csv'],
        'application/json' => ['json'],
        'application/zip' => ['zip'],
        'application/x-rar-compressed' => ['rar'],
        'application/x-7z-compressed' => ['7z']
    ];

    private static $maxFileSize = 5242880;
    private const BYTES_PER_MB = 1048576;

    /**
     * Converts extension array to allowed_mimes array
     *
     * @param array $allowedTypes Array of file extensions (e.g., ['pdf', 'jpg', 'png'])
     * @return array Array of MIME types
     */
    private static function convertTypesToMimes(array $allowedTypes): array
    {
        $allowedMimes = [];
        
        foreach ($allowedTypes as $type) {
            $type = strtolower(trim($type));
            
            // Find MIME type for this extension
            foreach (self::$allowedMimeTypes as $mimeType => $extensions) {
                if (in_array($type, $extensions)) {
                    $allowedMimes[] = $mimeType;
                    break;
                }
            }
        }
        
        return array_unique($allowedMimes);
    }

    /**
     * Validates a file based on the provided configuration and translations.
     *
     * @param array $file The file array from upload.
     * @param array $fieldConfig The field configuration.
     * @param string $languageCode The language code.
     * @return array The validation errors.
     */
    public static function validateFile(array $file, array $fieldConfig, string $languageCode): array
    {
        $errors = [];
        $languageHelper = new LanguageHelper($languageCode, $fieldConfig);
        
        // 1. Basic Security Checks
        if ($file['error'] !== UPLOAD_ERR_OK || empty($file['tmp_name'])) {
            return ['error' => $languageHelper->get('validation.fields.file.upload_error')];
        }

        // 2. Check for hidden files
        if (!self::checkForHiddenFile($file['name'])) {
            return ['error' => $languageHelper->get('validation.fields.file.hidden_file')];
        }

        // 3. Check file extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if ($extension === 'php' || $extension === 'exe') {
            return ['error' => $languageHelper->get('validation.fields.file.security_error')];
        }

        // 4. Check MIME-Type
        $allowedMimes = [];
        
        // Support extension (converted to MIME types)
        if (isset($fieldConfig['extension']) && is_array($fieldConfig['extension'])) {
            $allowedMimes = self::convertTypesToMimes($fieldConfig['extension']);
        } else {
            // Fallback to all supported MIME types
            $allowedMimes = array_keys(self::$allowedMimeTypes);
        }
        
        if (!in_array($file['type'], $allowedMimes)) {
            return ['error' => str_replace(
                ':allowedtypes',
                implode(', ', $allowedMimes),
                $languageHelper->get('validation.fields.file.invalid_type')
            )];
        }

        // 5. Check actual MIME-Type
        if (!self::validateActualMimeType($file['tmp_name'], $allowedMimes)) {
            return ['error' => $languageHelper->get('validation.fields.file.mime_mismatch')];
        }

        // 6. Check file signature
        if (!self::validateFileSignature($file['tmp_name'], $file['type'])) {
            return ['error' => $languageHelper->get('validation.fields.file.invalid_signature')];
        }

        // 7. Check file size
        $maxSize = $fieldConfig['maxsize'] ?? self::$maxFileSize;
        if ($file['size'] > $maxSize) {
            return ['error' => str_replace(
                [':maxsize', ':size'],
                [
                    (string)round($maxSize / self::BYTES_PER_MB, 2),
                    (string)round($file['size'] / self::BYTES_PER_MB, 2)
                ],
                $languageHelper->get('validation.fields.file.too_large')
            )];
        }

        return $errors;
    }

    /**
     * Validates the actual MIME-Type of a file
     */
    private static function validateActualMimeType($filePath, array $allowedMimes = null): bool 
    {
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $actualMimeType = finfo_file($finfo, $filePath);
            finfo_close($finfo);
            
            // Use provided allowed MIME types or fallback to all supported types
            $allowedTypes = $allowedMimes ?? array_keys(self::$allowedMimeTypes);
            return in_array($actualMimeType, $allowedTypes);
        }
        return false;
    }

    /**
     * Checks for hidden files
     */
    private static function checkForHiddenFile($fileName): bool 
    {
        return !str_starts_with($fileName, '.');
    }

    /**
     * Validates the file signature (Magic Numbers)
     */
    private static function validateFileSignature($filePath, $mimeType): bool 
    {
        $signatures = [
            'image/jpeg' => "\xFF\xD8\xFF",
            'image/png'  => "\x89\x50\x4E\x47\x0D\x0A\x1A\x0A",
            'image/gif'  => "\x47\x49\x46\x38",
            'image/webp' => "\x52\x49\x46\x46",
            'application/pdf' => "\x25\x50\x44\x46",
            'application/msword' => "\xD0\xCF\x11\xE0\xA1\xB1\x1A\xE1",
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => "\x50\x4B\x03\x04",
            'application/zip' => "\x50\x4B\x03\x04",
            'application/x-rar-compressed' => "\x52\x61\x72\x21\x1A\x07"
        ];

        // If no signature is defined for the MIME type, skip the check
        if (!isset($signatures[$mimeType])) {
            return true;
        }

        // Validate file exists and is readable
        if (!file_exists($filePath) || !is_readable($filePath)) {
            return false;
        }

        $handle = fopen($filePath, 'rb');
        if ($handle === false) {
            return false;
        }

        $bytes = fread($handle, 8);
        fclose($handle);

        // Check if we read enough bytes
        if ($bytes === false || strlen($bytes) < strlen($signatures[$mimeType])) {
            return false;
        }

        return str_starts_with($bytes, $signatures[$mimeType]);
    }
}
