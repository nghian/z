<?php
namespace frontend\models;


use common\models\User;
use common\models\UserEmail;
use common\models\UserLogin;
use common\models\UserOAuth;
use common\models\UserProfile;
use yii\base\Model;
use Yii;

class AuthSignupForm extends Model
{
    public $email;
    public $username;
    public $password;
    public $confirm;
    private $_authAttributes;

    public function rules()
    {
        $rules = [
            [['email', 'username', 'password', 'confirm'], 'trim'],
            [['username', 'password'], 'required'],
            ['username', 'filter', 'filter' => 'strtolower'],
            ['username', 'string', 'min' => 2, 'max' => 32],
            ['password', 'string', 'min' => 6, 'max' => 32],
            ['email', 'email'],
            ['username', 'unique', 'targetClass' => '\common\models\UserLogin', 'targetAttribute' => 'username'],
            ['email', 'unique', 'targetClass' => '\common\models\UserEmail', 'targetAttribute' => 'email'],
            ['confirm', 'compare', 'compareAttribute' => 'password']
        ];
        if (!$this->hasAuthAttribute('email')) {
            $rules[] = ['email', 'required'];
        }
        return $rules;
    }

    public function attributeLabels()
    {
        return ['confirm' => 'Confirm Password'];
    }

    public function signup()
    {
        if ($this->validate()) {
            $user = new User();
            if ($user->save()) {
                $userEmail = new UserEmail([
                    'email' => $this->hasAuthAttribute('email') ? $this->authAttributes['email'] : $this->email,
                    'user_id' => $user->id,
                    'priority' => UserEmail::PRIORITY_PRIMARY
                ]);
                $userEmail->save();
                $userLogin = new UserLogin([
                    'username' => $this->username,
                    'user_id' => $user->id,
                ]);
                $userLogin->setPassword($this->password);
                $userLogin->save();
                $userProfile = new UserProfile([
                    'user_id' => $user->id,
                    'username' => $userLogin->username,
                    'priority' => UserProfile::PRIORITY_PRIMARY
                ]);
                $userProfile->attributes = $this->getAuthAttributes();
                $userProfile->email = $userEmail->email;
                $userProfile->save();
                $userOAuth = new UserOAuth([
                    'profile_id' => $userProfile->id,
                    'user_id' => $user->id,
                    'client_id' => Yii::$app->session->get('_clientId'),
                    'access_token' => serialize(Yii::$app->session->get('_accessToken')),
                    'social_id' => $this->authAttributes['id']
                ]);
                $userOAuth->save();
                Yii::$app->user->setReturnUrl(['/account/profile']);
                $this->unsetAuthAttributes();
                $user->login();
                return true;
            }
        }
        return false;
    }

    public function getAuthAttributes()
    {
        if (!$this->_authAttributes) {
            if (!Yii::$app->session->has('_authAttributes')) {
                Yii::$app->response->redirect(['/account/signup']);
            } else {
                $this->_authAttributes = Yii::$app->session->get('_authAttributes');
            }
        }
        return $this->_authAttributes;
    }

    public function hasAuthAttribute($attribute)
    {
        $userAttributes = $this->getAuthAttributes();
        if (isset($userAttributes[$attribute])) {
            return true;
        }
        return false;
    }

    public function unsetAuthAttributes()
    {
        Yii::$app->session->remove('_authAttributes');
        Yii::$app->session->remove('_clientId');
        Yii::$app->session->remove('_accessToken');
        $this->_authAttributes == null;
    }
} 