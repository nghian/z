<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

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

