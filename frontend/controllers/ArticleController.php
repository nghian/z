<?php

namespace frontend\controllers;

use common\models\ArticleCategory;
use common\models\ArticleComment;
use common\models\ArticleItem;
use common\models\ArticleLike;
use common\models\ArticleTag;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\elasticsearch\Query;
use yii\filters\AccessControl;
use yii\filters\ContentNegotiator;
use yii\flash\Flash;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\JsExpression;
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
                'only' => ['sub-category', 'like', 'comment-update', 'comment-delete', 'comment-like'],
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

    public function actionTagged($id)
    {
        $model = $this->loadTag($id);
        $dataProvider = new ActiveDataProvider([
            'query' => $model->getArticles()
        ]);
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
            'pagination' => false,
            'sort' => [
                'defaultOrder' => ['created_at' => SORT_ASC]
            ]
        ]);
        $model->updateCounters(['view_count' => 1]);
        return $this->render('view', [
            'model' => $model,
            'commentProvider' => $commentProvider,
            'newComment' => $this->commentCreate($model->id)
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
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return ['status' => true];
        } else {
            return ['status' => false, 'message' => array_shift(array_values($model->firstErrors))];
        }
    }

    public function actionCommentDelete()
    {
        $id = Yii::$app->request->post('id');
        if ($id) {
            if (!Yii::$app->user->can('delete', ['model' => $this->loadComment($id)])) {
                throw new ForbiddenHttpException('You are not allowed to access this page');
            }
            if ($this->loadComment($id)->delete() > 0) {
                return ['status' => true, 'callback' => "jQuery('.comment[data-key={$id}]').hide();"];
            }
        }
        return ['status' => false, 'alert' => ['message' => 'Unable to delete this comment.', 'type' => 'danger']];
    }

    public function actionCommentLike()
    {
        $id = Yii::$app->request->post('id');
        $model = $this->loadComment($id);
        return $model->like();
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

    public function actionRss($id)
    {
        $query = ArticleItem::find()
            ->where(['status' => ArticleItem::STATUS_PUBLISHED])
            ->andWhere(['<=', 'published_at', new Expression('UNIX_TIMESTAMP()')])
            ->orderBy(['created_at' => SORT_DESC, 'updated_at' => SORT_DESC]);
        if ($id) {
            $query->andWhere(['cid' => $id]);
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20
            ]
        ]);
        return $this->render('rss', [
            'dataProvider' => $dataProvider
        ]);
    }

    public function popularTags()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => ArticleTag::find()->orderBy(['frequency' => SORT_DESC])
        ]);
        return $this->renderPartial('sidebar/tags', ['dataProvider' => $dataProvider]);
    }

    public function createButton()
    {
        return $this->renderPartial('sidebar/create');
    }

    public function searchForm()
    {
        return $this->renderPartial('sidebar/search');
    }

    public function listCategory($id)
    {
        $category = $this->loadCategory($id);
        $dataProvider = new ActiveDataProvider([
            'query' => $category->parent->getSubs()
        ]);
        return $this->renderPartial('sidebar/categories', ['dataProvider' => $dataProvider]);
    }


    public function relatedArticles($id, $keywords)
    {
        $dataProvider = new ActiveDataProvider([
            'query' => $this->searchQuery($keywords)->andWhere(['!=', 'id', $id]),
            'totalCount' => 10
        ]);
        return $this->renderPartial('sidebar/related', ['dataProvider' => $dataProvider]);
    }

    public function latestComments()
    {
        $query = ArticleComment::find()->groupBy(['article_id'])->orderBy(['created_at' => SORT_DESC]);
        $dataProvider = new ActiveDataProvider(['query' => $query]);
        return $this->renderPartial('sidebar/latest-comments', ['dataProvider' => $dataProvider]);
    }


    /**
     * @param $keywords
     * @return ActiveQuery
     */
    public function searchQuery($keywords)
    {
        $keywords = new Expression($keywords);
        return ArticleItem::find()
            ->select(['*', "relevance" => "MATCH (title) AGAINST ('$keywords' IN BOOLEAN MODE)"])
            ->where("MATCH (title) AGAINST ('$keywords' IN BOOLEAN MODE)")
            ->orderBy(['relevance' => SORT_DESC]);
    }

    /**
     * @param $slug
     * @return ArticleTag||null
     * @throws NotFoundHttpException
     */
    protected function loadTag($id)
    {
        if (!$this->_tag) {
            if (is_null($this->_tag = ArticleTag::findOne($id))) {
                throw new NotFoundHttpException('This tag was not found');
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
