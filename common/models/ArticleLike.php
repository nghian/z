<?php

namespace common\models;

use Yii;
use yii\helpers\Html;

/**
 * This is the model class for table "article_star".
 *
 * @property integer $article_id
 * @property integer $user_id
 *
 * Relations properties
 * @property ArticleItem $article
 *
 */
class ArticleLike extends \yii\db\ActiveRecord
{
    use UserRelationTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'article_like';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['article_id', 'user_id'], 'required'],
            [['article_id', 'user_id'], 'integer'],
            ['article_id', 'exist', 'targetClass' => '\common\models\Article', 'targetAttribute' => 'id'],
            ['user_id', 'exist', 'targetClass' => '\common\models\User', 'targetAttribute' => 'id'],
            [['article_id', 'user_id'], 'unique', 'targetAttribute' => ['article_id', 'user_id'], 'message' => 'The combination of Article ID and User ID has already been taken.']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'article_id' => 'Article ID',
            'user_id' => 'User ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArticle()
    {
        return $this->hasOne(ArticleItem::className(), ['id' => 'article_id']);
    }

    /**
     * @param $article_id
     * @return bool
     */
    public static function hasLike($article_id)
    {
        return self::findOne(['article_id' => $article_id, 'user_id' => Yii::$app->user->id]) !== null;
    }


    /**
     * @param $article_id
     * @return array
     */
    public function like($article_id)
    {
        if (self::hasLike($article_id)) {
            if (self::deleteAll(['article_id' => $article_id, 'user_id' => Yii::$app->user->id]) > 0) {
                return [
                    'status' => true,
                    'replace' => [
                        'html' => Html::tag('span', null, ['class' => 'psi-thumb-up']) . ' Like'
                    ],
                ];
            } else {
                return [
                    'status' => false,
                    'alert' => [
                        'message' => 'Unable to unlike this article.',
                        'type' => 'danger'
                    ]
                ];
            }
        } else {
            $model = new self(['article_id' => $article_id, 'user_id' => Yii::$app->user->id]);
            if ($model->save()) {
                return [
                    'status' => true,
                    'replace' => [
                        'html' => Html::tag('span', null, ['class' => 'psi-thumb-down']) . ' Unlike',
                        'data' => [
                            'alert' => 'Are you sure unlike this article?'
                        ]
                    ]
                ];
            } else {
                return [
                    'status' => false,
                    'alert' => [
                        'message' => 'Unable to like this article.',
                        'type' => 'danger'
                    ]
                ];
            }
        }
    }
}