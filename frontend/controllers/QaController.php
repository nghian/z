<?php

namespace frontend\controllers;

use common\models\QaAnswer;
use common\models\QaCategory;
use common\models\QaQuestion;
use yii\data\ActiveDataProvider;
use yii\flash\Flash;
use yii\web\NotFoundHttpException;
use Yii;

class QaController extends \yii\web\Controller
{
    private $_category;
    private $_question;
    private $_answer;

    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => QaQuestion::find()->orderBy(['created_at' => SORT_DESC])
        ]);
        return $this->render('index', [
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionCategory($id)
    {
        $model = $this->loadCategory($id);
        $dataProvider = new ActiveDataProvider([
            'query' => QaQuestion::find()->orderBy(['updated_at' => SORT_DESC]),
        ]);
        return $this->render('category', [
            'model' => $model,
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', ['model' => $this->loadQuestion($id)]);
    }

    public function actionAsk()
    {
        $model = new QaQuestion();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Flash::alert(Flash::ALERT_SUCCESS, 'Your question has been published.');
            $this->redirect($model->url);
            Yii::$app->end();
        }
        return $this->render('ask', ['model' => $model]);
    }

    public function newAnswer($id)
    {
        $model = new QaAnswer([
            'question_id' => $id,
            'user_id' => Yii::$app->user->id
        ]);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Flash::alert(Flash::ALERT_SUCCESS, 'Your answer has been published.');
            $this->refresh();
            Yii::$app->end();
        }
        return $this->render('answer/new', ['model' => $model]);
    }

    /**
     * @param $id
     * @return static QaCategory
     * @throws NotFoundHttpException
     */
    protected function loadCategory($id)
    {
        if (!$this->_category) {
            if (is_null($this->_category = QaCategory::findOne($id))) {
                throw new NotFoundHttpException('This category was not found');
            }
        }
        return $this->_category;
    }

    /**
     * @param $id
     * @return static QaQuestion
     * @throws NotFoundHttpException
     */
    protected function loadQuestion($id)
    {
        if (!$this->_question) {
            if (is_null($this->_question = QaQuestion::findOne($id))) {
                throw new NotFoundHttpException('This question was not found');
            }
        }
        return $this->_question;
    }

    /**
     * @param $id
     * @return static QaAnswer
     * @throws NotFoundHttpException
     */
    protected function loadAnswer($id)
    {
        if (!$this->_answer) {
            if (is_null($this->_answer = QaAnswer::findOne($id))) {
                throw new NotFoundHttpException('This answer was not found');
            }
        }
        return $this->_answer;
    }
}
