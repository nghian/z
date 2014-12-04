<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "video_playlist".
 *
 * @property integer $id
 * @property integer $cid
 * @property string $title
 * @property string $slug
 * @property integer $view_count
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $status
 */
class VideoPlaylist extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'video_playlist';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cid', 'title', 'slug', 'view_count', 'created_at', 'updated_at', 'status'], 'required'],
            [['cid', 'view_count', 'created_at', 'updated_at', 'status'], 'integer'],
            [['title', 'slug'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'cid' => 'Cid',
            'title' => 'Title',
            'slug' => 'Slug',
            'view_count' => 'View Count',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'status' => 'Status',
        ];
    }
}
