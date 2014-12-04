<?php
/**
 * @var $this \yii\web\View
 * @var $dataProvider \yii\data\ActiveDataProvider
 */
use yii\grid\GridView;

?>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        [
            'class' => 'yii\grid\SerialColumn'
        ],
        [
            'class' => 'yii\grid\DataColumn',
            'attribute' => 'link',
            'format' => 'Html',
            'label' => 'Article',
        ],
        [
            'class' => 'yii\grid\DataColumn',
            'attribute' => 'view_count',
            'format' => 'Html',
            'label' => 'Views',
        ]
    ],
    'layout' => "{items}\n{pager}",
    'tableOptions' => ['class' => 'table table-hover']
]); ?>