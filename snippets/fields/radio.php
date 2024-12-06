<?php foreach ($options as $key => $label): ?>
    <div class="radio-option">
        <input type="radio" id="<?= $key ?>" name="<?= $field->name() ?>" value="<?= $key ?>" <?= $field->value() === $key ? 'checked' : '' ?>>
        <label for="<?= $key ?>"><?= $label ?></label>
    </div>
<?php endforeach; ?>