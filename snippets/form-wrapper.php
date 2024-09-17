<?php
use KirbyEmailManager\PageMethods\ContentWrapper;

// Prüfe, ob Block-Daten vorhanden sind
$blockContent = isset($block) && $block !== null ? $block->content()->toArray() : null;
$contentWrapper = new ContentWrapper($page, $blockContent);

// FormHandler ausführen, mit ContentWrapper
$formHandler = $page->form_handler($contentWrapper);

// Session abfragen
$session = kirby()->session();
$successMessage = $session->get('form.success');
if ($successMessage): 
    // Erfolgsmeldung anzeigen und danach aus der Session entfernen
    $session->remove('form.success');
    ?>
    <div class="alert alert--success">
        <h2><?= $successMessage['title'] ?></h2>
        <?= $successMessage['text'] ?>
    </div>
<?php else: ?>
    <?php snippet('email-manager/form-builder', [
        'contentWrapper' => $contentWrapper,
        'alert' => $formHandler['alert'] ?? [],
        'data' => $formHandler['data'] ?? [],
    ]); ?>
<?php endif; ?>