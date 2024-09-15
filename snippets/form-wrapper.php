<?php 
// FormHandler ausführen
$formHandler = $page->form_handler();

// Session abfragen
$session = kirby()->session();
$successMessage = $session->get('form.success');

if ($successMessage): 
    // Erfolgsmeldung anzeigen und danach aus der Session entfernen
    $session->remove('form.success');
    ?>
    <div class="alert alert--success">
        <h2><?= $successMessage['title'] ?></h2>
        <p><?= $successMessage['text'] ?></p>
    </div>
<?php else: ?>
    <?php snippet('email-manager/form-builder', [
        'alert' => $formHandler['alert'] ?? [],
        'data' => $formHandler['data'] ?? [],
    ]); ?>
<?php endif; ?>