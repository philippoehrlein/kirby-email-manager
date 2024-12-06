<?php
use KirbyEmailManager\PageMethods\ContentWrapper;

$blockContent = isset($block) && $block !== null ? $block->content()->toArray() : null;
$contentWrapper = new ContentWrapper($page, $blockContent);

$formHandler = $page->form_handler($contentWrapper);

$session = kirby()->session();
$successMessage = $session->get('form.success');
if ($successMessage): 
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