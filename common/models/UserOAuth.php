<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "user_oauth".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $profile_id
 * @property string $client_id
 * @property string $social_id
 * @property string $access_token
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $status
 *
 * Relations
 * @property User $user
 * @property UserProfile $userProfile
 */
class UserOAuth extends \yii\db\ActiveRecord
{
    use UserRelationTrait;
    const STATUS_ACTIVE = 1;
    const STATUS_DELETE = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_oauth';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className()
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'client_id', 'social_id', 'access_token'], 'required'],
            [['created_at', 'updated_at'], 'integer'],
            [['access_token'], 'string'],
            [['client_id'], 'string', 'max' => 20],
            ['user_id', 'exist', 'targetClass' => '\common\models\User', 'targetAttribute' => 'id']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'profile_id' => Yii::t('app', 'Profile ID'),
            'client_id' => Yii::t('app', 'Client ID'),
            'access_token' => Yii::t('app', 'Access Token'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'status' => Yii::t('app', 'Status'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserProfile()
    {
        return $this->hasOne(UserProfile::className(), ['id' => 'profile_id']);
    }

}
