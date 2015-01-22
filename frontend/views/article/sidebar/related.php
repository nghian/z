<?php
/**
 * @var $this \yii\web\View
 * @var $dataProvider \yii\data\ActiveDataProvider
 */
use yii\widgets\ListView;

?>
<div class="sidebar related-articles">
    <div class="sidebar-header">
        <h3>Related Articles</h3>
    </div>
    <div class="sidebar-body">
        <?= ListView::widget([
            'dataProvider' => $dataProvider,
            'options' => [
                'tag' => 'ul',
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