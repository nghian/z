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
        <span>at</span> <?= TimeAgo::widget(['timestamp' => $model->created_at]); ?>
        <div class="pull-right">
            <?php if (Yii::$app->user->can('update', ['model' => $model, 'attribute' => 'user_id'])): ?>
                <button title="Edit comment" class="comment-edit btn btn-xs btn-default" data-action="/article/comment-update?id=<?=$model->id;?>" href="#discussion-form" data-pjax="0"><i class="psi-edit"></i></button>
            <?php endif; ?>
            <?php if (Yii::$app->user->can('delete', ['model' => $model, 'attribute' => 'user_id'])): ?>
                <button title="Delete comment" class="comment-delete btn btn-xs btn-danger" data-confirm="Are you sure to delete this comment?" data-action="/article/comment-delete?id=<?= $model->id; ?>" data-pjax="0"><i class="psi-bin"></i></button>
            <?php endif; ?>
        </div>
    </div>
    <div class="comment-detail-body"><?= $model->body ?></div>
    <div class="comment-detail-tools">
        <a class="btn btn-xs btn-success" href="#"><span class="psi-thumb-up"></span> Like</a>
        <a class="btn btn-xs btn-warning" href="#"><span class="psi-warning"></span> Report</a>
    </div>
</div>