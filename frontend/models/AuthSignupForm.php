<?php
namespace frontend\models;


use common\models\User;
use common\models\UserEmail;
use common\models\UserLogin;
use common\models\UserOAuth;
use common\models\UserProfile;
use yii\base\Model;
use Yii;

/**
 * Class AuthSignupForm
 * @package frontend\models
 *
 * @property array $authorize
 */
class AuthSignupForm extends Model
{
    public $email;
    public $username;
    public $password;
    public $password_repeat;
    private $_authorize;

    public function rules()
    {
        $rules = [
            [['email', 'username', 'password', 'password_repeat'], 'trim'],
            ['email', 'unique', 'targetClass' => '\common\models\UserEmail', 'targetAttribute' => 'email'],
            ['email', 'email'],
            [['username', 'password'], 'required'],
            ['username', 'filter', 'filter' => 'strtolower'],
            ['username', 'string', 'length' => [4, 32]],
            ['username', 'match', 'pattern' => '/^[a-z0-9\.]+$/', 'message' => '{attribute} allows only letters (a-z), numbers, periods.'],
            ['username', 'unique', 'targetClass' => '\common\models\UserLogin', 'targetAttribute' => 'username'],
            ['password', 'string', 'min' => 6, 'max' => 32],
            ['password_repeat', 'compare', 'compareAttribute' => 'password']
        ];
        if ($this->isEmailRequired()) {
            $rules[] = ['email', 'required'];
        }
        return $rules;
    }

    public function attributeLabels()
    {
        return ['password_repeat' => 'Confirm Password'];
    }

    public function signup()
    {
        if ($this->validate()) {
            $attributes = $this->authorize['attributes'];
            $user = new User();
            if ($user->save()) {
                if (!$this->isEmailRequired()) {
                    $this->email = $this->authorize['attributes']['email'];
                }
                (new UserEmail(['email' => $this->email, 'user_id' => $user->id, 'priority' => UserEmail::PRIORITY_PRIMARY]))->save();
                $userLogin = new UserLogin(['username' => $this->username, 'user_id' => $user->id]);
                $userLogin->setPassword($this->password);
                $userLogin->save();
                $userProfile = new UserProfile(['user_id' => $user->id]);
                unset($attributes['email']);
                $userProfile->attributes = $attributes;
                //print_r($userProfile->attributes);
                // die();
                $userProfile->save();
                (new UserOAuth([
                    'user_id' => $user->id,
                    'client_id' => $this->authorize['clientId'],
                    'access_token' => serialize($this->authorize['accessToken']),
                    'social_id' => $attributes['id']
                ]))->save();
                $user->login(true);
                return true;
            }
        }
        return false;
    }


    public function getAuthorize()
    {
        if (!$this->_authorize) {
            if (!Yii::$app->session->has('authorize')) {
                Yii::$app->response->redirect(['/account/signup']);
            } else {
                $this->_authorize = Yii::$app->session->get('authorize');
            }
        }
        return $this->_authorize;
    }

    public function isEmailRequired()
    {
        return isset($this->authorize['attributes']['email']) !== true;
    }

} 