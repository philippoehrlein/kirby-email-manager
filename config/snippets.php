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

    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $relativePath = str_replace($snippetsDir . '/', '', $file->getPathname());
            $key = 'email-manager/' . str_replace('.php', '', $relativePath);
            $snippets[$key] = $file->getPathname();
        }
    }

    return $snippets;
}

return getSnippets();