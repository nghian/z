<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model \common\models\ArticleComment */
/* @var $form ActiveForm */
?>
<?php if (Yii::$app->user->isGuest): ?>
    <p>Please, <?= Html::a('login', ['/account/login', 'ref' => Yii::$app->request->absoluteUrl]); ?>
        or <?= Html::a('register', ['/account/signup']); ?> to post comments</p>
<?php else: ?>
    <?php $form = ActiveForm::begin(); ?>
    <div class="media">
        <a class="pull-left" href="<?= Url::to(Yii::$app->user->identity->url); ?>">
            <img class="media-object" src="<?= Yii::$app->user->identity->avatarUrl; ?>" style="max-width: 71px;"
                 alt="Thumbnail"/>
        </a>
        <div class="media-body">
            <?= $form->field($model, 'body', ['template' => "{input}"])->textarea(["placeholder" => 'Enter your comment ...']); ?>
            <?= Html::submitButton(Yii::t('app', 'Comment'), ['class' => 'btn btn-primary pull-right']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
<?php endif; ?>