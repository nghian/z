<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "article_2tag".
 *
 * @property integer $article_id
 * @property integer $tag_id
 */
class Article2tag extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'article_2tag';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['article_id', 'tag_id'], 'required'],
            [['article_id', 'tag_id'], 'integer'],
            [['article_id', 'tag_id'], 'unique', 'targetAttribute' => ['article_id', 'tag_id'], 'message' => 'The combination of Article ID and Tag ID has already been taken.']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'article_id' => 'Article ID',
            'tag_id' => 'Tag ID',
        ];
    }
}
