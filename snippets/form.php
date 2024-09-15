<?php 
// FormHandler ausführen
$formHandler = $page->form_handler();

snippet('email-manager/form-builder', [
    'alert' => $formHandler['alert'] ?? [],
    'data' => $formHandler['data'] ?? [],
]); ?>
