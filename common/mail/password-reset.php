<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['account/password-reset', 'token' => $model->userLogin->password_reset_token]);
?>

Hello <strong><?= Html::encode($model->name) ?></strong>,

Follow the link below to reset your password:

<?= Html::a(Html::encode($resetLink), $resetLink) ?>
