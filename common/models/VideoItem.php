<?php

namespace common\models;

use Yii;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\Html;

/**
 * This is the model class for table "video_item".
 *
 * @property integer $id
 * @property integer $cid
 * @property integer $user_id
 * @property string $title
 * @property string $slug
 * @property string $file
 * @property string $description
 * @property integer $duration
 * @property integer $view_count
 * @property integer $rate_count
 * @property double $rate_avg
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $published_at
 * @property integer $status
 */
class VideoItem extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'video_item';
    }

    public function behaviors()
    {
        return [
            [
                'class' => SluggableBehavior::className(),
                'attribute' => 'title'
            ],
            [
                'class' => TimestampBehavior::className()
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cid', 'user_id', 'title', 'file'], 'required'],
            [['cid', 'user_id', 'duration', 'view_count', 'rate_count', 'created_at', 'updated_at', 'published_at', 'status'], 'integer'],
            [['description'], 'string'],
            [['rate_avg'], 'number'],
            [['title', 'slug', 'file'], 'string', 'max' => 255]
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
            'user_id' => 'User ID',
            'title' => 'Title',
            'slug' => 'Slug',
            'file' => 'File',
            'description' => 'Description',
            'duration' => 'Duration',
            'view_count' => 'View Count',
            'rate_count' => 'Rate Count',
            'rate_avg' => 'Rate Avg',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'published_at' => 'Published At',
            'status' => 'Status',
        ];
    }

    public function getUrl()
    {
        return ['/video/watch', 'id' => $this->id, 'slug' => $this->slug];
    }

    public function getLink()
    {
        return Html::a($this->title, $this->getUrl(), ['title' => $this->title]);
    }
}
