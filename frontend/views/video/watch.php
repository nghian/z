<?php
/**
 * @var $this \yii\web\View
 * @var $model \common\models\VideoItem
 */
use yii\jwplayer\JWPlayer;
use yii\helpers\Html;

$this->title = $model->title;
?>
<h1 class="page-header"><?= Html::encode($model->title) ?></h1>
<div class="row">
    <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
        <?= JWPlayer::widget([
            'options' => [
                'id' => 'player'
            ],
            'clientOptions' => [
                'file' => 'http://www.youtube.com/watch?v=' . $model->file
            ]
        ]); ?>
    </div>
</div>
