<?php
namespace frontend\models;

use common\models\User;
use common\models\UserEmail;
use common\models\UserLogin;
use common\models\UserProfile;
use yii\base\Model;
use yii\flash\Flash;

class SignUpForm extends Model
{
    public $name;
    public $email;
    public $username;
    public $password;
    public $password_repeat;
    public $verifyCode;
    public $accept;

    public function rules()
    {
        return [
            [['name', 'email', 'username', 'password', 'password_repeat'], 'trim'],
            [['name', 'email', 'username', 'password'], 'required'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => '\common\models\UserEmail', 'targetAttribute' => 'email'],
            ['username', 'filter', 'filter' => 'strtolower'],
            ['username', 'string', 'length' => [4, 32]],
            ['username', 'match', 'pattern' => '/^[a-z0-9\.]+$/', 'message' => '{attribute} allows only letters (a-z), numbers, periods.'],
            ['username', 'unique', 'targetClass' => '\common\models\UserLogin', 'targetAttribute' => 'username'],
            ['password', 'string', 'min' => 6],
            ['password_repeat', 'compare', 'compareAttribute' => 'password'],
            ['verifyCode', 'captcha'],
            ['accept', 'required', 'requiredValue' => 1, 'message' => 'You need accept terms of our service'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Full name',
            'password_repeat' => 'Confirm password',
            'accept' => 'I accept the terms of service'
        ];
    }

    public function signup()
    {
        if ($this->validate()) {
            $user = new User();
            if ($user->save()) {
                $login = new UserLogin([
                    'user_id' => $user->id,
                    'username' => $this->username
                ]);
                $login->setPassword($this->password);
                $login->save();
                $email = new UserEmail([
                    'user_id' => $user->id,
                    'email' => $this->email,
                    'priority' => UserEmail::PRIORITY_PRIMARY
                ]);
                $email->save();
                $profile = new UserProfile([
                    'user_id' => $user->id,
                    'name' => $this->name,
                ]);
                $profile->save();
                return true;
            }
        }
        return false;
    }
}