<?php
namespace frontend\models;

use common\models\UserEmail;
use common\models\UserLogin;
use yii\validators\EmailValidator;
use yii\base\Model;
use Yii;


/**
 * Class LoginForm
 * @package common\models
 * @property $userLogin UserLogin
 */
class RequestPasswordForm extends Model
{
    public $login;
    private $_isEmail = null;
    private $_user = false;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['login'], 'required'],
            ['login', 'validateLogin'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'login' => 'Username or Email',
        ];
    }

    /**
     * Validates the login provider.
     * This method serves as the inline validation for login.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validateLogin($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if (is_null($this->getUser())) {
                $this->addError($attribute, 'You account was not found.');
            } elseif (is_null($this->getUser()->userLogin)) {
                $this->addError($attribute, 'You account login not exist. This email can login via socials network');
            }
        }
    }

    /**
     * Find type of login is email or username
     * @return bool|null
     */
    public function getIsEmail()
    {
        if ($this->_isEmail === null) {
            $emailValidator = new EmailValidator();
            if ($emailValidator->validate($this->login, $error)) {
                $this->_isEmail = true;

            } else {
                $this->_isEmail = false;
            }
        }
        return $this->_isEmail;
    }

    /**
     * @return \common\models\User|mixed|null
     */
    public function getUser()
    {
        if (!$this->_user) {
            if ($this->getIsEmail()) {
                $userEmail = UserEmail::findOne(['email' => $this->login]);
                if ($userEmail) {
                    $this->_user = $userEmail->user;
                } else {
                    $this->_user = null;
                }
            } else {
                $userLogin = UserLogin::findOne(['username' => $this->login]);
                if ($userLogin) {
                    $this->_user = $userLogin->user;
                } else {
                    $this->_user = null;
                }
            }
        }
        return $this->_user;
    }

    public function sendEmail($view, $subject)
    {
        if (!is_null($this->getUser()->userEmail)) {
            $email = $this->getIsEmail() ? $this->login : $this->getUser()->userEmail->email;
            return Yii::$app->mailer->compose($view, ['model' => $this->getUser()])
                ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name])
                ->setTo($email)
                ->setSubject($subject)
                ->send();
        }
    }
}
