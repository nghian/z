<?php
/**
 * Created by PhpStorm.
 * User: nhat
 * Date: 12/22/2014
 * Time: 12:35 AM
 */

namespace frontend\widgets;


use yii\helpers\Html;

class AuthChoice extends \yii\authclient\widgets\AuthChoice
{
    public function init()
    {
        $this->options['id'] = $this->getId();
        $this->registerJs();
        echo Html::beginTag('div', $this->options);
    }

    public function registerJs()
    {
        AuthChoiceAsset::register($this->getView());
        $this->getView()->registerJs("jQuery('#{$this->options['id']}').authchoice();");
    }
}