<?php
/**
 * @var $this \yii\web\View
 * @var $model \common\models\User
 */
use common\widgets\Button;

?>
<div class="media">
    <div class="pull-left" style="margin:10px">
        <?= $model->getAvatarImage(['width' => 60], ['size' => 60]); ?>
    </div>
    <div class="media-body">
        <h4 class="media-heading"><?= $model->link; ?></h4>

        <p><?=$model->userProfile->inlineInfo;?></p>
        <?= Button::widget(['controller' => 'follow', 'model' => $model]) ?>
        <?= Button::widget(['controller' => 'friend', 'model' => $model]) ?>
    </div>
</div>
