<?php
/**
 * @var $this \yii\web\View
 * @var $model \common\models\ArticleItem
 */
use yii\helpers\Html;
?>
<h3><?=Html::a($model->title,$model->url);?></h3>
<div class="summary">
    <?=$model->summary;?>
</div>