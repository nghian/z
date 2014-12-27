<?php
/**
 * @var $this \yii\web\View
 * @var $model \frontend\models\AuthSignupForm
 */
use yii\widgets\ActiveForm;
use yii\helpers\Html;

$this->title = 'Authorize Sign Up';
?>
<div class="account-signup">
    <div class="row">
        <div class="col-lg-5 col-center">
            <?php $form = ActiveForm::begin(); ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><?= Html::encode($this->title) ?></h3>
                </div>
                <div class="panel-body">
                    <?php if ($model->isEmailRequired()): ?>
                        <?= $form->field($model, 'email') ?>
                    <?php endif; ?>
                    <?= $form->field($model, 'username') ?>
                    <?= $form->field($model, 'password')->passwordInput() ?>
                    <?= $form->field($model, 'confirm')->passwordInput() ?>
                </div>
                <div class="panel-footer"><?= Html::submitButton(Yii::t('app', 'Sign Up & Sign In'), ['class' => 'btn btn-default']) ?></div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>