<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "yidf".
 *
 * @property string $yid
 */
class Yidf extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'yidf';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['yid'], 'required'],
            [['yid'], 'string', 'max' => 100],
            [['yid'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'yid' => 'Yid',
        ];
    }
}
