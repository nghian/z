<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\captcha\Captcha;
use yii\authclient\widgets\AuthChoice;

/* @var $this yii\web\View */
/* @var $model frontend\models\SignupForm */
/* @var $form ActiveForm */
$this->title = 'Sign Up';

?>
<div class="account-signup">
    <h1 class="page-header"><span class="fa fa-plus-circle text-success"></span> <?= Html::encode($this->title) ?></h1>
    <div class="row">
        <div class="col-lg-5">
            Please fill out the following fields to login:
            <?php $form = ActiveForm::begin(); ?>
            <?= $form->field($model, 'first_name') ?>
            <?= $form->field($model, 'last_name') ?>
            <?= $form->field($model, 'username') ?>
            <?= $form->field($model, 'email') ?>
            <?= $form->field($model, 'password')->passwordInput() ?>
            <?= $form->field($model, 'confirm')->passwordInput() ?>
            <?= $form->field($model, 'verifyCode')->widget(Captcha::className(), [
                'template' => '<div class="row"><div class="col-lg-3 col-md-3">{image}</div><div class="col-lg-9 col-md-9">{input}</div></div>',
            ]) ?>
            <?= $form->field($model, 'accept')->checkbox() ?>
            <div class="form-group">
                <?= Html::submitButton(Html::tag('i','',['class'=>'fa fa-plus-circle']).'&nbsp;Sign Up', ['class' => 'btn btn-primary']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
        <div class="col-lg-7 col-md-7">
            <p>Sign up via social network:</p>
            <?php $auth = AuthChoice::begin([
                'clientCollection' => 'oauth',
                'clientIdGetParamName' => 'client',
                'baseAuthUrl' => ['account/authorize']
            ]) ?>
            <ul class="list-unstyled list-inline">
                <?php foreach ($auth->getClients() as $client): ?>
                    <li class="auth-client"><?= $auth->clientLink($client); ?></li>
                <?php endforeach; ?>
            </ul>
            <?php AuthChoice::end(); ?>
        </div>
    </div>
</div>

