<?php

namespace common\models;

use Yii;
use yii\behaviors\SluggableBehavior;

/**
 * This is the model class for table "qa_category".
 *
 * @property integer $id
 * @property integer $parent_id
 * @property string $title
 * @property string $slug
 * @property integer $status
 */
class QaCategory extends \yii\db\ActiveRecord
{
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => SluggableBehavior::className(),
                'attribute' => 'title'
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'qa_category';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['parent_id', 'status'], 'integer'],
            [['title', 'slug'], 'string', 'max' => 255],
            ['parent_id', 'default', 'value' => 0],
            ['parent_id', 'exist', 'targetAttribute' => 'id'],
            ['status', 'default', 'value' => self::STATUS_ENABLED]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'parent_id' => 'Parent ID',
            'title' => 'Title',
            'slug' => 'Slug',
            'status' => 'Status',
        ];
    }
}
