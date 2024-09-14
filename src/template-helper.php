<?php



function getTemplateName($config, $folder) {
    if (isset($config['name'])) {
        $currentLang = kirby()->language()->code();
        if (is_array($config['name'])) {
            if (isset($config['name'][$currentLang])) {
                return $config['name'][$currentLang];
            } elseif (isset($config['name']['en'])) {
                return $config['name']['en'];
            } else {
                return reset($config['name']); // Erste verfügbare Sprache
            }
        } elseif (is_string($config['name'])) {
            return $config['name'];
        }
    }
    return ucfirst($folder) . ' Template';
}