<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "video_playlist_item".
 *
 * @property integer $list_id
 * @property integer $video_id
 */
class VideoPlaylistItem extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'video_playlist_item';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['list_id', 'video_id'], 'required'],
            [['list_id', 'video_id'], 'integer'],
            [['list_id', 'video_id'], 'unique', 'targetAttribute' => ['list_id', 'video_id'], 'message' => 'The combination of List ID and Video ID has already been taken.']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'list_id' => 'List ID',
            'video_id' => 'Video ID',
        ];
    }
}
