<?php

namespace common\behaviors;


use yii\behaviors\AttributeBehavior;
use yii\db\BaseActiveRecord;

class NameableBehavior extends AttributeBehavior
{
    /**
     * @var string
     */
    public $nameAttribute = 'name';
    /**
     * @var array
     */
    public $attribute = ['first_name', 'last_name'];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if (empty($this->attributes)) {
            $this->attributes = [
                BaseActiveRecord::EVENT_BEFORE_INSERT => $this->nameAttribute,
                BaseActiveRecord::EVENT_BEFORE_UPDATE => $this->nameAttribute,
            ];
        }
    }

    protected function getValue($event)
    {
        $isNew = false;
        if ($this->owner->isNewRecord) {
            $isNew = true;
        } else {
            foreach ((array)$this->attribute as $attribute) {
                if ($this->owner->isAttributeChanged($attribute)) {
                    $isNew = true;
                    break;
                }
            }
        }
        if ($isNew) {
            $name = [];
            foreach ($this->attribute as $attribute) {
                $name[] = $this->owner->{$attribute};
            }
            return implode(' ', $name);
        } else {
            return $this->owner->{$this->nameAttribute};
        }
    }
} 