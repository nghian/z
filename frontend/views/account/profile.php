<?php
/* @var $this yii\web\View */
$this->title = 'Profile Settings';

?>
<div class="account-profile">
    <?= $this->context->profilePrimary(); ?>
    <?= $this->context->profilePicture(); ?>
    <?= $this->context->profileUpdate(); ?>
</div><!-- account-profile -->
