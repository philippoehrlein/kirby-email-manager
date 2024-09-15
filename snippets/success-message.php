<?php if ($page->successTitle() && $page->successText()): ?>
    <div class="alert alert--success">
        <h2><?= $page->successTitle() ?></h2>
        <p><?= $page->successText() ?></p>
    </div>
<?php endif; ?>