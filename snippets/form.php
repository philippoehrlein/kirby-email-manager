<?php
use KirbyEmailManager\PageMethods\ContentWrapper;

// Prüfe, ob Block-Daten vorhanden sind
$blockContent = isset($block) && $block !== null ? $block->content()->toArray() : null;
$contentWrapper = new ContentWrapper($page, $blockContent);

// FormHandler ausführen, mit ContentWrapper
$formHandler = $page->form_handler($contentWrapper);

snippet('email-manager/form-builder', [
    'contentWrapper' => $contentWrapper,
    'alert' => $formHandler['alert'] ?? [],
    'data' => $formHandler['data'] ?? [],
]);