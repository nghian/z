<?php
/* @var $this \yii\web\View */
/* @var $content string */
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use frontend\assets\AppAsset;

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
    <nav class="navbar navbar-ps navbar-static-top" role="navigation">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="psi-menu"></span>
                </button>
                <a class="navbar-brand" href="/">
                    <span class="psi-brand"></span>
                </a>
            </div>
            <div class="collapse navbar-collapse navbar-ex1-collapse">
                <ul class="navbar-link-btn navbar-right">
                    <?php if (Yii::$app->controller->action->id != 'signup'): ?>
                        <li>
                            <a class="btn btn-success" href="/account/signup"><span class="psi-person-plus"></span> Sign Up</a>
                        </li>
                    <?php endif; ?>
                    <?php if (Yii::$app->controller->action->id != 'login'): ?>
                        <li>
                            <a class="btn btn-default" href="/account/login"><span class="psi-lock-open"></span> Sign In</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container">
        <?= $content ?>
    </div>
</div>
<div class="footer">
    <div class="container">
        <div>&copy; <?= date('Y') ?> www.phpsyntax.com</div>
    </div>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
