<select <?= $field->attr() ?>>
    <?php foreach ($options as $key => $label): ?>
        <option value="<?= $key ?>" <?= $field->value() === $key ? 'selected' : '' ?>><?= $label ?></option>
    <?php endforeach; ?>
</select>