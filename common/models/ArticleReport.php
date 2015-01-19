<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "article_report".
 *
 * @property integer $id
 * @property integer $article_id
 * @property integer $user_id
 * @property string $body
 * @property integer $created_at
 */
class ArticleReport extends \yii\db\ActiveRecord
{
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => false,
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'article_report';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['article_id', 'user_id', 'body'], 'required'],
            [['article_id', 'user_id', 'created_at'], 'integer'],
            ['article_id', 'exist', 'targetClass' => '\common\models\ArticleItem', 'targetAttribute' => 'id'],
            ['user_id', 'exist', 'targetClass' => '\common\models\User', 'targetAttribute' => 'id'],
            ['body', 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'article_id' => 'Article ID',
            'user_id' => 'User ID',
            'body' => 'Body',
            'created_at' => 'Created At',
        ];
    }
}
