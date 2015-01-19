<?php
/**
 * @var $model \common\models\ArticleItem
 * @var $this \yii\web\View
 */
use yii\helpers\Html;

$this->title = $model->title . " - Republish";
if ($model->category->parent) {
    $breadcrumbs = [
        ['label' => 'Article', 'url' => ['/article/index']],
        ['label' => $model->category->parent->title, 'url' => $model->category->parent->url],
        ['label' => $model->category->title, 'url' => $model->category->url],
        ['label' => $model->title, 'url' => $model->url],
        'Republish'
    ];
} else {
    $breadcrumbs = [
        ['label' => 'Article', 'url' => ['/article/index']],
        ['label' => $model->category->title, 'url' => $model->category->url],
        ['label' => $model->title, 'url' => $model->url],
        'Republish'
    ];
}
$this->params['breadcrumbs'] = $breadcrumbs;
?>
<h1 class="page-header"><?= Html::encode($model->title); ?></h1>
<?= Html::activeInput('text', $model, 'title', ['class' => 'form-control']); ?>
<?= Html::activeTextarea($model, 'summary', ['class' => 'form-control']); ?>
<?= Html::activeTextarea($model, 'body', ['class' => 'form-control']); ?>
<?= Html::activeTextarea($model, 'bio', ['class' => 'form-control']); ?>
