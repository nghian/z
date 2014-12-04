<?php
/**
 * @var $this \yii\web\View
 */
use yii\helpers\Html;

$this->title = 'Emails Settings';
?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Emails</h3>
        </div>
        <div class="panel-body">
            <p> Your <strong>primary</strong> email address will be used for account-related notifications (e.g.
                account change) as well as any web-based system operations (e.g. edits and new comment). <br/>
                You need to be verified before establish primary.</p>
            <ul class="list-group">
                <?php foreach (Yii::$app->user->identity->userEmails as $email): ?>
                    <li class="list-group-item">
                        <?= Html::img($email->getGravatar(), ['width' => 18, 'class' => '']); ?>
                        <span><?= $email->email; ?></span>
                        <?php if ($email->isPrimary): ?>
                            <span class="label label-primary">Primary</span>
                        <?php endif ?>
                        <span class="pull-right">
                        <?php if (!$email->verified): ?>
                            <?= Html::a($email->verify_reset_token != null ? 'Resend verification link' : 'Verify', ['/account/email-verify', 'email' => $email->email], ['class' => 'btn btn-warning btn-xs']); ?>
                        <?php endif ?>
                        <?php if (!$email->isPrimary && $email->verified): ?>
                            <?= Html::a('Set Primary', ['/account/email-primary', 'email' => $email->email], ['class' => 'btn btn-primary btn-xs']); ?>
                        <?php endif ?>
                        <?= Html::a(Html::tag('span', null, ['class' => 'glyphicon glyphicon-trash']), ['/account/email-delete', 'email' => $email->email], ['class' => 'btn btn-xs btn-danger' . (!$email->isPrimary ? '' : ' disabled')]); ?>
                    </span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
<?= $this->context->addEmail(); ?>