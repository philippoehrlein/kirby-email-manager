<?php foreach ($options as $key => $label): ?>
    <div class="checkbox-option">
        <input type="checkbox" id="<?= $key ?>" name="<?= $field->name() ?>[]" value="<?= $key ?>" <?= in_array($key, (array) $field->value()) ? 'checked' : '' ?>>
        <label for="<?= $key ?>"><?= $label ?></label>
    </div>
<?php endforeach; ?>