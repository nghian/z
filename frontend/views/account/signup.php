<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\captcha\Captcha;
use yii\authclient\widgets\AuthChoice;

/* @var $this yii\web\View */
/* @var $model frontend\models\SignupForm */
/* @var $form ActiveForm */
$this->title = 'Join phpSyntax';

?>
<div class="account-signup">
    <h1>Join phpsyntax</h1>

    <div class="row">
        <div class="col-lg-5 col-md-6 col-sm-8">
            <div class="well">
                <?php $form = ActiveForm::begin(); ?>
                <legend>Create your personal account</legend>
                <?= $form->field($model, 'name') ?>
                <?= $form->field($model, 'username') ?>
                <?= $form->field($model, 'email') ?>
                <?= $form->field($model, 'password')->passwordInput() ?>
                <?= $form->field($model, 'confirm')->passwordInput() ?>
                <?= $form->field($model, 'verifyCode')->widget(Captcha::className(), [
                    'template' => '<div class="row"><div class="col-lg-3 col-md-3">{image}</div><div class="col-lg-9 col-md-9">{input}</div></div>',
                ]) ?>
                <?= $form->field($model, 'accept')->checkbox() ?>
                <div class="form-group">
                    <?= Html::submitButton(Html::tag('i', '', ['class' => 'fa fa-plus-circle']) . '&nbsp;Sign Up', ['class' => 'btn btn-primary']) ?>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

