<?php
/**
 * @var $this \yii\web\View
 * @var $model \common\models\UserEmail
 */
use yii\widgets\ActiveForm;
use yii\helpers\Html;

?>
<?php $form = ActiveForm::begin(); ?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Add new Email</h3>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
                <?= $form->field($model, 'email'); ?>
            </div>
        </div>
    </div>
    <div class="panel-footer">
        <?= Html::submitButton('Add', ['class' => 'btn btn-default']); ?>
    </div>
</div>
<?php ActiveForm::end(); ?>
