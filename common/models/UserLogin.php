<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "user_login".
 *
 * @property integer $user_id
 * @property string $username
 * @property string $password_hash
 * @property string $password_token
 */
class UserLogin extends \yii\db\ActiveRecord
{
    use UserRelationTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_login';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'username', 'password_hash'], 'required'],
            [['user_id'], 'integer'],
            [['user_id', 'username'], 'unique'],
            [['user_id'], 'exist', 'targetClass' => '\common\models\User', 'targetAttribute' => 'id'],
            [['username'], 'string', 'max' => 32],
            [['password_hash', 'password_token'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User Id',
            'username' => 'Username',
            'password_hash' => 'Password Hash',
            'reset_token' => 'Reset Token',
        ];
    }

    /**
     * @param $resetToken
     * @return static
     */
    public static function findByResetToken($resetToken)
    {
        return static::findOne(['password_token' => $resetToken]);
    }

    /**
     * Generate password hash
     * @param $password
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Finds out if password is valid
     *
     * @param string $password password provider
     * @return boolean
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates new reset token
     */
    public function setResetToken()
    {
        $this->password_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function unsetResetToken()
    {
        $this->password_token = null;
    }


}
