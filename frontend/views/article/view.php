<?php
/**
 * @var $this \yii\web\View
 * @var $model \common\models\ArticleItem
 */
use yii\helpers\Html;
use yii\timeago\TimeAgo;
use yii\widgets\ListView;
use common\widgets\Button;

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
    <div class="col-md-8">
        <div class="article">
            <div class="article-info">
                <span class="psi-schedule"></span> <?= TimeAgo::widget(['timestamp' => $model->created_at]); ?>
                <span class="psi-eye"></span> <?= $model->view_count; ?>
                <span class="psi-file-word"></span> <?= $model->word_count; ?>
            </div>
            <div class="article-summary"><?= $model->summary; ?></div>
            <div class="article-body"><?= $model->body; ?></div>
            <div class="article-tools">
                <?= Html::a('<span class="psi-thumb-up"></span> Like', ['/article/like', 'id' => $model->id, 'slug' => $model->slug], ['class' => 'btn btn-xs btn-default']); ?>
                <?= Html::a('<span class="psi-print"></span> Print', ['/article/like', 'id' => $model->id, 'slug' => $model->slug], ['class' => 'btn btn-xs btn-default']); ?>
                <?= Html::a('<span class="psi-notifications-on"></span> Subscribe', ['/article/subscribe', 'id' => $model->id, 'slug' => $model->slug], ['class' => 'btn btn-xs btn-default']); ?>
                <?= Html::a('<span class="psi-publish"></span> Publish', ['/article/publish', 'id' => $model->id, 'slug' => $model->slug], ['class' => 'btn btn-xs btn-default']); ?>
                <?= Html::a('<span class="psi-warning"></span> Report', ['/article/report', 'id' => $model->id, 'slug' => $model->slug], ['class' => 'btn btn-xs btn-default']); ?>
                <?= Html::a('<span class="psi-share"></span> Shares', ['/article/share', 'id' => $model->id, 'slug' => $model->slug], ['class' => 'btn btn-xs btn-default']); ?>
            </div>
            <div class="article-author">
                <div class="author-avatar">
                    <?= $model->user->avatarImage; ?>
                </div>
                <div class="author-box">
                    <div class="author-header">
                        <h3><?= $model->user->link ?></h3>
                    </div>
                    <div class="author-body">
                        <div class="author-about">
                            <?= $model->bio; ?>
                        </div>
                    </div>
                    <div class="author-footer">
                        <?= Button::widget([
                            'controller' => 'follow',
                            'model' => $model->user,
                            'labelIcon' => 'psi-user-check'
                        ]); ?>
                        <?= Button::widget([
                            'controller' => 'friend',
                            'model' => $model->user,
                        ]); ?>
                    </div>
                </div>
            </div>
            <div class="article-tags">
                <?=$model->tags;?>
            </div>
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
