<?php

namespace frontend\controllers;

use common\behaviors\LayoutsBehavior;
use common\models\User;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

class UserController extends \yii\web\Controller
{
    public $defaultAction = 'friend';
    private $_user;

    public function behaviors()
    {
        return [
            [
                'class' => LayoutsBehavior::className(),
                'layouts' => [
                    'user' => ['view', 'article', 'friend', 'follower', 'following']
                ]
            ]
        ];
    }

    public function actionView($id)
    {
        $model = $this->getUser($id);
        return $this->render('view', ['model' => $model]);
    }

    public function actionArticle($id)
    {
        $dataProvider = new ActiveDataProvider([
            'query' => $this->getUser($id)->getArticleItems()
        ]);
        return $this->render('article', ['dataProvider' => $dataProvider]);
    }

    public function actionFriend($id)
    {
        $dataProvider = new ActiveDataProvider([
            'query' => $this->getUser($id)->getUserFriends()
        ]);
        return $this->render('friend', ['dataProvider' => $dataProvider]);
    }

    public function actionFollower($id)
    {
        $dataProvider = new ActiveDataProvider([
            'query' => $this->getUser($id)->getUserFollowers()
        ]);
        return $this->render('follower', ['dataProvider' => $dataProvider]);
    }

    public function actionFollowing($id)
    {
        $dataProvider = new ActiveDataProvider([
            'query' => $this->getUser($id)->getUserFollowing()
        ]);
        return $this->render('following', ['dataProvider' => $dataProvider]);
    }

    public function getInfo($id)
    {
        $model = $this->getUser($id);
        return $this->renderPartial('userInfo', ['model' => $model]);
    }

    public function getUser($id)
    {
        if (!$this->_user) {
            $this->_user = User::findOne($id);
            if (!$this->_user) {
                throw new NotFoundHttpException('This user was not found');
            }
        }
        return $this->_user;
    }
}
