<?php

namespace frontend\models;


use yii\base\Model;
use Yii;

class ChangePasswordForm extends Model
{
    public $password;
    public $newPassword;
    public $password_repeat;

    public function rules()
    {
        return [
            [['password', 'newPassword'], 'required'],
            ['password', 'validatePassword'],
            ['newPassword', 'string', 'min' => 6, 'max' => 32],
            ['password_repeat', 'compare', 'compareAttribute' => 'newPassword']
        ];
    }

    public function attributeLabels()
    {
        return [
            'password' => 'Current password',
            'newPassword' => 'New password',
            'password_repeat' => 'Confirm new password'
        ];
    }

    public function validatePassword($attribute)
    {
        if (!Yii::$app->user->identity->login->validatePassword($this->password)) {
            $this->addError($attribute, 'Your password incorrect');
        }
    }

    public function change()
    {
        if ($this->validate()) {
            Yii::$app->user->identity->login->setPassword($this->newPassword);
            return Yii::$app->user->identity->login->save();
        }
        return false;
    }

} 