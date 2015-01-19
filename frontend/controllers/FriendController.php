<?php

namespace frontend\controllers;

use common\models\UserFriend;
use yii\filters\AccessControl;
use yii\filters\ContentNegotiator;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use Yii;


class FriendController extends \yii\web\Controller
{
    /**
     * @inheritdoc
     */
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
            'friend_id' => Yii::$app->request->post('id')
        ]);
        if ($model->save()) {
            return [
                'status' => true,
                'replace' => [
                    'data' => [
                        'url' => Url::to(['friend/cancel']),
                        'alert' => 'Are you sure to cancel friend this person?',
                    ],
                    'html' => Html::tag('span', null, ['class' => 'psi-cancel-o']) . ' Cancel friend'
                ]
            ];
        } else {
            return ['status' => false, 'alert' => ['message' => array_shift(array_values($model->firstErrors)), 'type' => 'warning']];
        }
    }

    public function actionCancel()
    {
        $model = UserFriend::findOne([
            'user_id' => Yii::$app->user->id,
            'friend_id' => Yii::$app->request->post('id'),
            'status' => UserFriend::STATUS_CONFIRM
        ]);
        if (!$model) {
            return [
                'status' => false,
                'alert' => [
                    'message' => 'The request friend not exist.',
                    'type' => 'danger'
                ]
            ];
        } elseif ($model->delete()) {
            return [
                'status' => true,
                'replace' => [
                    'data' => [
                        'url' => Url::to(['friend/add']),
                    ],
                    'html' => Html::tag('span', null, ['class' => 'psi-add']) . ' Add friend'
                ]
            ];
        } else {
            return [
                'status' => false,
                'alert' => [
                    'message' => 'Unable to cancel request.',
                    'type' => 'danger'
                ]
            ];
        }
    }

    public function actionConfirm()
    {
        $model = UserFriend::findOne([
            'user_id' => Yii::$app->request->post('id'),
            'friend_id' => Yii::$app->user->id
        ]);
        if (is_null($model)) {
            return [
                'status' => false,
                'alert' => [
                    'message' => 'The request friend not exist.',
                    'type' => 'danger'
                ]
            ];
        }
        $model->confirm();
        if ($model->save()) {
            $confirmModel = new UserFriend([
                'user_id' => Yii::$app->user->id,
                'friend_id' => Yii::$app->request->post('id'),
                'status' => UserFriend::STATUS_ACTIVE
            ]);
            if ($confirmModel->save()) {
                return [
                    'status' => true,
                    'replace' => [
                        'data' => [
                            'url' => Url::to(['friend/un']),
                            'alert' => 'Are you sure to unfriend this person?',
                        ],
                        'html' => Html::tag('span', null, ['class' => 'psi-cancel-o']) . ' Cancel friend'
                    ]
                ];
            } else {
                return [
                    'status' => false,
                    'alert' => [
                        'message' => array_shift(array_values($confirmModel->firstErrors)),
                        'type' => 'warning'
                    ]
                ];
            }
        } else {
            return [
                'status' => false,
                'alert' => [
                    'message' => array_shift(array_values($model->firstErrors)),
                    'type' => 'warning'
                ]
            ];
        }
    }

    public function actionUn()
    {
        $user_id = Yii::$app->user->id;
        $friend_id = Yii::$app->request->post('id');
        if (UserFriend::deleteAll(['OR', ['AND', ['user_id' => $friend_id], ['friend_id' => $user_id]], ['AND', ['friend_id' => $friend_id], ['user_id' => $user_id]]]) > 0) {
            return [
                'status' => true,
                'replace' => [
                    'data' => [
                        'url' => Url::to(['friend/add']),
                    ],
                    'html' => Html::tag('span', null, ['class' => 'psi-add']) . ' Add friend'
                ]
            ];
        } else {
            return [
                'status' => false,
                'alert' => [
                    'message' => 'Unable to unfriend this person.',
                    'type' => 'danger'
                ]
            ];
        }
    }
}
