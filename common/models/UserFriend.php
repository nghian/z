<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "user_friend".
 *
 * @property integer $user_id
 * @property integer $friend_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $status
 *
 * Relations
 * @property User $user
 * @property User $friend
 */
class UserFriend extends \yii\db\ActiveRecord
{
    use UserRelationTrait;
    const STATUS_ACTIVE = 1;
    const STATUS_CONFIRM = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_friend';
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
            [['user_id', 'friend_id'], 'required'],
            [['user_id', 'friend_id', 'created_at', 'updated_at', 'status'], 'integer'],
            [['user_id', 'friend_id'], 'unique', 'targetAttribute' => ['user_id', 'friend_id']],
            [['user_id', 'friend_id'], 'exist', 'targetClass' => '\common\models\User', 'targetAttribute' => 'id'],
            ['friend_id', 'compare', 'compareAttribute' => 'user_id', 'operator' => '!=', 'message' => "You can not friend with yourself"],
            ['status', 'default', 'value' => self::STATUS_CONFIRM]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'friend_id' => 'Friend ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'status' => 'Status',
        ];
    }

    public function confirm()
    {
        $this->status = self::STATUS_ACTIVE;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFriend()
    {
        return $this->hasOne(User::className(), ['id' => 'friend_id']);
    }
}
