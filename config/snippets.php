<?php
namespace KirbyEmailManager\Config;

use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

function getSnippets(): array
{
    // SNIPPETS_DIR hier direkt definieren, falls noch nicht definiert
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