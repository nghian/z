<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;

/**
 * This is the model class for table "article_comment".
 *
 * @property integer $id
 * @property integer $article_id
 * @property integer $user_id
 * @property string $body
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $status
 *
 * Relations
 * @property User $user
 * @property ArticleItem $article
 */
class ArticleComment extends \yii\db\ActiveRecord
{
    use UserRelationTrait;
    const STATUS_APPROVED = 1;
    const STATUS_PENDING = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'article_comment';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className()
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['article_id', 'user_id', 'body'], 'required'],
            [['article_id', 'user_id', 'created_at', 'updated_at', 'status'], 'integer'],
            ['user_id', 'exist', 'targetClass' => '\common\models\User', 'targetAttribute' => 'id'],
            ['body', 'trim'],
            ['body', 'string'],
            ['status', 'default', 'value' => self::STATUS_APPROVED],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'article_id' => Yii::t('app', 'Article'),
            'user_id' => Yii::t('app', 'User'),
            'body' => Yii::t('app', 'Comment'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'status' => Yii::t('app', 'Status'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArticle()
    {
        return $this->hasOne(ArticleItem::className(), ['id' => 'article_id']);
    }

    public function hasLiked()
    {
        if (!Yii::$app->user->isGuest) {
            return ArticleCommentLike::findOne(['comment_id' => $this->id, 'user_id' => Yii::$app->user->id]) !== null;
        }
        return false;
    }

    public function like()
    {
        if ($this->hasLiked()) {
            if (ArticleCommentLike::deleteAll(['comment_id' => $this->id, 'user_id' => Yii::$app->user->id]) > 0) {
                return [
                    'status' => true,
                    'replace' => [
                        'html' => Html::tag('span', null, ['class' => 'psi-thumb-up']) . ' Like',
                        'attribute' => [
                            'class' => 'btn btn-xs btn-success'
                        ]
                    ]
                ];
            } else {
                return [
                    'status' => false,
                    'alert' => [
                        'message' => 'Unable to unlike this comment.',
                        'type' => 'danger'
                    ]
                ];
            }
        } else {
            if ((new ArticleCommentLike(['comment_id' => $this->id, 'user_id' => Yii::$app->user->id]))->save()) {
                return [
                    'status' => true,
                    'replace' => [
                        'html' => Html::tag('span', null, ['class' => 'psi-thumb-down']) . ' Unlike',
                        'data' => [
                            'alert' => 'Are you sure to unlike this comment?'
                        ],
                        'attribute' => [
                            'class' => 'btn btn-xs btn-danger'
                        ]
                    ]
                ];
            } else {
                return [
                    'status' => false,
                    'alert' => [
                        'message' => 'Unable to like this comment.',
                        'type' => 'danger'
                    ]
                ];
            }
        }
    }

    public function getLikeButton($options = [])
    {
        $options = array_merge($options, ['class' => 'btn btn-xs btn-success']);
        if (Yii::$app->user->isGuest) {
            return Html::a(Html::tag('span', null, ['class' => 'psi-thumb-up']) . ' Like', [Yii::$app->user->loginUrl, 'ref' => Yii::$app->request->absoluteUrl], $options);
        } else {
            $options = array_merge($options, [
                'data-toggle' => 'ajax',
                'data-data-type' => 'json',
                'data-cache' => 'false',
                'data-type' => 'POST',
                'data-url' => Url::to(['article/comment-like']),
                'data-data' => Json::encode(['id' => $this->id])
            ]);
            if ($this->hasLiked()) {
                return Html::button(Html::tag('span', null, ['class' => 'psi-thumb-down']) . ' Unlike', array_merge($options, [
                    'class' => 'btn btn-xs btn-danger',
                    'data-alert' => 'Are you sure to unlike this comment?'
                ]));
            } else {
                return Html::button(Html::tag('span', null, ['class' => 'psi-thumb-down']) . ' Like', $options);
            }
        }
    }


    /**
     * @inheritdoc
     */
    public function beforeValidate()
    {
        if (parent::beforeValidate()) {
            if (is_null($this->user_id)) {
                $this->user_id = Yii::$app->user->id;
            }
            return true;
        }
        return false;
    }

}

