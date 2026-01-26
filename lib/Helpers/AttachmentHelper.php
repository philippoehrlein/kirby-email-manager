<?php

namespace KirbyEmailManager\Helpers;

use KirbyEmailManager\Helpers\SecurityHelper;
use KirbyEmailManager\Helpers\FileValidationHelper;

/**
 * AttachmentHelper class provides methods to handle file uploads and attachments.
 * Author: Philipp Oehrlein
 * Version: 1.0.0
 */
class AttachmentHelper
{
    /**
     * Normalizes upload field data to a consistent array format.
     * Handles both single file uploads and multiple file uploads.
     *
     * @param mixed $uploadField The upload field data from $_FILES
     * @return array Normalized array of file uploads
     */
    public static function normalizeUploads($uploadField): array
    {
        if (!is_array($uploadField)) {
            return [];
        }

        // Single file structure: ['name'=>..., 'tmp_name'=>..., ...]
        if (isset($uploadField['name']) && isset($uploadField['tmp_name'])) {
            return [$uploadField];
        }

        // Multiple files: indexed array of file arrays
        return array_values($uploadField);
    }

    /**
     * Validates file uploads against field configuration.
     *
     * @param array $fileList Normalized list of file uploads
     * @param array $fieldConfig Field configuration from template
     * @param string $languageCode Language code for error messages
     * @return array ['errors' => [], 'validFiles' => []]
     */
    public static function validateFiles(array $fileList, array $fieldConfig, string $languageCode): array
    {
        $errors = [];
        $validFiles = [];

        // Check maximum number of files
        if (isset($fieldConfig['max']) && count($fileList) > $fieldConfig['max']) {
            $errors[] = str_replace(
                ':maxFiles',
                $fieldConfig['max'],
                'validation.fields.file.too_many_files'
            );
            return ['errors' => $errors, 'validFiles' => []];
        }

        // Validate each file
        foreach ($fileList as $file) {
            if (!is_array($file) || !isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
                continue;
            }

            $fileErrors = FileValidationHelper::validateFile($file, $fieldConfig, $languageCode);
            if (!empty($fileErrors)) {
                $errors[] = $fileErrors['error'] ?? 'validation.fields.file.unknown_error';
                break; // Stop on first error
            }

            $validFiles[] = $file;
        }

        return ['errors' => $errors, 'validFiles' => $validFiles];
    }

    /**
     * Moves uploaded files to a temporary directory with clean names.
     *
     * @param array $validFiles Array of validated file uploads
     * @return array Array of temporary file paths for email attachments
     */
    public static function moveToTemp(array $validFiles): array
    {
        $attachments = [];
        $baseDir = rtrim(sys_get_temp_dir(), "\/\\") . '/kem-' . uniqid('', true);
        @mkdir($baseDir, 0700, true);

        foreach ($validFiles as $file) {
            $originalName = SecurityHelper::sanitizeFilename($file['name']);
            $targetPath = $baseDir . '/' . $originalName; // Originalname beibehalten

            // Add unique suffix for name collisions
            if (file_exists($targetPath)) {
                $pi = pathinfo($originalName);
                $base = $pi['filename'] ?? 'file';
                $ext = isset($pi['extension']) ? '.' . $pi['extension'] : '';
                $targetPath = $baseDir . '/' . $base . '-' . uniqid('', true) . $ext;
            }

            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                $attachments[] = $targetPath;
            }
        }

        return $attachments;
    }

    /**
     * Cleans up temporary attachment files.
     *
     * @param array $attachmentPaths Array of temporary file paths to delete
     */
    public static function cleanup(array $attachmentPaths): void
    {
        $dirs = [];
        foreach ($attachmentPaths as $path) {
            if (is_string($path) && file_exists($path)) {
                @unlink($path);
                $dirs[dirname($path)] = true;
            }
        }
        // Try to remove created temp directories (if empty)
        foreach (array_keys($dirs) as $dir) {
            @rmdir($dir);
        }
    }

    /**
     * Processes all file uploads for a form submission.
     * Combines normalization, validation, and temporary storage.
     *
     * @param array $uploads All file uploads from $_FILES
     * @param array $templateConfig Template configuration
     * @param string $languageCode Language code for error messages
     * @return array ['errors' => [], 'attachments' => [], 'fileData' => []]
     */
    public static function processUploads(array $uploads, array $templateConfig, string $languageCode): array
    {
        $allErrors = [];
        $allAttachments = [];
        $fileData = [];

        foreach ($uploads as $fieldKey => $uploadField) {
            $fileList = self::normalizeUploads($uploadField);
            
            $fieldConfig = $templateConfig['fields'][$fieldKey] ?? [];
            
            // Required file check
            if (!empty($fieldConfig['required']) && empty($fileList)) {
                $allErrors[$fieldKey] = 'validation.fields.file.no_file_uploaded';
                continue;
            }
            
            if (empty($fileList)) {
                continue;
            }

            $result = self::validateFiles($fileList, $fieldConfig, $languageCode);
            
            if (!empty($result['errors'])) {
                $allErrors[$fieldKey] = $result['errors'][0]; // Take first error
                continue;
            }

            // Move to temp and collect attachments
            $attachments = self::moveToTemp($result['validFiles']);
            $allAttachments = array_merge($allAttachments, $attachments);

            // Store file names for email content
            $fileData[$fieldKey] = array_map(function($file) {
                return $file['name'];
            }, $result['validFiles']);
        }

        return [
            'errors' => $allErrors,
            'attachments' => $allAttachments,
            'fileData' => $fileData
        ];
    }
}
