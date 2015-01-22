<?php
/**
 * @var $this \yii\web\View
 */
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

?>
<div class="sidebar search">
    <div class="sidebar-header">
        <h3>Search articles</h3>
    </div>
    <div class="sidebar-body">
        <?php $form = ActiveForm::begin([
            'action' => Url::to(['article/search']),
            'method' => 'get',
            'enableClientScript' => false,
        ]); ?>
        <div class="input-group">
            <?= Html::textInput('q', null, ['class' => 'form-control', 'placeholder' => 'Enter keywords to search articles...']); ?>
            <span class="input-group-btn">
                <?= Html::submitButton(null, ['class' => 'btn btn-default psi-search']); ?>
            </span>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
