<?php

namespace frontend\controllers;

class MessageController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

}
