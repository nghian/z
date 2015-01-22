<?php
/**
 * @var $this \yii\web\View
 * @var $dataProvider \yii\data\ActiveDataProvider
 */
use yii\widgets\ListView;

?>
<div class="sidebar latest-comments">
    <div class="sidebar-header">
        <h3>All category</h3>
    </div>
    <div class="sidebar-body">
        <?= ListView::widget([
            'dataProvider' => $dataProvider,
            'options' => [
                'tag' => 'ul',
            ],
            'itemOptions' => [
                'tag' => 'li',
            ],
            'layout' => '{items}',
            'itemView' => function ($model, $key, $index, $widget) {
                return $model->user->link . ' on ' . $model->article->getLink([], ['#' => 'comment' . $model->id]);
            }
        ]); ?>
    </div>

</div>