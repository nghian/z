<?php
/**
 * @var $this yii\web\View
 * @var $model \common\models\User
 */
use yii\helpers\Html;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\widgets\Pjax;
use yii\helpers\Url;

$this->title = $model->name . ' - Profile';
$this->params['page-heading'] = $model->name;
$this->params['breadcrumbs'] = [
    ['label' => 'Account', 'url' => ['index']],
    $model->name
];
?>
<div class="manager-article">
    <h2>Manager Articles</h2>
    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => new ActiveDataProvider([
            'query' => $model->getArticleItems()
        ]),
        'layout' => "{items}\n{pager}",
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn'
            ],
            [
                'class' => 'yii\grid\DataColumn',
                'attribute' => 'link',
                'format' => 'html',
                'label' => 'Title',
            ],
            [
                'class' => 'yii\grid\DataColumn',
                'attribute' => 'created_at',
                'format' => 'dateTime',
                'label' => 'Created',
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'controller' => 'article',
                'template' => "{update} {delete}",
                'urlCreator' => function ($action, $model, $key, $index) {
                    return \yii\helpers\Url::toRoute(['/article/' . $action, 'id' => $model->id, 'slug' => $model->slug]);
                }
            ]
        ]
    ]); ?>
    <?php Pjax::end(); ?>
</div>
<div class="manager-friend">
    <h2>Manager Friends</h2>
    <?= GridView::widget([
        'dataProvider' => new ActiveDataProvider([
            'query' => $model->getUserFriends()
        ]),
        'layout' => "{items}\n{pager}",
        'columns' => [
            'id',
            [
                'class' => 'yii\grid\DataColumn',
                'attribute' => 'link',
                'format' => 'html',
                'label' => 'Title',
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'controller' => 'article',
                'urlCreator' => function ($action, $model, $key, $index) {
                    return Url::toRoute(['/article/' . $action, 'id' => $model->id, 'slug' => $model->userProfile->slug]);
                }
            ]
        ]
    ]); ?>
</div>


