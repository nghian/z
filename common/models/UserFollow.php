<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "user_follow".
 *
 * @property integer $user_id
 * @property integer $follow_id
 * @property integer $created_at
 * @property integer $updated_at
 *
 * Relations
 * @property User $user
 * @property User $follow
 */
class UserFollow extends \yii\db\ActiveRecord
{
    use UserRelationTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_follow';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'follow_id'], 'required'],
            [['user_id', 'follow_id', 'created_at', 'updated_at'], 'integer'],
            [['user_id', 'follow_id'], 'unique', 'targetAttribute' => ['user_id', 'follow_id'], 'message' => 'You have followed this person'],
            [['user_id','follow_id'], 'exist', 'targetClass' => '\common\models\User', 'targetAttribute' => 'id'],
            ['follow_id', 'compare', 'compareAttribute' => 'user_id', 'operator' => '!=', 'message' => "You can not follow yourself"],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User',
            'follow_id' => 'Follow',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getFollow()
    {
        return $this->hasOne(User::className(), ['id' => 'follow_id']);
    }
}
