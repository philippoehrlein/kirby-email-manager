<ul style="list-style-type: none; padding-left: 0;">
    <?php foreach ($form->data() as $field => $value): ?>
        <?php if (empty($value)) continue; ?>
        <?php if (is_array($value)): ?>
            <?php $value = implode(', ', $value); ?>
        <?php endif; ?>
        <li style="margin-bottom: 10px;"><strong style="font-weight: bold;"><?= ucfirst($field) ?>:</strong> <?= $value ?></li>
    <?php endforeach; ?>
</ul>