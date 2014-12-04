<?php

namespace frontend\controllers;

use common\models\ArticleCategory;
use common\models\ArticleComment;
use common\models\ArticleItem;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\ContentNegotiator;
use yii\flash\Flash;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use Yii;


class ArticleController extends \yii\web\Controller
{
    public function behaviors()
    {
        return [
            [
                'class' => ContentNegotiator::className(),
                'only' => ['sub-category'],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ]
            ],
            [
                'class' => AccessControl::className(),
                'only' => ['create', 'update'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ]
        ];
    }

    public function actionIndex()
    {
        $category = ArticleCategory::findAll(['parent_id' => 0, 'status' => ArticleCategory::STATUS_ACTIVE]);
        $dataProvider = new ActiveDataProvider([
            'query' => ArticleItem::find()
                ->where(['status' => ArticleItem::STATUS_ACTIVE])
                ->orderBy(['created_at' => SORT_DESC, 'updated_at' => SORT_DESC]),
            'pagination' => [
                'pageSize' => 20
            ]
        ]);
        return $this->render('index', [
            'category' => $category,
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionCategory($id)
    {
        $model = ArticleCategory::findOne($id);
        if (is_null($model)) {
            throw new NotFoundHttpException("This category was not found");
        }
        $dataProvider = new ActiveDataProvider([
            'query' => ArticleItem::find()
                ->where([
                    'cid' => $model->id,
                    'status' => ArticleItem::STATUS_ACTIVE
                ])
                ->orderBy(['created_at' => SORT_DESC, 'updated_at' => SORT_DESC]),
            'pagination' => [
                'pageSize' => 20
            ]
        ]);
        return $this->render('category', [
            'model' => $model,
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionSubCategory($id)
    {
        $model = ArticleCategory::findAll(['parent_id' => $id]);
        $response = [['id' => 0, 'title' => 'None (Not select subcategory)']];
        foreach ($model as $cat) {
            $response[] = ['id' => $cat->id, 'title' => $cat->title];
        }
        return $response;
    }

    public function actionCreate()
    {
        $category = ArrayHelper::map(ArticleCategory::findAll([
            'status' => ArticleCategory::STATUS_ACTIVE,
            'parent_id' => 0
        ]), 'id', 'title');
        $category = ArrayHelper::merge(['' => 'None'], $category);
        $model = new ArticleItem();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                if ($model->save()) {
                    Flash::alert(Flash::ALERT_SUCCESS, 'Your article has been created successfully.');
                    $this->redirect(['/article/view', 'id' => $model->id, 'slug' => $model->slug]);
                    Yii::$app->end();
                }
            }
        }
        if ($model->subcategory_id) {
            $subcategory = ArrayHelper::map(ArticleCategory::findAll([
                'status' => ArticleCategory::STATUS_ACTIVE,
                'parent_id' => $model->category_id
            ]), 'id', 'title');
            $subcategory = ArrayHelper::merge([0 => 'None (Not select subcategory)'], $subcategory);
        } else {
            $subcategory = [];
        }
        return $this->render('create', [
            'model' => $model,
            'category' => $category,
            'subcategory' => $subcategory
        ]);
    }

    public function actionUpdate($id)
    {
        $category = ArrayHelper::map(ArticleCategory::findAll([
            'status' => ArticleCategory::STATUS_ACTIVE,
            'parent_id' => 0
        ]), 'id', 'title');
        $category = ArrayHelper::merge(['' => 'None'], $category);
        $model = ArticleItem::findOne($id);
        $model->synCategory();
        if ($model->subcategory_id) {
            $subcategory = ArrayHelper::map(ArticleCategory::findAll([
                'status' => ArticleCategory::STATUS_ACTIVE,
                'parent_id' => $model->category_id
            ]), 'id', 'title');
            $subcategory = ArrayHelper::merge(['0' => 'None'], $subcategory);
        } else {
            $subcategory = [];
        }
        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                if ($model->save()) {
                    Flash::alert(Flash::ALERT_SUCCESS, 'Your article has been saved successfully.');
                    $this->redirect(['/article/view', 'id' => $model->id, 'slug' => $model->slug]);
                    Yii::$app->end();
                }
            }
        }
        return $this->render('create', [
            'model' => $model,
            'category' => $category,
            'subcategory' => $subcategory
        ]);
    }

    public function actionView($id)
    {
        $model = ArticleItem::findOne($id);
        if (is_null($model)) {
            throw new NotFoundHttpException("This Article was not found");
        }
        $commentProvider = new ActiveDataProvider([
            'query' => ArticleComment::find()->where(['article_id' => $model->id, 'status' => ArticleComment::STATUS_ACTIVE]),
            'pagination' => [
                'pageSize' => 2
            ]
        ]);
        $model->updateCounters(['view_count' => 1]);
        return $this->render('view', [
            'model' => $model,
            'commentProvider' => $commentProvider
        ]);
    }

    public function comment($article_id)
    {
        $model = new ArticleComment([
            'article_id' => $article_id
        ]);
        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                if ($model->save()) {
                    Flash::alert(Flash::ALERT_SUCCESS, 'Your comment has been published.');
                }
            }
        } else {
            return $this->renderPartial('comment', [
                'model' => $model,
            ]);
        }
    }
}
