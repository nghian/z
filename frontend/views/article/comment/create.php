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
        <p>Please, <?= Html::a('login', ['/account/login', 'ref' => Yii::$app->request->absoluteUrl]); ?> or <?= Html::a('register', ['/account/signup']); ?> to post comments</p>
    <?php else: ?>
        <?php $user = Yii::$app->user->identity; ?>
        <div class="comment-author">
            <?= Html::a($user->getAvatarImage(['class' => 'img-circle'], ['s' => 48]), $user->url); ?>
        </div>
        <div class="comment-form">
            <?php $form = ActiveForm::begin(); ?>
            <?= $form->field($model, 'body', ['template' => "{input}\n{error}"])->widget(Redactor::className(), [
                'clientOptions' => [
                    'buttons' => ['html', 'bold', 'italic', 'underline', 'unorderedlist', 'orderedlist', 'image', 'file', 'link'],
                    'buttonSource' => true,
                    'imageUpload' => '/redactor/upload/image',
                    'fileUpload' => '/redactor/upload/file',
                    'imageManagerJson' => '/redactor/upload/imagejson',
                    'fileManagerJson' => '/redactor/upload/filejson',
                    'plugins' => ['filemanager', 'imagemanager', 'fullscreen']
                ]
            ]); ?>
            <?= Html::submitButton('Comment', ['class' => 'btn btn-default']) ?>
            <?php ActiveForm::end(); ?>
        </div>
    <?php endif; ?>
</div>