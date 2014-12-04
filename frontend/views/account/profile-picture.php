<?php

/* @var $this yii\web\View */
/* @var $model common\models\UserProfile */
/* @var $form ActiveForm */
use yii\widgets\ActiveForm;
use yii\avatar\Avatar;

?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Profile Picture</h3>
    </div>
    <div class="panel-body">
        <?= Avatar::widget([
            'clientOptions' => [
                'picture' => Yii::$app->user->identity->userProfile->getAvatarUrl(),
                'uploadingUrl' => 'picture-upload',
                'croppingUrl' => 'picture-crop',
            ]
        ]); ?>
    </div>
</div>