<?php
/**
 * @var $this \yii\web\View
 * @var $model \common\models\ArticleComment
 */
use yii\helpers\Url;

?>

<a class="pull-left" href="<?= Url::to($model->user->url); ?>">
    <?= $model->user->getAvatarImage(['style' => 'max-width:64px']); ?>
</a>
<div class="media-body">
    <h4 class="media-heading"><?= $model->user->link; ?>
        <small>at <?= Yii::$app->formatter->asDatetime($model->created_at); ?></small>
    </h4>
    <div><?= $model->body ?></div>
</div>

