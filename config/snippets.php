<?php

namespace KirbyEmailManager\Config;

use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;


function getSnippets(): array
{
    $snippetsDir = SNIPPETS_DIR;
    $snippets = [];
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($snippetsDir));

    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $relativePath = str_replace($snippetsDir . '/', '', $file->getPathname());
            $key = 'email-templates/' . str_replace('.php', '', $relativePath);
            $snippets[$key] = $file->getPathname();
        }
    }

    return $snippets;
}

return getSnippets();