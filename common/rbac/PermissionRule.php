<?php
/**
 * Created by PhpStorm.
 * User: nhat
 * Date: 1/8/2015
 * Time: 10:13 AM
 */

namespace common\rbac;


use yii\db\ActiveRecord;
use yii\rbac\Item;
use yii\rbac\Rule;

class PermissionRule extends Rule
{
    /**
     * Executes the rule.
     *
     * @param string|integer $user the user ID. This should be either an integer or a string representing
     * the unique identifier of a user. See [[\yii\web\User::id]].
     * @param Item $item the role or permission that this rule is associated with
     * @param ActiveRecord $model properties passed to [[ManagerInterface::checkAccess()]].
     * @return boolean a value indicating whether the rule permits the auth item it is associated with.
     */
    public function execute($user, $item, $model)
    {
        $routes_owner = ['account/emails', 'account/profile', ''];
        if (in_array($item->name, $routes_owner)) {
            return $model->user_id === $user;
        }
        return false;
    }

}