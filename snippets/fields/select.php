<select <?= $field->attr() ?>>
    <?php if (!$field->required): ?>
        <option class="placeholder" value=""><?= $field->placeholder ?></option>
    <?php endif; ?>
    
    <?php foreach ($options as $key => $label): ?>
        <option value="<?= $key ?>" <?= $field->value() === $key ? 'selected' : '' ?>><?= $label ?></option>
    <?php endforeach; ?>
</select>

