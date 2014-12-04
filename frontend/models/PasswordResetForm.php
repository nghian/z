<?php
namespace frontend\models;


use common\models\UserLogin;
use yii\base\Model;

class PasswordResetForm extends Model
{
    public $password;
    public $confirm;

    public function rules()
    {
        return [
            ['password', 'required'],
            ['password', 'string', 'min' => 6, 'max' => 32],
            ['confirm', 'compare', 'compareAttribute' => 'password']
        ];
    }

    public function attributeLabels()
    {
        return [
            'password' => 'New Password',
            'confirm' => 'Confirm new Password'
        ];
    }

    public function reset($userLogin)
    {
        if ($this->validate()) {
            if ($userLogin instanceof UserLogin) {
                $userLogin->setPassword($this->password);
                $userLogin->unsetResetToken();
                return $userLogin->save();
            }
        }
        return false;
    }
} 