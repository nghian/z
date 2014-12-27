<?php
namespace frontend\models;

use common\models\User;
use common\models\UserEmail;
use common\models\UserLogin;
use Yii;
use yii\base\Model;
use yii\validators\EmailValidator;

/**
 * Class LoginForm
 * @package common\models
 */
class LoginForm extends Model
{
    public $login;
    public $password;
    public $rememberMe = true;
    private $_isEmail = null;
    private $_user;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['login', 'password'], 'required'],
            ['rememberMe', 'boolean'],
            ['login', 'validateLogin'],
            ['password', 'validatePassword'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'login' => 'Username or Email',
            'rememberMe' => 'Stay signed in'
        ];
    }

    /**
     * Validates the login provider.
     * This method serves as the inline validation for login.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validateLogin($attribute)
    {
        if (!$this->hasErrors()) {
            if (is_null($this->getUser())) {
                $this->addError($attribute, 'You account was not found.');
            } elseif ($this->getUser()->role == User::ROLE_BANNED) {
                $this->addError($attribute, 'You account has been banned.');
            } elseif (is_null($this->getUser()->userLogin)) {
                $this->addError($attribute, 'This email can login via social network.');
            }
        }
    }

    /**
     * Validates the password provider.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if (!$this->getUser()->userLogin->validatePassword($this->password)) {
                $this->addError($attribute, 'Your password incorrect.');
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

    /**
     * Logs in a user using the provided login and password.
     *
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            return $this->getUser()->login($this->rememberMe);
        } else {
            return false;
        }
    }

}
