<?php
/**
 * @var $this \yii\web\View
 * @var $model \frontend\models\AuthSignupForm
 */
use yii\widgets\ActiveForm;
use yii\helpers\Html;

$this->title = 'Authorize SignUp';
?>
<div class="account-signup">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-lg-5">
            Please fill out the following fields to create account and login:
            <?php $form = ActiveForm::begin(); ?>
            <?php if ($model->isAttributeRequired('email')): ?>
                <?= $form->field($model, 'email') ?>
            <?php endif; ?>
            <?= $form->field($model, 'username') ?>
            <?= $form->field($model, 'password')->passwordInput() ?>
            <?= $form->field($model, 'confirm')->passwordInput() ?>
            <div class="form-group">
                <?= Html::submitButton(Yii::t('app', 'Sign Up & Sign In'), ['class' => 'btn btn-primary']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>