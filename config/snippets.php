<?php
/**
 * This file is responsible for registering and loading snippets for the Kirby Email Manager plugin.
 * It scans the snippets directory and creates an array mapping snippet names to their file paths.
 * 
 * @package KirbyEmailManager
 * @author Philipp Oehrlein
 */
namespace KirbyEmailManager\Config;

use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

function getSnippets(): array
{
    if (!defined('SNIPPETS_DIR')) {
        define('SNIPPETS_DIR', __DIR__ . '/../snippets');
    }

    $snippetsDir = SNIPPETS_DIR;
    $snippets = [];
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($snippetsDir));

    // Register the Email-Manager block
    $snippets['blocks/email-manager'] = $snippetsDir . '/blocks/email-manager.php';

    // Register the remaining snippets
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $relativePath = str_replace($snippetsDir . '/', '', $file->getPathname());
            // Skip the Blocks directory since we handle it separately
            if (strpos($relativePath, 'blocks/') === 0) {
                continue;
            }
            $key = 'email-manager/' . str_replace('.php', '', $relativePath);
            $snippets[$key] = $file->getPathname();
        }
    }

    return $snippets;
}

return getSnippets();