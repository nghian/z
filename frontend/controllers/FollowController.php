<?php

namespace frontend\controllers;

use common\models\UserFollow;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use Yii;

class FollowController extends \yii\web\Controller
{
    /**
     * @inheritdoc
     */
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
                        'verbs' => ['POST'],
                        'roles' => ['@'],
                    ]
                ]
            ]
        ];
    }

    public function actionAdd()
    {
        $model = new UserFollow([
            'user_id' => Yii::$app->user->id,
            'follow_id' => Yii::$app->request->post('id')
        ]);
        if ($model->save()) {
            return [
                'status' => true,
                'replace' => [
                    'data' => [
                        'url' => Url::to(['follow/un']),
                        'alert' => 'Are you sure to unfollow this person?',
                    ],
                    'html' => Html::tag('span', null, ['class' => 'psi-eye-minus']) . ' Unfollow'
                ]
            ];
        } else {
            return ['status' => false, 'alert' => ['message' => array_shift(array_values($model->firstErrors)), 'type' => 'warning']];
        }
    }

    public function actionUn()
    {
        $model = UserFollow::findOne([
            'user_id' => Yii::$app->user->id,
            'follow_id' => Yii::$app->request->post('id')
        ]);
        if (is_null($model)) {
            return [
                'status' => false,
                'alert' => [
                    'message' =>
                        'This follow not exist',
                    'type' => 'danger'
                ]
            ];
        }
        if ($model->delete()) {
            return [
                'status' => true,
                'replace' => [
                    'data' => [
                        'url' => Url::to(['follow/add']),
                    ],
                    'html' => Html::tag('span', null, ['class' => 'psi-eye-plus']) . ' Follow'
                ]
            ];
        }
    }
}
