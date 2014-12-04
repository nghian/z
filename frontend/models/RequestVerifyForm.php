<?php
namespace frontend\models;

use common\models\UserEmail;
use yii\base\Model;
use Yii;
use yii\flash\Flash;


/**
 * Class LoginForm
 * @package common\models
 */
class RequestVerifyForm extends Model
{
    public $email;
    public $_user;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email'], 'required'],
            ['email', 'exist', 'targetClass' => '\common\models\UserEmail', 'targetAttribute' => 'email']
        ];
    }

    public function verify()
    {
        if ($this->validate()) {
            $model = UserEmail::findOne(['email' => $this->email]);
            $model->setResetToken();
            if ($model->save()) {
                if ($this->sendEmail($model)) {
                    Flash::alert(Flash::ALERT_SUCCESS, 'A link is sent to your e-mail verification request');
                    return true;
                } else {
                    Flash::alert(Flash::ALERT_WARNING, 'Unable to send email to you');
                }
            }
        }
        return false;
    }

    public function sendEmail($model)
    {

        return Yii::$app->mailer->compose('verify', ['model' => $model])
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name])
            ->setTo($model->email)
            ->setSubject(Yii::$app->name . ' Request verify email')
            ->send();
    }
}
