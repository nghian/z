<?php
namespace common\components;

use yii\base\Action;
use yii\web\Response;
use Yii;

/**
 * Class UploadAction
 * @package common\components
 * @property \common\models\UploadForm $model
 */
class UploadAction extends Action
{
    public $modelClass = '\common\models\UploadForm';
    public $ruleOptions = [];
    public $view;
    public $_model;


    public function run()
    {
        if (Yii::$app->request->isPost) {
            if ($this->model->upload()) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'status' => true,
                    'files' => $this->model->getResponse()
                ];
            }else{
                var_dump($this->model->firstErrors);
            }
        } else {
            if ($this->view) {
                return $this->controller->renderPartial($this->view, ['model' => $this->model]);
            }
        }

    }

    public function getModel()
    {
        if (!$this->_model) {
            $this->_model = Yii::createObject(['class' => $this->modelClass, 'ruleOptions' => $this->ruleOptions]);
        }
        return $this->_model;
    }
}