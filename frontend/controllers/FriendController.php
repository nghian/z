<?php

namespace frontend\controllers;

use common\models\UserFriend;
use yii\filters\AccessControl;
use yii\filters\ContentNegotiator;
use yii\helpers\Url;
use yii\web\Response;
use Yii;


class FriendController extends \yii\web\Controller
{
    public $defaultAction = 'add';

    public function behaviors()
    {
        return [
            [
                'class' => ContentNegotiator::className(),
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ]
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'verbs' => ['POST'],
                        'roles' => ['@'],
                    ]
                ]
            ]
        ];
    }

    public function actionAdd()
    {
        $model = new UserFriend([
            'user_id' => Yii::$app->user->id,
            'friend_id' => Yii::$app->request->post('userId')
        ]);
        if ($model->save()) {
            return [
                'status' => true,
                'options' => [
                    'action' => 'cancel',
                    'label' => 'Cancel Friend',
                    'labelIcon' => 'remove',
                    //'color' => 'warning',
                    'confirm' => 'Are you sure you want to cancel request?'
                ]
            ];
        } else {
            return ['status' => false, 'message' => array_shift(array_values($model->firstErrors))];
        }
    }

    public function actionCancel()
    {
        $model = UserFriend::findOne([
            'user_id' => Yii::$app->user->id,
            'friend_id' => Yii::$app->request->post('userId'),
            'status' => UserFriend::STATUS_CONFIRM
        ]);
        if (!$model) {
            return ['status' => false, 'message' => 'The request friend not found'];
        } elseif ($model->delete()) {
            return [
                'status' => true,
                'options' => [
                    'action' => 'add',
                    'label' => 'Add Friend',
                    'labelIcon' => 'plus',
                    //'color' => 'warning',
                ]
            ];
        } else {
            return ['status' => false, 'message' => 'Unable to cancel request'];
        }
    }

    public function actionConfirm()
    {
        $model = UserFriend::findOne([
            'user_id' => Yii::$app->request->post('userId'),
            'friend_id' => Yii::$app->user->id
        ]);
        if (is_null($model)) {
            return ['status' => false, 'message' => 'The request friend not exist'];
        }
        $model->confirm();
        if ($model->save()) {
            $confirmModel = new UserFriend([
                'user_id' => Yii::$app->user->id,
                'friend_id' => Yii::$app->request->post('userId'),
                'status' => UserFriend::STATUS_ACTIVE
            ]);
            if ($confirmModel->save()) {
                return [
                    'status' => true,
                    'options' => [
                        'action' => 'un',
                        'label' => 'Unfriend',
                        'labelIcon' => 'remove-circle',
                        //'color' => 'danger',
                        'confirm'=>'Are you sure you want to unfriend?'
                    ]
                ];
            } else {
                return ['status' => false, 'message' => array_shift(array_values($confirmModel->firstErrors))];
            }
        } else {
            return ['status' => false, 'message' => array_shift(array_values($model->firstErrors))];
        }
    }

    public function actionUn()
    {
        if (UserFriend::deleteAll([
                'OR',
                [
                    'AND',
                    ['user_id' => Yii::$app->request->post('userId')],
                    ['friend_id' => Yii::$app->user->id]
                ],
                [
                    'AND',
                    ['friend_id' => Yii::$app->request->post('userId')],
                    ['user_id' => Yii::$app->user->id]
                ],
            ])) {
            return [
                'status' => true,
                'options' => [
                    'action' => 'add',
                    'label' => 'Add Friend',
                    'labelIcon' => 'plus',
                    'color' => 'default',
                ]
            ];
        } else {
            return ['status' => false];
        }
    }
}
