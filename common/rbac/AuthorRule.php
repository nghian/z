<?php
namespace common\rbac;


use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\rbac\Item;
use yii\rbac\Rule;
use Yii;

class AuthorRule extends Rule
{
    /**
     * @var string
     */
    public $name = 'author';

    /**
     * Executes the rule.
     *
     * @param string|integer $user the user ID. This should be either an integer or a string representing
     * the unique identifier of a user. See [[\yii\web\User::id]].
     * @param Item $item the role or permission that this rule is associated with
     * @param array $params properties passed to [[ManagerInterface::checkAccess()]].
     * @return boolean a value indicating whether the rule permits the auth item it is associated with.
     */
    public function execute($user, $item, $params)
    {
        $model = ArrayHelper::getValue($params, 'model');
        $attribute = ArrayHelper::getValue($params, 'attribute', 'user_id');
        if (($model instanceof ActiveRecord) && $model->{$attribute} === $user) {
            return true;
        }
        return false;
    }
}