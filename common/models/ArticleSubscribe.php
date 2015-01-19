<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "article_subscribe".
 *
 * @property integer $article_id
 * @property integer $user_id
 */
class ArticleSubscribe extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'article_subscribe';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['article_id', 'user_id'], 'required'],
            [['article_id', 'user_id'], 'integer'],
            ['article_id', 'exist', 'targetClass' => '\common\models\ArticleItem', 'targetAttribute' => 'id'],
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
     * @param $article_id
     * @param $user_id
     * @return ArticleSubscribe|bool
     */
    public function create($article_id, $user_id)
    {
        $model = new self(['article_id' => $article_id, 'user_id' => $user_id]);
        if ($model->save()) {
            return $model;
        }
        return false;
    }
}
