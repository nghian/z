<?php
namespace frontend\controllers;

use common\models\User;
use frontend\models\ChangeAvatar;
use Imagine\Image\Box;
use yii\imagine\Image;
use yii\web\Controller;
use Yii;
use yii\web\Response;

class AvatarController extends Controller
{
    public $defaultImagePath = '@webroot/images/avatars';

    public function actionPicture($u, $s = 200, $e = 'png')
    {
        $id = intval($u);
        if (is_null($user = User::findOne($id)) || is_null($picture = $user->userProfile->picture)) {
            $picture = Yii::getAlias($this->defaultImagePath) . '/avatar.' . ($id % 10) . '.png';
        }
        (new Image())->getImagine()->open($picture)->resize(new Box($s, $s))->show('png');
    }

    public function actionChange()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new ChangeAvatar();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->change()) {
                return ['status' => true, 'url' => $model->getUrl(), 'filename' => $model->getFileName()];
            } else {
                return ['status' => false, 'messages' => $model->getFirstError('file')];
            }
        } else {
            return ['status' => false, 'messages' => 'Unable to load data'];
        }
    }
}