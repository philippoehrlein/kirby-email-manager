<div class="<?= $classActions ?>">
  <?php if ($resetButtonShow): ?>
    <button type="reset" tabindex="0" class="<?= $classButtonSecondary ?>"><?= $resetButtonText ?></button>
  <?php endif; ?>
  <input type="submit" tabindex="0" class="<?= $classButtonPrimary ?>" name="submit" value="<?= $sendButtonText ?>" />
</div>