<?php

namespace common\behaviors;

use yii\base\ActionFilter;

/**
 * Class LayoutsBehavior
 * @package common\behaviors
 *
 * Example
 *
 */
class LayoutsBehavior extends ActionFilter
{
    /**
     * @var array
     * [
     *      'column2'=>['edit','delete'],
     *      'column1'=>['view','print']
     * ]
     */
    public $layouts = [];

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        $layout = $this->findLayout($action->id);
        if($layout){
            $this->owner->layout = $layout;
        }
        return true;
    }

    /**
     * Find layout with current action
     * @param $action string action id
     * @return bool|int|string
     */
    protected function findLayout($action)
    {
        foreach ($this->layouts as $layout => $actions) {
            if (in_array($action, $actions, true)) {
                return $layout;
            }
        }
        return false;
    }
} 