<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "article_comment_like".
 *
 * @property integer $comment_id
 * @property integer $user_id
 */
class ArticleCommentLike extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'article_comment_like';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['comment_id', 'user_id'], 'required'],
            [['comment_id', 'user_id'], 'integer'],
            ['comment_id', 'exist', 'targetClass' => '\common\models\ArticleComment', 'targetAttribute' => 'id'],
            ['user_id', 'exist', 'targetClass' => '\common\models\User', 'targetAttribute' => 'id'],
            [['comment_id', 'user_id'], 'unique', 'targetAttribute' => ['comment_id', 'user_id'], 'message' => 'The combination of Comment ID and User ID has already been taken.']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'comment_id' => 'Comment ID',
            'user_id' => 'User ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getComment()
    {
        return $this->hasOne(ArticleComment::className(), ['comment_id' => 'id']);
    }
}
