<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\UserEmail */

$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['account/verify', 'token' => $model->verify_token]);
?>

Hello <strong><?= Html::encode($model->user->name) ?></strong>,
<p>Follow the link below to verify your email:</p>
<?= Html::a(Html::encode($resetLink), $resetLink) ?>