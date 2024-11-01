<div class="<?= $field->className() ?>">
    <input type="date" <?= $field->attr() ?> name="<?= $field->name() ?>[start]" value="<?= $field->value('start') ?>" />
    <input type="date" <?= $field->attr() ?> name="<?= $field->name() ?>[end]" value="<?= $field->value('end') ?>" />
</div>