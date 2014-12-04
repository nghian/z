<?php
/**
 * @var $this \yii\web\View
 * @var $content string
 */
use yii\helpers\Html;
use yii\bootstrap\Nav;

?>
<?php $this->beginContent('@app/views/layouts/main.php'); ?>
    <div class="row">
        <div class="col-sm-4 col-md-4 col-lg-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Personal settings</h3>
                </div>
                <?= Nav::widget([
                    'options' => [
                        'class' => 'nav nav-stacked nav-menu'
                    ],
                    'items' => [
                        ['label' => 'Profile settings', 'url' => ['/account/profile']],
                        ['label' => 'Account  settings', 'url' => ['/account/settings']],
                        ['label' => 'Emails  settings', 'url' => ['/account/emails']],
                        ['label' => 'Create Login', 'url' => ['/account/create-login']],
                        ['label' => 'Change Login', 'url' => ['/account/change-login']],
                        ['label' => 'Change Password', 'url' => ['/account/change-password']],

                    ]
                ]); ?>
            </div>
        </div>
        <div class="col-sm-8 col-md-8 col-lg-8">
            <?= $content; ?>
        </div>
    </div>
<?php $this->endContent(); ?>