<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\PasswordResetRequestForm */

$this->title = 'Request password reset';
?>
<div class="account-request-password vertical-center">
    <div class="row">
        <div class="col-lg-4 col-md-5 col-sm-6 col-center">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Request password reset</h3>
                </div>
                <div class="panel-body">
                    <?php $form = ActiveForm::begin(['id' => 'password-request-form']); ?>
                    <?= $form->field($model, 'login') ?>
                    <div class="form-group">
                        <?= Html::submitButton('Send', ['class' => 'btn btn-primary']) ?>
                    </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
