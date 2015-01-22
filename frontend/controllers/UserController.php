<?php

namespace frontend\controllers;

use common\behaviors\LayoutsBehavior;
use common\models\UserLogin;
use Imagine\Image\Box;
use yii\data\ActiveDataProvider;
use yii\imagine\Image;
use yii\web\NotFoundHttpException;
use Yii;

class UserController extends \yii\web\Controller
{
    public $defaultAction = 'feed';
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

    public function actionArticle($username)
    {
        $dataProvider = new ActiveDataProvider([
            'query' => $this->getUser($username)->getArticleItems()
        ]);
        return $this->render('article', ['dataProvider' => $dataProvider]);
    }

    public function actionFriend($username)
    {
        $dataProvider = new ActiveDataProvider([
            'query' => $this->getUser($username)->getUserFriends()
        ]);
        return $this->render('friend', ['dataProvider' => $dataProvider]);
    }

    public function actionFollower($username)
    {
        $dataProvider = new ActiveDataProvider([
            'query' => $this->getUser($username)->getUserFollowers()
        ]);
        return $this->render('follower', ['dataProvider' => $dataProvider]);
    }

    public function actionFollowing($username)
    {
        $dataProvider = new ActiveDataProvider([
            'query' => $this->getUser($username)->getUserFollowing()
        ]);
        return $this->render('following', ['dataProvider' => $dataProvider]);
    }

    public function getInfo($username)
    {
        $model = $this->getUser($username);
        return $this->renderPartial('userInfo', ['model' => $model]);
    }

    public function actionPicture($username, $s = 200, $e = 'png')
    {
        $model = $this->getUser($username);
        if (is_null($picture = $model->userProfile->picture)) {
            $picture = Yii::getAlias('@webroot/images/avatars/avatar.' . ($model->id % 10) . '.png');
        }
        $imagine = new Image();
        $imagine->getImagine()->open($picture)->resize(new Box($s,$s))->show($e);
    }

    public function getUser($username)
    {
        if (!$this->_user) {
            if (is_null($userLogin = UserLogin::findOne(['username' => $username]))) {
                throw new NotFoundHttpException('This user was not found');
            } else {
                $this->_user = $userLogin->user;
            }
        }
        return $this->_user;
    }
}