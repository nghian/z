<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\bootstrap\BootstrapPluginAsset;
BootstrapPluginAsset::register($this);
/* @var $this yii\web\View */
/* @var $model \common\models\UserLogin */
/* @var $form ActiveForm */
$this->registerJs('$("[data-toggle=\'popover\']").popover();');
?>
<div class="account-change-username">
    <?php $form = ActiveForm::begin([
        'id'=>'change-username'
    ]); ?>
    <div class="panel panel-warning">
        <div class="panel-heading">
            <h3 class="panel-title">
                <span class="glyphicon glyphicon-warning-sign"></span>
                Change Username
            </h3>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
                    <?= $form->field($model, 'username')->textInput([
                        'data-toggle'=>'popover',
                        'title'=>'Unexpected bad things will happen',
                        'data-content'=>'* We will not redirects for your old profile page.<br/>* We will not redirects for pages sites.',
                        'data-placement'=>"left",
                        'data-trigger'=>'hover focus',
                        'data-html'=>'true',
                    ]) ?>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <?= Html::submitButton(Yii::t('app', 'Change'), [
                'class' => 'btn btn-primary'
            ]) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div><!-- account-change-username -->
