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
    public $first_name;
    public $last_name;
    public $email;
    public $username;
    public $password;
    public $confirm;
    public $verifyCode;
    public $accept;

    public function rules()
    {
        return [
            [['first_name', 'last_name', 'email', 'username', 'password', 'confirm'], 'trim'],
            [['first_name', 'last_name', 'email', 'username', 'password'], 'required'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => '\common\models\UserEmail', 'targetAttribute' => 'email'],
            ['username', 'filter', 'filter' => 'strtolower'],
            ['username', 'string', 'min' => 2, 'max' => 32],
            ['username', 'unique', 'targetClass' => '\common\models\UserLogin', 'targetAttribute' => 'username'],
            ['password', 'string', 'min' => 6],
            ['confirm', 'compare', 'compareAttribute' => 'password'],
            ['verifyCode', 'captcha'],
            ['accept', 'required', 'requiredValue' => 1, 'message' => 'You need accept terms of our service'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'confirm' => 'Confirm password',
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
                if (!$login->save()) {
                    $user->delete();
                    Flash::alert(Flash::ALERT_DANGER, 'Unable to save account login');
                    return false;
                }
                $email = new UserEmail([
                    'email' => $this->email,
                    'user_id' => $user->id,
                    'priority' => UserEmail::PRIORITY_PRIMARY
                ]);
                if (!$email->save()) {
                    $user->delete();
                    $login->delete();
                    Flash::alert(Flash::ALERT_DANGER, 'Unable to create account email');
                    return false;
                }
                $profile = new UserProfile([
                    'first_name' => $this->first_name,
                    'last_name' => $this->last_name,
                    'email' => $this->email,
                    'username' => $this->username,
                    'user_id' => $user->id
                ]);
                if ($profile->save()) {
                    $user->updateAttributes(['profile_id' => $profile->id]);
                } else {
                    $user->delete();
                    $login->delete();
                    $email->delete();
                    Flash::alert(Flash::ALERT_DANGER, 'Unable to create profile.');
                    return false;
                }
                return true;
            }
        }
        return false;
    }
}