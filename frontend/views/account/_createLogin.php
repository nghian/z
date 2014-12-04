<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\CreateLoginForm */
/* @var $form ActiveForm */
$this->title = 'Create your account login';
$this->params['breadcrumbs'] = [
    ['label' => 'Account', 'url' => ['index']],
    $this->title
];
?>
<div class="account-createLogin">
    <p>In the account creation process we require you to create an account login form.</p>
    <p>Please fill out the following fields to create your account login:</p>
    <div class="row">
        <div class="col-lg-8 col-md-8">
            <?php $form = ActiveForm::begin(); ?>
            <?php if (empty($model->email)): ?>
                <?= $form->field($model, 'email') ?>
            <?php else: ?>
                <?= Html::activeHiddenInput($model, 'email'); ?>
            <?php endif; ?>
            <?= $form->field($model, 'username') ?>
            <?= $form->field($model, 'password')->passwordInput() ?>
            <?= $form->field($model, 'confirm')->passwordInput() ?>
            <div class="form-group">
                <?= Html::submitButton(Yii::t('app', 'Create'), ['class' => 'btn btn-primary']) ?>
            </div>
            <?php ActiveForm::end(); ?>

        </div>
        <!-- account-createLogin -->
    </div>
</div>

