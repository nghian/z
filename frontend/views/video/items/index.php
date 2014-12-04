<?php
/**
 * @var $this \yii\web\View
 * @var $model \common\models\VideoItem
 */
?>
<div class="video-item">
    <div class="thumb">
        <img src="https://i.ytimg.com/vi/<?= $model->file; ?>/mqdefault.jpg" alt=""/>
    </div>
    <div class="video-statistics">
        <span class="video-view-count">
            <i class="fa fa-eye"></i>&nbsp;<?= Yii::$app->formatter->asDecimal($model->view_count) ?>
        </span>
        <span class="video-duration">
            <i class="fa fa-clock-o"></i>&nbsp;<?= Yii::$app->formatter->asTime($model->duration, $model->duration > 3600 ? 'h:mm:ss' : 'mm:ss') ?>
        </span>
    </div>
    <h3 class="video-title"><?= $model->link ?></h3>
</div>