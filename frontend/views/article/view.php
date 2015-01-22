<?php
/**
 * @var $this \yii\web\View
 * @var $model \common\models\ArticleItem
 * @var $newComment string
 * @var $commentProvider \yii\data\ActiveDataProvider
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
    <div class="col-sm-8">
        <div class="article">
            <div class="article-info">
                <span class="psi-schedule"></span> Published at <?= TimeAgo::widget(['timestamp' => $model->published_at]); ?>
                <span class="psi-eye"></span> <?= $model->view_count; ?> views time
                <span class="psi-file-word"></span> <?= $model->word_count; ?> words count
            </div>
            <div class="article-summary"><?= $model->summary; ?></div>
            <div class="article-body"><?= $model->body; ?></div>
            <div class="article-tools">
                <?php if (!Yii::$app->user->can('update', ['model' => $model])): ?>
                    <?= $model->getLikeButton(); ?>
                    <?= Html::a('<span class="psi-warning"></span> Report', ['/article/report', 'id' => $model->id, 'slug' => $model->slug], ['class' => 'btn btn-xs btn-default']); ?>
                <?php endif; ?>
                <?= Html::a('<span class="psi-print"></span> Print', ['/article/print', 'id' => $model->id, 'slug' => $model->slug], ['class' => 'btn btn-xs btn-default']); ?>
                <?= Html::a('<span class="psi-publish"></span> Publish', ['/article/publish', 'id' => $model->id, 'slug' => $model->slug], ['class' => 'btn btn-xs btn-default']); ?>
                <?= Html::a('<span class="psi-email"></span> Email', ['/article/email', 'id' => $model->id, 'slug' => $model->slug], ['class' => 'btn btn-xs btn-default']); ?>
                <?= Html::a('<span class="psi-share"></span> Share', ['/article/share', 'id' => $model->id, 'slug' => $model->slug], ['class' => 'btn btn-xs btn-default']); ?>
            </div>
            <div class="author article-author">
                <div class="author-avatar">
                    <?= $model->user->getAvatarImage([], ['s' => 96]); ?>
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
                        <?= Button::widget(['controller' => 'follow',
                            'model' => $model->user,
                            'labelIcon' => 'psi-user-check']); ?>
                        <?= Button::widget(['controller' => 'friend',
                            'model' => $model->user,]); ?>
                    </div>
                </div>
            </div>
            <?php if ($model->getTags()->count()): ?>
                <div class="tags article-tags">
                    <?php foreach ($model->tags as $tag): ?>
                        <?= $tag->link; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="discussion article-comment">
            <div class="discussion-header">
                <h3><span class="psi-comment"></span> Comments</h3>
            </div>
            <div class="discussion-body">
                <?php \yii\widgets\Pjax::begin(['timeout' => 2000]); ?>
                <?= ListView::widget(['dataProvider' => $commentProvider,
                    'itemView' => 'comment/item',
                    'itemOptions' => ['class' => 'comment'],
                    'layout' => "{items}\n{pager}",
                    'options' => ['class' => 'comments'],
                    'emptyText' => 'No comments found']); ?>
                <?php \yii\widgets\Pjax::end(); ?>
                <?= $newComment; ?>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <?= $this->context->createButton(); ?>
        <?= $this->context->searchForm(); ?>
        <?= $this->context->listCategory($model->cid); ?>
        <?= $this->context->relatedArticles($model->id, $model->title); ?>
        <?=$this->context->latestComments();?>
    </div>
</div>