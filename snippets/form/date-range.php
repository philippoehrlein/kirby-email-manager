<div class="<?= $field->className() ?>">
    <input type="date" <?= $field->attr('start') ?> name="<?= $field->name() ?>[start]" value="<?= $field->value('start') ?>" />
    <input type="date" <?= $field->attr('end') ?> name="<?= $field->name() ?>[end]" value="<?= $field->value('end') ?>" />
</div>