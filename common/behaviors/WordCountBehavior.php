<?php

namespace common\behaviors;


use yii\behaviors\AttributeBehavior;
use yii\db\BaseActiveRecord;

class WordCountBehavior extends AttributeBehavior
{
    public $wordCountAttribute = 'word_count';
    public $attribute = 'body';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (empty($this->attributes)) {
            $this->attributes = [
                BaseActiveRecord::EVENT_BEFORE_INSERT => $this->wordCountAttribute,
                BaseActiveRecord::EVENT_BEFORE_UPDATE => $this->wordCountAttribute,
            ];
        }
    }

    protected function getValue($event)
    {
        if ($this->owner->isNewRecord || (!$this->owner->isNewRecord && ($this->owner->isAttributeChanged($this->attribute)))) {
            return $this->counter($this->owner->{$this->attribute});
        } else {
            return $this->owner->{$this->wordCountAttribute};
        }
    }

    protected function counter($str)
    {
        $str = htmlspecialchars_decode(strip_tags(trim($str)));
        $separators = [
            ' ', '_', "\x20", "\xA0", "\x0A", "\x0D", "\x09",
            "\x0B", "\x2E", "\t", '=', '+', '-', '*', '/',
            '\\', ',', '.', ';', ':', '"', '\'', '[',
            ']', '{', '}', '(', ')', '<', '>', '&',
            '%', '$', '@', '#', '^', '!', '?'
        ];
        return count(explode(' ',preg_replace('/[ ]+/s',' ',str_replace($separators,' ',$str))));
    }

} 