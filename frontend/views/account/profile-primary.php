<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\selectize\Selectize;

?>
<?php $form = ActiveForm::begin([

]); ?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Change primary profile</h3>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                <?= Selectize::widget([
                    'name' => 'id',
                    'value' => Yii::$app->user->identity->userProfile->id,
                    'items' => $profiles
                ]); ?>
            </div>
        </div>
    </div>
    <div class="panel-footer">
        <?= Html::submitButton(Yii::t('app', 'Primary'), ['class' => 'btn btn-success']) ?>
    </div>
</div>
<?php ActiveForm::end(); ?>

