<?php

namespace KirbyEmailManager\Helpers;

class FileValidationHelper
{
    private static $allowedMimeTypes = [
        'application/pdf' => ['pdf'],
        'image/jpeg' => ['jpg', 'jpeg'],
        'image/png' => ['png'],
        'image/gif' => ['gif'],
        'application/msword' => ['doc'],
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => ['docx']
    ];

    private static $maxFileSize = 5242880;

    public static function validateFile(array $file, array $fieldConfig, array $translations, string $languageCode): array
    {
        $errors = [];
        
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['error' => self::getUploadErrorMessage($file['error'], $translations)];
        }

        // Prüfe Dateigröße
        $maxSize = (int)($fieldConfig['max_size'] ?? self::$maxFileSize);
        if ($file['size'] > $maxSize) {
            $errors['size'] = str_replace(
                ':maxSize',
                round($maxSize / 1048576, 2),
                $fieldConfig['error_message'][$languageCode] ?? $translations['error_messages']['file_too_large']
            );
        }

        // Prüfe MIME-Type
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        
        $allowedTypes = $fieldConfig['allowed_types'] ?? array_keys(self::$allowedMimeTypes);
        if (!in_array($mimeType, $allowedTypes)) {
            $errors['type'] = str_replace(
                ':allowedTypes',
                implode(', ', $allowedTypes),
                $fieldConfig['error_message'][$languageCode] ?? $translations['error_messages']['invalid_file_type']
            );
        }

        // Prüfe auf schädlichen Content
        if (!self::isSafeFile($file['tmp_name'])) {
            $errors['security'] = $translations['error_messages']['malicious_file'];
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

    private static function isSafeFile(string $filePath): bool
    {
        $content = file_get_contents($filePath);
        $dangerousPatterns = [
            '/<%.*%>/i',     // ASP-Style Code
            '/<script.*>/i',  // JavaScript
            '/<iframe.*>/i',  // iframes
            '/<object.*>/i'   // Embedded Objects
        ];

        foreach ($dangerousPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return false;
            }
        }

        return true;
    }

    public static function generateSecureFilename(string $originalName): string
    {
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        return uniqid('file_', true) . '.' . strtolower($extension);
    }
}
