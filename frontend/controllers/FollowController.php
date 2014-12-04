<?php

namespace frontend\controllers;

use common\models\UserFollow;
use yii\helpers\Url;
use yii\web\Response;
use Yii;

class FollowController extends \yii\web\Controller
{
    public $defaultAction = 'add';

    public function behaviors()
    {
        return [
            [
                'class' => 'yii\filters\ContentNegotiator',
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ]
            ],
            'access' => [
                'class' => 'yii\filters\AccessControl',
                'rules' => [
                    [
                        'allow' => true,
                        //'verbs' => ['POST'],
                        'roles' => ['@'],
                    ]
                ]
            ]
        ];
    }

    public function actionAdd()
    {
        $model = new UserFollow();
        $model->user_id = Yii::$app->user->id;
        $model->follow_id = Yii::$app->request->post('userId');
        if ($model->save()) {
            return [
                'status' => true,
                'options' => [
                'action' => 'un',
                'label' => 'Unfollow',
                'labelIcon' => 'heart-empty',
                //'color' => 'warning',
                'confirm' => 'Are you sure you want to unfollow?'
            ]
            ];
        } else {
            return ['status' => false, 'message' => array_shift(array_values($model->firstErrors))];
        }
    }

    public function actionUn()
    {
        $model = UserFollow::findOne([
            'user_id' => Yii::$app->user->id,
            'follow_id' => Yii::$app->request->post('userId')
        ]);
        if (is_null($model)) {
            return ['status' => false, 'message' => 'This follow not exist'];
        }
        if ($model->delete()) {
            return [
                'status' => true,
                'options' => [
                    'action' => 'add',
                    'label' => 'Follow',
                    'labelIcon' => 'heart',
                   // 'color' => 'default',
                ]
            ];
        }
    }

    public function actionActive($id)
    {
        $model = UserFollow::findOne([
            'user_id' => $id,
            'follow_id' => Yii::$app->user->id
        ]);
        if (is_null($model)) {
            return ['status' => false, 'message' => 'This follow not exist'];
        }
        $model->setActive();
        if ($model->save()) {
            return ['status' => true];
        } else {
            return ['status' => false, 'message' => array_shift(array_values($model->firstErrors))];
        }
    }

}
