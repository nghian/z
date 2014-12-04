<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "yid".
 *
 * @property integer $id
 * @property string $yid
 * @property string $title
 * @property string $category
 * @property string $author
 * @property string $description
 * @property integer $duration
 * @property integer $view_count
 * @property integer $published_at
 * @property integer $updated_at
 * @property integer $created_at
 */
class Yid extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'yid';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['yid'], 'required'],
            [['description'], 'string'],
            [['duration', 'view_count', 'published_at', 'updated_at', 'created_at'], 'integer'],
            [['yid'], 'string', 'max' => 100],
            [['title', 'category', 'author'], 'string', 'max' => 255],
            [['yid'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'yid' => 'Yid',
            'title' => 'Title',
            'category' => 'Category',
            'author' => 'Author',
            'description' => 'Description',
            'duration' => 'Duration',
            'view_count' => 'View Count',
            'published_at' => 'Published At',
            'updated_at' => 'Updated At',
            'created_at' => 'Created At',
        ];
    }
}
