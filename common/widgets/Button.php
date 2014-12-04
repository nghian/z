<?php
namespace common\widgets;

use common\models\User;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\base\Widget;
use Yii;

/**
 * Class Button
 * @package frontend\widgets
 * @property User $model
 */
class Button extends Widget
{
    public $controller;
    public $model;
    public $label;
    public $labelIcon;
    public $color;
    public $size;
    public $options = [];
    protected $definedSizes = ['lg', 'sm', 'xs'];
    protected $definedColors = ['default', 'primary', 'success', 'info', 'warning', 'danger', 'link'];

    public function init()
    {
        if (!$this->model instanceof User) {
            throw new InvalidConfigException('Configure property $model  not match');
        }

        $this->options['data-controller'] = $this->controller;
        $this->options['data-action'] = 'add';
        $this->options['data-toggle'] = 'button-ajax';
        $this->options['data-user-id'] = $this->model->id;
        $this->normalizeController();
        $this->initClass();
    }

    public function run()
    {
        $label = $this->labelIcon ? Html::tag('span', '', ['class' => $this->labelIcon]) . '&nbsp;' : '';
        $label .= $this->label;
        echo Html::button($label, $this->options);
    }

    public function initClass()
    {
        Html::addCssClass($this->options, 'btn');
        $this->size = in_array($this->size, $this->definedSizes) ? $this->size : 'xs';
        Html::addCssClass($this->options, 'btn-' . $this->size);
        $this->color = in_array($this->color, $this->definedColors) ? $this->color : 'default';
        Html::addCssClass($this->options, 'btn-' . $this->color);
        if (Yii::$app->user->isGuest || Yii::$app->user->id == $this->model->id) {
            Html::addCssClass($this->options, 'disabled');
        }
    }

    public function normalizeController()
    {
        if ($this->controller == 'follow') {
            if ($this->model->isFollowed) {
                $this->label = $this->label ? $this->label : 'Unfollow';
                $this->options['data-action'] = 'un';
                $this->options['data-confirm'] = 'Are you sure you want to unfollow?';

            } else {
                $this->label = $this->label ? $this->label : 'Follow';
            }
            $this->labelIcon ='fa fa-child';
        }
        if ($this->controller == 'friend') {
            if ($this->model->isFriend) {
                $this->label = $this->label ? $this->label : 'Unfriend';
                $this->labelIcon = $this->labelIcon ? $this->labelIcon : 'fa fa-times-circle ';
                $this->options['data-action'] = 'un';
                $this->options['data-confirm'] = 'Are you sure you want to unfriend?';
            } elseif ($this->model->isRequestFriend) {
                $this->label = $this->label ? $this->label : 'Cancel Friend';
                $this->labelIcon = $this->labelIcon ? $this->labelIcon : 'fa fa-times-circle ';
                $this->options['data-action'] = 'cancel';
                $this->options['data-confirm'] = 'Are you sure you want to cancel friend request?';
            } elseif ($this->model->isConfirmFriend) {
                $this->label = $this->label ? $this->label : 'Confirm Friend';
                $this->labelIcon = $this->labelIcon ? $this->labelIcon : 'fa fa-check-circle';
                $this->options['data-action'] = 'confirm';
            } else {
                $this->label = $this->label ? $this->label : 'Add Friend';
                $this->labelIcon = $this->labelIcon ? $this->labelIcon : 'fa fa-plus-circle';
            }
        }
    }
} 