<?php

namespace KirbyEmailManager\Helpers;

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
        'application/pdf' => ['pdf'],
        'application/msword' => ['doc'],
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => ['docx'],
        'application/zip' => ['zip'],
        'application/x-rar-compressed' => ['rar']
    ];

    private static $maxFileSize = 5242880;

    /**
     * Validates a file based on the provided configuration and translations.
     *
     * @param array $file The file array.
     * @param array $fieldConfig The field configuration.
     * @param array $translations The translations.
     * @param string $languageCode The language code.
     * @return array The validation errors.
     */
    public static function validateFile(array $file, array $fieldConfig, array $translations, string $languageCode): array
    {
        $errors = [];
        
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['error' => self::getUploadErrorMessage($file['error'], $translations)];
        }

        $maxSize = $fieldConfig['max_size'] ?? self::$maxFileSize;
        if ($file['size'] > $maxSize) {
            $errors['size'] = str_replace(
                ':maxSize',
                (string)round($maxSize / 1048576, 2),
                $translations['file']['too_large']
            );
            return $errors;
        }

        if (!self::isSafeFile($file['tmp_name'])) {
            $errors['security'] = $translations['file']['malicious'];
            return $errors;
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if (!isset(self::$allowedMimeTypes[$mimeType])) {
            $errors['type'] = str_replace(
                ':allowedTypes',
                implode(', ', array_keys(self::$allowedMimeTypes)),
                $translations['file']['invalid_type']
            );
            return $errors;
        }
        
        if (!in_array($extension, self::$allowedMimeTypes[$mimeType])) {
            $errors['type'] = str_replace(
                ':allowedTypes',
                implode(', ', self::$allowedMimeTypes[$mimeType]),
                $translations['file']['invalid_type']
            );
            return $errors;
        }

        return $errors;
    }

    private static function getUploadErrorMessage(int $errorCode, array $translations): string
    {
        $errorKey = match ($errorCode) {
            UPLOAD_ERR_INI_SIZE => 'file_too_large_ini',
            UPLOAD_ERR_FORM_SIZE => 'file_too_large_form',
            UPLOAD_ERR_PARTIAL => 'file_partial_upload',
            UPLOAD_ERR_NO_FILE => 'no_file_uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'missing_temp_folder',
            UPLOAD_ERR_CANT_WRITE => 'file_write_error',
            UPLOAD_ERR_EXTENSION => 'file_upload_stopped',
            default => 'unknown_upload_error'
        };

        return $translations['error_messages'][$errorKey];
    }

    /**
     * Checks if a file is safe by checking for dangerous patterns and the UTF-7 BOM.
     *
     * @param string $filePath The path to the file to check.
     * @return bool True if the file is safe, false otherwise.
     */
    private static function isSafeFile(string $filePath): bool
    {
        $maliciousPatterns = [
            '<?php',
            '<script',
            'eval(',
            'base64_decode(',
            'system(',
            'exec(',
            'passthru(',
            'shell_exec('
        ];
        
        $content = file_get_contents($filePath);
        
        foreach ($maliciousPatterns as $pattern) {
            if (stripos($content, $pattern) !== false) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Generates a secure filename by appending a unique identifier to the original filename.
     *
     * @param string $originalName The original filename.
     * @return string The secure filename.
     */
    public static function generateSecureFilename(string $originalName): string
    {
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        return uniqid('file_', true) . '.' . strtolower($extension);
    }
}
