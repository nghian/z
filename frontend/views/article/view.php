<?php
/**
 * @var $this \yii\web\View
 * @var $model \common\models\ArticleItem
 */
use yii\helpers\Html;
use yii\timeago\TimeAgo;
use yii\widgets\ListView;

$this->title = $model->title;
if ($model->category->parent) {
    $breadcrumbs = [
        ['label' => 'Article', 'url' => ['/article/index']],
        ['label' => $model->category->parent->title, 'url' => $model->category->parent->url],
        ['label' => $model->category->title, 'url' => $model->category->url],
        $model->title
    ];
} else {
    $breadcrumbs = [
        ['label' => 'Article', 'url' => ['/article/index']],
        ['label' => $model->category->title, 'url' => $model->category->url],
        $model->title
    ];
}
$this->params['breadcrumbs'] = $breadcrumbs;
?>
<h1 class="page-header"><?= Html::encode($model->title) ?></h1>
<div class="row">
    <div class="col-sm-8 col-md-8 col-lg-8">
        <div class="article-detail">
            <div class="information">
                <span
                    class="glyphicon glyphicon-time"></span> <?= TimeAgo::widget(['timestamp' => $model->created_at]); ?>
                <span class="glyphicon glyphicon-eye-open"></span> <?= $model->view_count; ?> times
                <span class="glyphicon glyphicon-file"></span> <?= $model->word_count; ?> words
            </div>
            <div class="body"><?= $model->body; ?></div>
        </div>
        <div class="article-comment">
            <h2>Comments</h2>
            <?= $this->context->comment($model->id); ?>
            <?= ListView::widget([
                'dataProvider' => $commentProvider,
                'itemView' => 'items/comment',
                'itemOptions' => [
                    'class' => 'media comment-item'
                ],
                'layout' => "{items}\n{pager}"
            ]); ?>
        </div>
    </div>
    <div class="col-sm-4 col-md-4 col-lg-4">

    </div>
</div>
