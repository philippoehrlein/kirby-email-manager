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
        'application/pdf' => ['pdf'],
        'application/msword' => ['doc'],
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => ['docx'],
        'application/zip' => ['zip'],
        'application/x-rar-compressed' => ['rar']
    ];

    private static $maxFileSize = 5242880;
    private const BYTES_PER_MB = 1048576;

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
        
        // 1. Grundlegende Sicherheitschecks
        if ($file['error'] !== UPLOAD_ERR_OK || empty($file['tmp_name'])) {
            return ['error' => $languageHelper->get('validation.fields.file.upload_error')];
        }

        // 2. Prüfe Extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if ($extension === 'php' || $extension === 'exe') {
            return ['error' => $languageHelper->get('validation.fields.file.security_error')];
        }

        // 3. Prüfe MIME-Type
        if (!isset(self::$allowedMimeTypes[$file['type']])) {
            return ['error' => str_replace(
                ':allowedTypes',
                implode(', ', array_keys(self::$allowedMimeTypes)),
                $languageHelper->get('validation.fields.file.invalid_type')
            )];
        }

        // 4. Prüfe Dateigröße
        $maxSize = $fieldConfig['max_size'] ?? self::$maxFileSize;
        if ($file['size'] > $maxSize) {
            return ['error' => str_replace(
                [':maxSize', ':size'],
                [
                    (string)round($maxSize / self::BYTES_PER_MB, 2),
                    (string)round($file['size'] / self::BYTES_PER_MB, 2)
                ],
                $languageHelper->get('validation.fields.file.too_large')
            )];
        }

        return $errors;
    }
}
