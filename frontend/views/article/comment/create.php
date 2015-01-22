<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\redactor\widgets\Redactor;

/* @var $this yii\web\View */
/* @var $model \common\models\ArticleComment */
/* @var $form ActiveForm */
?>
<div name="discussion-form" id="discussion-form" class="discussion-form">
    <?php if (!Yii::$app->user->can('create')): ?>
        <p class="text-center">Please <?= Html::a('login', ['/account/login', 'ref' => Yii::$app->request->absoluteUrl]); ?> to leave your comment</p>
    <?php else: ?>
        <div class="comment-form">
            <?php $form = ActiveForm::begin(); ?>
            <?= $form->field($model, 'body')->widget(Redactor::className(), [
                'clientOptions' => [
                    'buttons' => ['html', 'bold', 'italic', 'underline', 'unorderedlist', 'orderedlist', 'image', 'file', 'link'],
                    'buttonSource' => true,
                    'imageUpload' => '/redactor/upload/image',
                    'fileUpload' => '/redactor/upload/file',
                    'imageManagerJson' => '/redactor/upload/imagejson',
                    'fileManagerJson' => '/redactor/upload/filejson',
                    'plugins' => ['filemanager', 'imagemanager', 'fullscreen']
                ]
            ])->label('New comment'); ?>
            <?= Html::submitButton('Comment', ['class' => 'btn btn-sm btn-default']) ?>
            <?php ActiveForm::end(); ?>
        </div>
    <?php endif; ?>
</div>