<?php
/**
 * @var $this \yii\web\View
 */
$this->title = 'Account Settings';
?>
<?= $this->context->settingPassword(); ?>
<?= $this->context->settingUsername(); ?>
<?= $this->context->settingDelete(); ?>