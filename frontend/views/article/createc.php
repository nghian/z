<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \common\models\ArticleCategory */
/* @var $form ActiveForm */
?>
<div class="article-createc">

    <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'parent_id') ?>
        <?= $form->field($model, 'title') ?>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>

</div><!-- article-createc -->
