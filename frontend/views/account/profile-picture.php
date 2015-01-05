<?php

/* @var $this yii\web\View */
/* @var $model common\models\UserProfile */
/* @var $form ActiveForm */
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use frontend\assets\AvatarAsset;

AvatarAsset::register($this);
$this->registerJs("jQuery('.avatar-view').avatar()");
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Profile Picture</h3>
    </div>
    <div class="panel-body">
        <div class="avatar-view" title="Click to change" href="#avatar-modal" data-toggle="modal">
            <?= Html::img(Yii::$app->user->identity->avatarUrl, ['alt' => 'Avatar']); ?>
            <span class="avatar-loading psi-spinner9 psi-spin"></span>
        </div>
        <!--Cropping modal-->
        <div class="modal fade" id="avatar-modal" aria-hidden="true" aria-labelledby="avatar-modal-label" role="dialog" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <?php $form = ActiveForm::begin([
                        'id' => 'avatar-form',
                        'action' => '/avatar/change',
                        'options' => ['enctype' => 'multipart/form-data'],
                    ]); ?>
                    <div class="modal-header">
                        <button class="close" data-dismiss="modal" type="button">&times;</button>
                        <h4 class="modal-title" id="avatar-modal-label">Change Avatar</h4>
                    </div>
                    <div class="modal-body">
                        <div class="avatar-body">
                            <div class="avatar-upload">
                                <?= Html::activeHiddenInput($model, 'x'); ?>
                                <?= Html::activeHiddenInput($model, 'y'); ?>
                                <?= Html::activeHiddenInput($model, 'width'); ?>
                                <?= Html::activeHiddenInput($model, 'height'); ?>
                                <?= $form->field($model, 'file')->fileInput(['accept' => 'image/*']); ?>
                            </div>
                            <div class="row">
                                <div class="col-md-9">
                                    <div class="avatar-wrapper"></div>
                                </div>
                                <div class="col-md-3">
                                    <div class="avatar-preview avatar-preview-lg"></div>
                                    <div class="avatar-preview avatar-preview-md"></div>
                                    <div class="avatar-preview avatar-preview-sm"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-default" data-dismiss="modal" type="reset">Cancel</button>
                        <button class="btn btn-primary avatar-save" type="submit">Change</button>
                    </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
        <!-- /.modal -->
    </div>
</div>