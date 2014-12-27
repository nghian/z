<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use frontend\assets\FontAwesomeAsset;
use yii\flash\Alert;

/* @var $this \yii\web\View */
/* @var $content string */
//FontAwesomeAsset::register($this);
AppAsset::register($this);

?>
<?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
    </head>
    <body>
    <?php $this->beginBody() ?>
    <div class="wrapper">
        <?php
        NavBar::begin([
            'brandLabel' => Html::tag('span', null, ['class' => 'psi-brand']),
            'brandUrl' => Yii::$app->homeUrl,
            'options' => [
                'class' => 'navbar navbar-ps navbar-static-top',
            ],
        ]);
        echo Nav::widget([
            'options' => ['class' => 'navbar-nav'],
            'encodeLabels' => false,
            'items' => [
                ['label' => '<span class="psi-file-text"></span> Tutorials', 'url' => ['article/index']],
                ['label' => '<span class="psi-question-answer"></span> Q & A', 'url' => ['qa/index']],
            ],
        ]);
        $menuItems = [];
        if (Yii::$app->user->isGuest) {
            $menuItems[] = ['label' => Html::tag('span', null, ['class' => 'psi-person-plus']) . ' Sign Up', 'url' => ['/account/signup']];
            $menuItems[] = ['label' => Html::tag('span', null, ['class' => 'psi-lock-open']) . ' Sign In', 'url' => ['/account/login']];
        } else {
            $menuItems[] = [
                'label' => Html::tag('span', null, ['class' => 'psi-person']) . ' ' . Yii::$app->user->identity->name,
                'url' => Yii::$app->user->identity->url
            ];
            $menuItems[] = [
                'label' => Html::tag('span', null, ['class' => 'psi-notifications-none']),
                'url' => ['#']
            ];
            $menuItems[] = [
                'label' => Html::tag('span', null, ['class' => 'psi-settings']),
                'url' => ['/account/profile']
            ];
            $menuItems[] = [
                'label' => Html::tag('span', null, ['class' => 'psi-exit']),
                'url' => ['/account/logout'],
                'linkOptions' => ['data-method' => 'post']
            ];
        }
        echo Nav::widget([
            'options' => ['class' => 'navbar-nav navbar-right'],
            'encodeLabels' => false,
            'items' => $menuItems,
        ]);
        NavBar::end();
        ?>

        <div class="container">
            <?= Alert::widget() ?>
            <?= Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]) ?>
            <?= $content ?>
        </div>
    </div>

    <div class="footer">
        <div class="container">
            <p class="pull-left">&copy; phprestful.com <?= date('Y') ?></p>

            <p class="pull-right"><?= Yii::powered() ?></p>
        </div>
    </div>
    <?php $this->endBody() ?>
    </body>
    </html>
<?php $this->endPage() ?>