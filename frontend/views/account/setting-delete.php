<?php
/**
 * @var $this \yii\web\View
 */
use yii\helpers\Html;

?>
<div class="panel panel-danger">
    <div class="panel-heading">
        <h3 class="panel-title">Delete account</h3>
    </div>
    <div class="panel-body">
        <p>All data articles, messages ... will be deleted permanently.</p>
        <?= Html::button('Delete', ['class' => 'btn btn-danger']); ?>
    </div>
</div>