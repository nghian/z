<?php
/**
 * @var $this \yii\web\View
 * @var $model \common\models\ArticleComment
 */
use yii\helpers\Url;
use yii\helpers\Html;
use yii\timeago\TimeAgo;

?>
<div class="comment-avatar">
    <?= Html::a($model->user->getAvatarImage(['class' => 'img-circle'], ['s' => 48]), $model->user->url); ?>
</div>
<div class="comment-detail">
    <div class="comment-detail-header">
        <span>By </span> <?= $model->user->link; ?>
        <span>at</span>
        <a title="" rel="nofollow" id="comment<?= $key; ?>" href="#comment<?= $key; ?>"><?= TimeAgo::widget(['timestamp' => $model->created_at]); ?></a>
        <div class="pull-right">
            <?php if (Yii::$app->user->can('update', ['model' => $model, 'attribute' => 'user_id'])): ?>
                <?= Html::button(Html::tag('span', null, ['class' => 'psi-edit']), [
                    'title' => 'Edit comment',
                    'class' => 'btn btn-xs btn-default',
                    'data-toggle' => 'comment-edit',
                    'data-key' => $key,
                    'data-pjax' => 'false',
                ]); ?>
            <?php endif; ?>
            <?php if (Yii::$app->user->can('delete', ['model' => $model, 'attribute' => 'user_id'])): ?>
                <?= Html::button(Html::tag('span', null, ['class' => 'psi-bin']), [
                    'title' => 'Delete comment',
                    'class' => 'btn btn-xs btn-danger',
                    'data-toggle' => 'ajax',
                    'data-cache' => 'false',
                    'data-type' => 'POST',
                    'data-data-type' => 'json',
                    'data-data' => \yii\helpers\Json::encode(['id' => $key]),
                    'data-alert' => 'Are you sure to delete this comment?',
                    'data-url' => Url::to(['article/comment-delete']),
                    'data-pajax' => 'false'
                ]); ?>
            <?php endif; ?>
        </div>
    </div>
    <div data-key="<?= $key ?>" class="comment-detail-body"><?= $model->body ?></div>
    <?php if (Yii::$app->user->can('update', ['model' => $model, 'attribute' => 'user_id'])): ?>
        <div data-key="<?= $key ?>" class="comment-detail-tools" style="display: none">
            <?= Html::button('Cancel', ['class' => 'btn btn-xs btn-default', 'data-key' => $key, 'data-toggle' => 'comment-cancel']); ?>
            <?= Html::button('Update', ['class' => 'btn btn-xs btn-success', 'data-key' => $key, 'data-toggle' => 'comment-update']); ?>
        </div>
    <?php else: ?>
        <div class="comment-detail-tools">
            <?= $model->getLikeButton(); ?>
            <?= Html::button(Html::tag('span', null, ['class' => 'psi-warning']) . ' Report', [
                'class' => 'btn btn-xs btn-warning',
                'data-toggle' => 'ajax',
                'data-url' => Url::to(['article/comment-report', 'id' => $model->id])
            ]); ?>
        </div>
    <?php endif; ?>

</div>