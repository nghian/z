<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "video_category".
 *
 * @property integer $id
 * @property integer $parent_id
 * @property string $title
 * @property string $slug
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $status
 */
class VideoCategory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'video_category';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent_id', 'title', 'slug', 'created_at', 'updated_at', 'status'], 'required'],
            [['parent_id', 'created_at', 'updated_at', 'status'], 'integer'],
            [['title'], 'string', 'max' => 255],
            [['slug'], 'string', 'max' => 155]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'parent_id' => 'Parent ID',
            'title' => 'Title',
            'slug' => 'Slug',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'status' => 'Status',
        ];
    }
}
