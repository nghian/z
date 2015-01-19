<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\PasswordResetForm */
/* @var $form ActiveForm */
$this->title = 'Create a new login password';
?>
<div class="account-passwordReset">
    <h1><?= Html::encode($this->title) ?></h1>
    <p>Please fill out your new password and confirm this to create new your login password.</p>
    <div class="row">
        <div class="col-lg-5 col-md-5">
            <?php $form = ActiveForm::begin(); ?>
            <?= $form->field($model, 'password')->passwordInput() ?>
            <?= $form->field($model, 'password_repeat')->passwordInput() ?>
            <div class="form-group">
                <?= Html::submitButton(Yii::t('app', 'Create'), ['class' => 'btn btn-primary']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
        <!-- account-passwordReset -->
    </div>
</div>

