<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\selectize\Selectize;
use yii\redactor\widgets\Redactor;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model \common\models\ArticleItem */
/* @var $form ActiveForm */
/* @var $category array */
/* @var  $subCategory array */
$loadCate = <<<JS
function(value) {jQuery("#articleitem-cid").val(value.target.value); var subc = jQuery("#articleitem-subcategory_id").get(0).selectize; subc.disable(); subc.clearOptions(); subc.load(function (callback) { jQuery.ajax({url: "/article/sub-category?id=" + value.target.value, success: function (res) { subc.enable(); callback(res);}, error: function () {callback();}});});}
JS;

$loadSubCate = <<<JS
    function(value){if(value.target.value==0){jQuery("#articleitem-cid").val(jQuery("#articleitem-category_id").get(0).value);}else{jQuery("#articleitem-cid").val(value.target.value);}}
JS;


$this->title = "Create new Article";
$this->params['breadcrumbs'] = [
    ['label' => 'Articles', 'url' => ['article/index']],
    $this->title
];
?>
<div class="article-create">
    <h1><?= Html::encode($this->title); ?></h1>

    <p>Please fill out the following fields to create new article:</p>

    <div class="row">
        <div class="col-lg-8 col-md-8">
            <?php $form = ActiveForm::begin(); ?>
            <?= $form->field($model, 'category_id')->widget(Selectize::className(), [
                'items' => $category,
                'options' => [
                    'placeholder' => "Select a Category"
                ],
                'clientEvents' => [
                    "change" => new JsExpression($loadCate)
                ]
            ]) ?>
            <?= $form->field($model, 'subcategory_id')->widget(Selectize::className(), [
                'items' => $subCategory,
                'options' => [
                    'placeholder' => "Select a subcategory"
                ],
                'clientOptions' => [
                    'create' => true,
                    "valueField" => "id",
                    "labelField" => "title"
                ],
                'clientEvents' => [
                    'change' => new JsExpression($loadSubCate)
                ]
            ]) ?>
            <?= $form->field($model, 'cid', ['template' => "{input}"])->hiddenInput(); ?>
            <?= $form->field($model, 'title') ?>
            <?= $form->field($model, 'summary')->widget(Redactor::className(), [
                'clientOptions' => [
                    'buttons' => ['html', 'bold', 'italic', 'link'],
                    'buttonSource' => true,
                    'allowedTags' => ['b', 'i', 'strong', 'em', 'a'],
                    'limiter' => 300,
                    'plugins' => ['limiter'],
                    'linkNofollow' => true
                ]
            ]) ?>
            <?= $form->field($model, 'body')->widget(Redactor::className(), [
                'clientOptions' => [
                    'buttonSource' => true,
                    'buttons' => ['html', 'formatting', 'bold', 'italic', 'deleted', 'unorderedlist', 'orderedlist', 'image', 'file', 'link', 'alignment'],
                    'formatting' => ['p', 'h2', 'h3', 'pre'],
                    'clipboardUploadUrl' => 'redactor/upload/clipboard',
                    'imageUpload' => '/redactor/upload/image',
                    'imageManagerJson' => '/redactor/upload/imagejson',
                    'imageFloatMargin' => '20px',
                    'fileUpload' => '/redactor/upload/file',
                    'fileManagerJson' => '/redactor/upload/filejson',
                    'linkNofollow' => true,
                    'plugins' => ['imagemanager', 'filemanager', 'video', 'table', 'fullscreen']
                ]
            ]) ?>
            <?= $form->field($model, 'bio')->widget(Redactor::className(), [
                'clientOptions' => [
                    'buttonSource' => true,
                    'buttons' => ['html', 'bold', 'italic', 'link'],
                    'allowedTags' => ['b', 'i', 'strong', 'em'],
                    'linkNofollow' => true
                ]]) ?>
            <?= $form->field($model, 'str_tags')->widget(Selectize::className(), [
                'clientOptions' => [
                    'plugins' => ['remove_button', 'drag_drop'],
                    'maxItems' => 5
                ]
            ]) ?>
            <div class="form-group">
                <?= Html::submitButton(Yii::t('app', 'Create'), ['class' => 'btn btn-primary']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
