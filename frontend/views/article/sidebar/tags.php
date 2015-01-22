<div class="sidebar popular-tags">
    <div class="sidebar-header">
        <h3>Popular Tags</h3>
    </div>
    <div class="sidebar-body">
        <?= \yii\widgets\ListView::widget([
            'dataProvider' => $dataProvider,
            'options' => [
                'tag' => 'ul',
                'class' => 'tags'
            ],
            'itemOptions' => [
                'tag' => 'li',
                'class' => 'tag-item'
            ],
            'layout' => '{items}',
            'itemView' => function ($model, $key, $index, $widget) {
                return $model->link . \yii\helpers\Html::tag('span', $model->frequency, ['class' => 'badge']);
            }
        ]); ?>
    </div>

</div>