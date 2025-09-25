<?php
use KirbyEmailManager\Helpers\FormHelper;

$config = $config ?? [];
$alert = $alert ?? [];
?>

<?php if (isset($alert['message']) && $alert['type'] === 'error' && kirby()->request()->is('POST')): ?>
    <p class="<?= FormHelper::getClassName('error', $config, 'error') ?>"><?= $alert['message'] ?></p>
<?php elseif (isset($alert['message']) && $alert['type'] === 'warning' && kirby()->request()->is('POST')): ?>
    <p class="<?= FormHelper::getClassName('error', $config, 'warning') ?>"><?= $alert['message'] ?></p>
<?php endif ?>