<?php
/**
 * @var $this \yii\web\View
 * @var $content string
 * @var $model \common\models\User
 */
use yii\helpers\Html;
use yii\bootstrap\Nav;
use common\widgets\Button;

$model = $this->context->getUser(Yii::$app->request->get('id'));
$this->title = $model->userProfile->name;
?>
<?php $this->beginContent('@app/views/layouts/main.php'); ?>
    <h1 class="page-header">
        <?= $model->getAvatarImage(['width' => 40]); ?>&nbsp;<?= Html::a($model->userLogin->username, $model->url); ?>
        (<?= $model->name; ?>)
        <div class="pull-right">
            <?= Button::widget(['controller' => 'follow', 'model' => $model, 'size' => 'sm']); ?>
            <?= Button::widget(['controller' => 'friend', 'model' => $model, 'size' => 'sm']); ?>
        </div>
    </h1>
    <div class="row">
        <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
            <?= Nav::widget([
                'options' => [
                    'class' => 'nav nav-tabs',
                ],
                'items' => [
                    ['label' => 'Articles', 'url' => ['/user/article', 'username' => $model->userLogin->username]],
                    ['label' => 'Friends', 'url' => ['/user/friend', 'username' => $model->userLogin->username]],
                    ['label' => 'Follower', 'url' => ['/user/follower', 'username' => $model->userLogin->username]],
                    ['label' => 'Following', 'url' => ['/user/following', 'username' => $model->userLogin->username]]
                ]
            ]); ?>
            <div class="tab-content prepend">
                <div role="tabpanel" class="tab-pane active">
                    <?= $content ?>
                </div>
            </div>
        </div>
        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
            <?= $this->context->getInfo(Yii::$app->request->get('id')); ?>
        </div>
    </div>
<?php $this->endContent(); ?>