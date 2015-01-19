<?php

namespace frontend\controllers;

use common\models\ArticleCategory;
use common\models\ArticleComment;
use common\models\ArticleItem;
use common\models\ArticleLike;
use common\models\ArticleTag;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\filters\ContentNegotiator;
use yii\flash\Flash;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use Yii;


class ArticleController extends Controller
{
    private $_item;
    private $_category;
    private $_comment;
    private $_tag;

    public function behaviors()
    {
        return [
            [
                'class' => ContentNegotiator::className(),
                'only' => ['sub-category', 'like', 'comment-delete'],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ]
            ],
            [
                'class' => AccessControl::className(),
                'only' => ['create', 'update', 'star', 'report', 'email', 'comment-update', 'comment-delete'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ]
                ],
            ],
//            [
//                'class' => 'yii\filters\HttpCache',
//                'only' => ['index', 'category', 'tagged', 'view'],
//                'lastModified' => function ($action, $params) {
//                    if (in_array($action->id, ['index', 'category'])) {
//                        return (new ArticleItem())->find()->max('updated_at');
//                    } elseif ($action->id == 'tagged') {
//                        return $this->loadTag(Yii::$app->request->get('slug'))->getArticles()->max('updated_at');
//                    } else {
//                        return $this->loadItem(Yii::$app->request->get('id'))->updated_at;
//                    }
//                }
//            ]
        ];
    }

    public function actionIndex()
    {
        $category = ArticleCategory::findAll(['parent_id' => 0, 'status' => ArticleCategory::STATUS_ACTIVE]);
        $dataProvider = new ActiveDataProvider([
            'query' => ArticleItem::find()
                ->where([
                    'AND',
                    ['status' => ArticleItem::STATUS_PUBLISHED],
                    ['<=', 'published_at', new Expression('UNIX_TIMESTAMP()')]
                ])
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
        $model = $this->loadCategory($id);
        $dataProvider = new ActiveDataProvider([
            'query' => ArticleItem::find()
                ->where(['cid' => $model->id, 'status' => ArticleItem::STATUS_PUBLISHED])
                ->andWhere(['<=', 'published_at', new Expression('UNIX_TIMESTAMP()')])
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

    public function actionTagged($slug)
    {
        $model = $this->loadTag($slug);
        $dataProvider = new ActiveDataProvider([
            'query' => $model->getArticles()
        ]);
        var_dump($model->articles);
        return $this->render('tagged', ['model' => $model, 'dataProvider' => $dataProvider]);
    }

    public function actionSubCategory($id)
    {
        $response = [['id' => 0, 'title' => 'None (Not select sub category)']];
        if (ArticleCategory::find()->select(['id', 'title'])->where(['parent_id' => $id])->count() > 0) {
            $subs = ArticleCategory::find()->select(['id', 'title'])->where(['parent_id' => $id])->asArray()->all();
            $response = array_merge($response, $subs);
        }
        return $response;
    }

    public function actionCreate()
    {
        $category = ArrayHelper::map(ArticleCategory::findAll([
            'status' => ArticleCategory::STATUS_ACTIVE,
            'parent_id' => 0
        ]), 'id', 'title');
        $category = ArrayHelper::merge([0 => 'Select an category'], $category);
        $model = new ArticleItem();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Flash::alert(Flash::ALERT_SUCCESS, 'Your article has been created successfully.');
            $this->redirect(['/article/view', 'id' => $model->id, 'slug' => $model->slug]);
            Yii::$app->end();
        }
        if ($model->subcategory_id) {
            $subCategory = ArrayHelper::map(ArticleCategory::findAll([
                'status' => ArticleCategory::STATUS_ACTIVE,
                'parent_id' => $model->category_id
            ]), 'id', 'title');
            $subCategory = ArrayHelper::merge([0 => 'Select an sub category)'], $subCategory);
        } else {
            $subCategory = [];
        }
        return $this->render('create', [
            'model' => $model,
            'category' => $category,
            'subCategory' => $subCategory
        ]);
    }

    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('update', ['model' => $this->loadItem($id)])) {
            throw new ForbiddenHttpException('You are not allowed to access this page');
        }
        $category = ArrayHelper::map(ArticleCategory::findAll([
            'status' => ArticleCategory::STATUS_ACTIVE,
            'parent_id' => 0
        ]), 'id', 'title');
        $category = ArrayHelper::merge(['' => 'None'], $category);
        $model = $this->loadItem($id);
        $model->syncCategory();
        if ($model->subcategory_id) {
            $subCategory = ArrayHelper::map(ArticleCategory::findAll([
                'status' => ArticleCategory::STATUS_ACTIVE,
                'parent_id' => $model->category_id
            ]), 'id', 'title');
            $subCategory = ArrayHelper::merge([0 => 'Select an sub category)'], $subCategory);
        } else {
            $subCategory = [];
        }
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Flash::alert(Flash::ALERT_SUCCESS, 'Your article has been saved successfully.');
            $this->redirect(['/article/view', 'id' => $model->id, 'slug' => $model->slug]);
            Yii::$app->end();
        }
        return $this->render('create', [
            'model' => $model,
            'category' => $category,
            'subCategory' => $subCategory
        ]);
    }

    public function actionView($id)
    {
        $model = $this->loadItem($id);
        $commentProvider = new ActiveDataProvider([
            'query' => ArticleComment::find()->where(['article_id' => $model->id, 'status' => ArticleComment::STATUS_APPROVED]),
            'pagination' => [
                'pageSize' => 20
            ]
        ]);
        $model->updateCounters(['view_count' => 1]);
        return $this->render('view', [
            'model' => $model,
            'commentProvider' => $commentProvider,
            'newComment' => $this->commentCreate($id)
        ]);
    }

    /**
     * @param $article_id
     * @return string
     */
    public function commentCreate($article_id)
    {
        $model = new ArticleComment([
            'article_id' => $article_id
        ]);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Flash::alert(Flash::ALERT_SUCCESS, 'Your comment has been published.');
            $this->refresh();
            Yii::$app->end();
        }
        return $this->renderPartial('comment/create', [
            'model' => $model,
        ]);
    }

    public function actionCommentUpdate($id)
    {
        if (!Yii::$app->user->can('update', ['model' => $this->loadComment($id)])) {
            throw new ForbiddenHttpException('You are not allowed to access this page');
        }
        $model = $this->loadComment($id);
        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                if ($model->save()) {
                    Flash::alert(Flash::ALERT_SUCCESS, 'Your comment has been updated successfully.');
                    $this->redirect($model->article->url);
                    Yii::$app->end();
                }
            }
        } else {
            return $this->renderAjax('comment/update', [
                'model' => $model,
            ]);
        }
    }

    public function actionCommentDelete($id)
    {
        if (!Yii::$app->user->can('delete', ['model' => $this->loadComment($id)])) {
            throw new ForbiddenHttpException('You are not allowed to access this page');
        }
        if ($this->loadComment($id)->delete() > 0) {
            return ['status' => true];
        } else {
            return ['status' => false];
        }
    }

    public function actionPrint($id)
    {
        return $this->render('print', ['model' => $this->loadItem($id)]);
    }

    public function actionPublish($id)
    {
        return $this->render('publish', ['model' => $this->loadItem($id)]);
    }

    public function actionLike()
    {
        $id = Yii::$app->request->post('id');
        if ($id) {
            return (new ArticleLike())->like($id);
        }
    }

    public function actionReport($id)
    {
        return $this->renderAjax('report', ['model' => $this->loadItem($id)]);
    }

    public function actionRss($id = null)
    {

    }

    /**
     * @param $slug
     * @return ArticleTag||null
     * @throws NotFoundHttpException
     */
    protected function loadTag($slug)
    {
        if (!$this->_tag) {
            if (is_null($this->_tag = ArticleTag::findOne(['slug' => $slug]))) {
                throw new NotFoundHttpException('This Tag was not found');
            }
        }
        return $this->_tag;
    }

    /**
     * @param $id
     * @return ArticleCategory||null
     * @throws NotFoundHttpException
     */
    protected function loadCategory($id)
    {
        if (!$this->_category) {
            if (is_null($this->_category = ArticleCategory::findOne($id))) {
                throw new NotFoundHttpException('This Category was not found');
            }
        }
        return $this->_category;
    }

    /**
     * @param $id
     * @return ArticleItem|null
     * @throws NotFoundHttpException
     */
    protected function loadItem($id)
    {
        if (!$this->_item) {
            if (is_null($this->_item = ArticleItem::findOne($id))) {
                throw new NotFoundHttpException('This Article was not found');
            }
        }
        return $this->_item;
    }

    /**
     * @param $id
     * @return ArticleComment|null
     * @throws NotFoundHttpException
     */
    protected function loadComment($id)
    {
        if (!$this->_comment) {
            if (is_null($this->_comment = ArticleComment::findOne($id))) {
                throw new NotFoundHttpException('This Comment was not found');
            }
        }
        return $this->_comment;
    }
}
