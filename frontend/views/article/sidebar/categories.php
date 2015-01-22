<?php
/**
 * @var $this \yii\web\View
 * @var $dataProvider \yii\data\ActiveDataProvider
 */
use yii\widgets\ListView;

?>
<div class="sidebar categories">
    <div class="sidebar-header">
        <h3>All category</h3>
    </div>
    <div class="sidebar-body">
        <?= ListView::widget([
            'dataProvider' => $dataProvider,
            'options' => [
                'tag' => 'ul',
                'class' => 'categories'
            ],
            'itemOptions' => [
                'tag' => 'li',
                'class' => 'category'
            ],
            'layout' => '{items}',
            'itemView' => function ($model, $key, $index, $widget) {
                return $model->link;
            }
        ]); ?>
    </div>

</div>