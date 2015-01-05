<?php
/**
 * @var $this yii\web\View
 * @var $dataProvider \yii\data\ActiveDataProvider
 * @var $model \common\models\ArticleCategory
 */
use yii\widgets\ListView;
use yii\helpers\Html;

if ($model->parent) {
    $breadcrumbs = [
        ['label' => 'Articles', 'url' => ['/article/index']],
        ['label' => $model->parent->title, 'url' => $model->parent->url],
        $model->title
    ];
} else {
    $breadcrumbs = [
        ['label' => 'Articles', 'url' => ['/article/index']],
        $model->title
    ];
}
$this->title = $model->title . ' - Article Category';
$this->params['breadcrumbs'] = $breadcrumbs;
?>
<h1 class="page-header"><?= Html::encode($model->title); ?></h1>
<div class="row">
    <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
        <?php if (!empty($model->subs)): ?>
            <div class="article-category">
                <h2>Sub Category</h2>

                <div class="article-category-body">
                    <ul class="row">
                        <?php foreach ($model->subs as $cat): ?>
                            <li class="col-lg-4 col-sm-4 col-md-4"><?= Html::a($cat->title, $cat->url); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>
        <div class="article-recent">
            <h2>
                Recent Article
            </h2>
            <?= ListView::widget([
                'dataProvider' => $dataProvider,
                'itemView' => 'items/index',
                'layout' => "{items}\{pager}",
                'emptyText' => Yii::t('app','No articles found')
            ]); ?>
        </div>
    </div>
    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
        <div class="widget">
            <div class="tags-cloud">

            </div>
        </div>
    </div>
</div>