<?php foreach ($form->data() as $field => $value): ?>
    <?php if (empty($value)) continue; ?>
    <?php if (is_array($value)): ?>
        <?php $value = implode(', ', $value); ?>
    <?php endif; ?>
    - <?= ucfirst($field) ?>: <?= $value ?>
<?php endforeach; ?>