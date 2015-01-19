<?php


namespace common\rbac;


use common\models\User;
use Yii;
use yii\rbac\Rule;

/**
 * User group rule class.
 */
class GroupRule extends Rule
{
    /**
     * @inheritdoc
     */
    public $name = 'group';

    /**
     * @inheritdoc
     */
    public function execute($user, $item, $params)
    {
        if (!Yii::$app->user->isGuest) {
            $role = Yii::$app->user->identity->role;
            if ($item->name === 'register') {
                return $role === User::ROLE_REGISTER;
            } elseif ($item->name === 'editor') {
                return $role === User::ROLE_EDITOR || $role === User::ROLE_REGISTER;
            } elseif ($item->name === 'manager') {
                return $role === User::ROLE_EDITOR || $role === User::ROLE_REGISTER || $role === User::ROLE_MANAGER;
            }elseif($item->name = 'admin'){
                return $role === User::ROLE_EDITOR || $role === User::ROLE_REGISTER || $role === User::ROLE_MANAGER || $role === User::ROLE_ADMIN;
            }
        }
        return false;
    }
}