<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use frontend\widgets\AuthChoice;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model frontend\models\LoginForm */
$this->title = "Login - phpSyntax";
?>
<div class="account-login vertical-center">
    <div class="row">
        <div class="col-lg-4 col-md-5 col-sm-6 col-center">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Sign In</h3>
                </div>
                <div class="panel-body">
                    <?php $auth = AuthChoice::begin([
                        'clientCollection' => 'oauth',
                        'clientIdGetParamName' => 'client',
                        'baseAuthUrl' => ['account/authorize']
                    ]) ?>
                    <ul class="auth-clients">
                        <?php foreach ($auth->getClients() as $client): ?>
                            <li class="auth-client">
                                <?= Html::a(Html::tag('span', null, ['class' => 'psi psi-' . $client->id]), $auth->createClientUrl($client), ['class' => 'btn btn-social-icon btn-' . $client->id]); ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php AuthChoice::end(); ?>
                </div>
                <div class="panel-body">
                    <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
                    <?= $form->field($model, 'login', ['template' => '<div class="input-group"><span class="input-group-addon"><i class="psi-person"></i></span>{input}</div>{error}'])->textInput(['placeholder' => 'Username or Email']) ?>
                    <?= $form->field($model, 'password', ['template' => '<div class="input-group"><span class="input-group-addon"><i class="psi-key"></i></span>{input}</div>{error}'])->passwordInput(['placeholder' => 'Password']) ?>
                    <?= $form->field($model, 'rememberMe')->checkbox() ?>
                    <?= Html::submitButton('Login', ['class' => 'btn btn-default', 'name' => 'login-button']) ?>
                    <a class="btn btn-link" href="/account/request-password" title="Forgot your password">Reset password</a>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
