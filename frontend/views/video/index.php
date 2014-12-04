<?php
/**
 * @var $this yii\web\View
 * @var $dataProvider \yii\data\ActiveDataProvider
 */
use yii\widgets\ListView;
use yii\widgets\Pjax;

?>
<div class="video-index">
    <div class="row">
        <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
            <?php Pjax::begin() ?>
            <?= ListView::widget([
                'dataProvider' => $dataProvider,
                'itemView' => 'items/index'
            ]); ?>
            <?php Pjax::end(); ?>
        </div>
    </div>
</div>
