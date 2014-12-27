<?php

namespace common\models;

use common\helpers\Gravatar;
use Yii;
use yii\flash\Flash;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "user_email".
 *
 * @property string $email
 * @property integer $user_id
 * @property integer $priority
 * @property integer $verified
 * @property string $verify_token
 *
 * Relations
 * @property User $user
 *
 * Shortcut
 * @property bool isPrimary
 */
class UserEmail extends \yii\db\ActiveRecord
{
    use UserRelationTrait;
    const PRIORITY_PRIMARY = 1;
    const PRIORITY_NONE = 0;
    const VERIFIED = 1;
    const UNVERIFIED = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_email';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email', 'user_id'], 'required'],
            [['user_id', 'priority', 'verified'], 'integer'],
            [['user_id'], 'exist', 'targetClass' => '\common\models\User', 'targetAttribute' => 'id'],
            [['email', 'verify_token'], 'string', 'max' => 100],
            ['email', 'email'],
            ['priority', 'default', 'value' => self::PRIORITY_NONE],
            ['verified', 'default', 'value' => self::UNVERIFIED],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'email' => Yii::t('app', 'Email'),
            'user_id' => Yii::t('app', 'User ID'),
            'priority' => Yii::t('app', 'Priority'),
            'verified' => Yii::t('app', 'Verify'),
        ];
    }

    public function getIsPrimary()
    {
        return $this->priority === self::PRIORITY_PRIMARY;
    }

    /**
     * @param array $config
     * @return object
     * @throws \yii\base\InvalidConfigException
     */
    public function getGravatar($config = [])
    {
        $config = ArrayHelper::merge([
            'class' => Gravatar::className(),
            'email' => $this->email
        ], $config);
        return Yii::createObject($config);
    }

    public static function findByResetToken($resetToken)
    {
        return static::findOne(['verify_token' => $resetToken]);
    }

    /**
     * Generates new value for verify_token
     */
    public function setResetToken()
    {
        $this->verify_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes value verify_token
     */
    public function unsetResetToken()
    {
        $this->verify_token = null;
    }

    public function primary()
    {
        $this->updateAll(['priority' => self::PRIORITY_NONE], ['user_id' => $this->user_id]);
        if($this->updateAttributes(['priority' => self::PRIORITY_PRIMARY])){
            Flash::alert(Flash::ALERT_SUCCESS, 'Your primary email address was established');
            return true;
        }
        return false;
    }

    public function verifying()
    {
        $this->setResetToken();
        if ($this->save()) {
            $sent = Yii::$app->mailer->compose('verify', ['model' => $this])
                ->setSubject(Yii::$app->name . ": Require verify email")
                ->setTo($this->email)
                ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name])
                ->send();
            if ($sent) {
                Flash::alert(Flash::ALERT_SUCCESS, 'A link is sent to your e-mail verification request');
                return true;
            } else {
                Flash::alert(Flash::ALERT_WARNING, 'Unable to send email to you');
            }
        }
        return false;
    }

    public function verified()
    {
        $this->verified = self::VERIFIED;
        $this->unsetResetToken();
        if ($this->save()) {
            Flash::alert(Flash::ALERT_SUCCESS, 'Your email has been verified');
        } else {
            Flash::alert(Flash::ALERT_WARNING, 'Unable to verify this email');
        }
    }

    public function afterSave($insert)
    {
        if ($insert) {
            $this->verifying();
        }
    }
}
