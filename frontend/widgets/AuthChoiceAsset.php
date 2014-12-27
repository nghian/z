<?php
/**
 * Created by PhpStorm.
 * User: nhat
 * Date: 12/22/2014
 * Time: 12:39 AM
 */

namespace frontend\widgets;


class AuthChoiceAsset extends \yii\authclient\widgets\AuthChoiceAsset
{
    public $depends = [
        'yii\web\YiiAsset',
    ];
}