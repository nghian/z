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
FontAwesomeAsset::register($this);
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
    <div class="wrap">
        <?php
        NavBar::begin([
            'brandLabel' => 'PHP RESTFUL',
            'brandUrl' => Yii::$app->homeUrl,
            'options' => [
                'class' => 'navbar-default navbar-fw',
            ],
        ]);
        $menuItems = [];
        if (Yii::$app->user->isGuest) {
            $menuItems[] = ['label' => Html::tag('span', null, ['class' => 'fa fa-plus']) . ' Sign Up', 'url' => ['/account/signup']];
            $menuItems[] = ['label' => Html::tag('span', null, ['class' => 'fa fa-sign-in']) . ' Sign In', 'url' => ['/account/login']];
        } else {
            $menuItems[] = [
                'label' => Html::tag('span', null, ['class' => 'glyphicon glyphicon-user']) . ' ' . Yii::$app->user->identity->userProfile->first_name,
                'url' => Yii::$app->user->identity->url
            ];
            $menuItems[] = [
                'label' => Html::tag('span', null, ['class' => 'fa fa-users']),
                'url' => ['#']
            ];
            $menuItems[] = [
                'label' => Html::tag('span', null, ['class' => 'fa fa-bell-o']),
                'url' => ['#']
            ];
            $menuItems[] = [
                'label' => Html::tag('span', null, ['class' => 'fa fa-cogs']),
                'url' => ['/account/profile']
            ];
            $menuItems[] = [
                'label' => Html::tag('span', null, ['class' => 'fa fa-sign-out']),
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
            <?= Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]) ?>
            <?= Alert::widget() ?>
            <?= $content ?>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <p class="pull-left">&copy; phprestful.com <?= date('Y') ?></p>

            <p class="pull-right"><?= Yii::powered() ?></p>
        </div>
    </footer>

    <?php $this->endBody() ?>
    </body>
    </html>
<?php $this->endPage() ?>