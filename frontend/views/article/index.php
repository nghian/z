<?php
/**
 * @var $this yii\web\View
 * @var $dataProvider \yii\data\ActiveDataProvider
 * @var $category \common\models\ArticleCategory[]
 */
use yii\widgets\ListView;
use yii\helpers\Html;

$this->title = "Articles";
$this->params['breadcrumbs'][] = $this->title;
?>
<h1 class="page-header"><?= Html::encode($this->title); ?></h1>
<div class="row">
    <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
        <div class="article-category">
            <h2 class="page-header">Article Category</h2>

            <div class="article-category-body">
                <ul class="row">
                    <?php foreach ($category as $cat): ?>
                        <li class="col-lg-4 col-sm-4 col-md-4"><?= Html::a($cat->title, $cat->url); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <div class="article-recent">
            <h2 class="page-header">Recent Article</h2>
            <?= ListView::widget([
                'dataProvider' => $dataProvider,
                'itemView' => 'items/index',
                'layout' => "{items}"
            ]); ?>
        </div>
    </div>
    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
        <div class="well text-center">
            <a class="btn btn-default btn-primary" href="#">Create new an Article</a>
        </div>
        <div class="tags-cloud">
            <a class="tag" href="#">tag1</a>
            <a class="tag" href="#">tag2</a>
            <a class="tag" href="#">tag3</a>
            <a class="tag" href="#">tag4</a>
            <a class="tag" href="#">tag5</a>
        </div>
    </div>
</div>


