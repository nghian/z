<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\authclient\widgets\AuthChoice;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model      frontend\models\LoginForm */

$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">
    <h1 class="page-header"><span class="fa fa-sign-in text-primary"></span> <?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-lg-4">
            <p>Please fill out the following fields to login:</p>
            <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
            <?= $form->field($model, 'login', ['template' => '<div class="input-group"><span class="input-group-addon"><i class="fa fa-at text-primary"></i></span>{input}</div>{error}'])->textInput(['placeholder' => 'Email or Username']) ?>
            <?= $form->field($model, 'password', ['template' => '<div class="input-group"><span class="input-group-addon"><i class="fa fa-key text-primary"></i></span>{input}</div>{error}'])->passwordInput(['placeholder' => 'Password']) ?>
            <?= $form->field($model, 'rememberMe')->checkbox() ?>
            <div style="color:#999;margin:1em 0">
                If you forgot your password you can <?= Html::a('reset it', ['account/request-password']) ?>.
                <br/>
                If your account unverified you can <?= Html::a('verify it', ['account/request-verify']) ?>.
            </div>
            <div class="form-group">
                <?= Html::submitButton('Login', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
        <div class="col-lg-7">
            <p>Login via social network:</p>
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
