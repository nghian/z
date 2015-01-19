<?php
/**
 * @var $model \common\models\ArticleComment
 * @var $this \yii\web\View
 */
use yii\widgets\ActiveForm;
use yii\redactor\widgets\Redactor;
use yii\helpers\Html;

?>
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
<?= Html::submitButton('Update', ['class' => 'btn btn-default']) ?>
<?php ActiveForm::end(); ?>