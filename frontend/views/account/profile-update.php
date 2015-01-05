<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\datepicker\DatePicker;
use yii\selectize\Selectize;
/* @var $this yii\web\View */
/* @var $model common\models\UserProfile */
/* @var $form ActiveForm */
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Update Profile</h3>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
                <?php $form = ActiveForm::begin([
                    'options' => [
                        'id' => 'profile-update',
                        'enctype' => 'multipart/form-data',
                        'role' => 'form'
                    ]
                ]); ?>
                <?= $form->field($model, 'name') ?>
                <?= $form->field($model, 'gender')->widget(Selectize::className(), [
                    'items' => [
                        null => 'Select your gender',
                        'male' => 'Male',
                        'female' => 'Female'
                    ]
                ]) ?>
                <?= $form->field($model, 'birthday')->widget(DatePicker::className(), [
                    'clientOptions' => ['format' => 'yyyy-mm-dd'],
                    'type' => 'component'
                ]) ?>
                <?= $form->field($model, 'location')->textInput(['placeholder' => 'Street, city, state, country']) ?>
                <?= $form->field($model, 'company') ?>
                <?= $form->field($model, 'website')->textInput(['placeholder' => 'http://']) ?>
                <?= $form->field($model, 'bio')->textarea(['placeholder' => 'About me']) ?>
                <div class="form-group">
                    <?= Html::submitButton(Yii::t('app', 'Change'), ['class' => 'btn btn-primary']) ?>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>

    </div>
</div><!-- update-profile -->