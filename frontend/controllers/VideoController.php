<?php

namespace frontend\controllers;

use common\models\VideoCategory;
use common\models\VideoItem;
use common\models\VideoPlaylist;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;


class VideoController extends Controller
{
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => VideoItem::find()->orderBy(['created_at' => SORT_DESC])
        ]);
        return $this->render('index', ['dataProvider' => $dataProvider]);
    }

    public function actionCategory($id)
    {
        $model = VideoCategory::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException("Video category was not found");
        }
        $dataProvider = new ActiveDataProvider([
            'query' => VideoItem::find()->andWhere(['cid' => $model->id])
        ]);
        return $this->render('category', [
            'model' => $model,
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionPlaylist($id)
    {
        $model = VideoPlaylist::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException("Playlist was not found");
        }
        return $this->render('playlist', ['model' => $model]);
    }

    public function actionWatch($id)
    {
        $model = $this->getItem($id);
        $model->update();
        return $this->render('watch', ['model' => $model]);
    }

    public function actionRate($id)
    {
        //$model = $this->getItem($id);
    }

    public function actionLike($id)
    {

    }

    public function comment($video)
    {

    }

    protected function getItem($id)
    {
        $model = VideoItem::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException("Video was not found");
        }
        return $model;
    }
}
