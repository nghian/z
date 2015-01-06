<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\selectize\Selectize;
use yii\redactor\widgets\Redactor;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model \common\models\ArticleItem */
/* @var $form ActiveForm */
$subc = <<<JS
    function (value) {
        $("#articleitem-cid").val(value.target.value);
        var subc = $("#articleitem-subcategory_id")[0].selectize;
        subc.disable();
        subc.clearOptions();
        subc.load(function (callback) {
        $.ajax({
            url: "/article/sub-category?id=" + value.target.value,
             success: function (res) {
                subc.enable();
                callback(res);
            },
            error: function ()
            {
                callback();
            }});
        });
    }
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
                    "change" => new \yii\web\JsExpression($subc)
                ]
            ]) ?>
            <?= $form->field($model, 'subcategory_id')->widget(Selectize::className(), [
                'items' => $subcategory,
                'options' => [
                    'placeholder' => "Select a subcategory"
                ],
                'clientOptions' => [
                    'create' => true,
                    "valueField" => "id",
                    "labelField" => "title"
                ],
                'clientEvents' => [
                    'change' => 'function(value){if(value.target.value==0){$("#articleitem-cid").val($("#articleitem-category_id")[0].value);}else{$("#articleitem-cid").val(value.target.value);}}'
                ]
            ]) ?>
            <?= $form->field($model, 'cid', ['template' => "{input}"])->hiddenInput(); ?>
            <?= $form->field($model, 'title') ?>
            <?= $form->field($model, 'summary')->textarea(['rows' => 4]) ?>
            <?= $form->field($model, 'body')->widget(Redactor::className()) ?>
            <?= $form->field($model, 'bio')->textarea(['rows' => 4]) ?>
            <?= $form->field($model, 'list_tags')->widget(Selectize::className(), ['clientOptions' => ['plugins' => ['remove_button', 'drag_drop'],
                'maxItems' => 5]]) ?>
            <div class="form-group">
                <?= Html::submitButton(Yii::t('app', 'Create'), ['class' => 'btn btn-primary']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div><!-- article-ask -->
