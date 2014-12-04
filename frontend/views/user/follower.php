<?php
/**
 * @var $this \yii\web\View
 * @var $model \common\models\User
 */
use yii\widgets\ListView;

?>
<?= ListView::widget([
    'dataProvider' => $dataProvider,
    'itemView' => 'items/follower',
    'layout' => "{items}\n{pager}",
    'itemOptions' => [
        'class' => 'col-lg-6 col-md-6'
    ],
    'options' => ['class' => 'row']
]); ?>