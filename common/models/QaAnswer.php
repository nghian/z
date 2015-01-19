<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "qa_answer".
 *
 * @property integer $id
 * @property integer $question_id
 * @property integer $user_id
 * @property string $body
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $status
 */
class QaAnswer extends \yii\db\ActiveRecord
{
    use UserRelationTrait;

    const STATUS_PUBLISHED = 1;
    const STATUS_PENDING = 0;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'qa_answer';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['question_id', 'user_id', 'body'], 'required'],
            [['question_id', 'user_id', 'created_at', 'updated_at', 'status'], 'integer'],
            ['question_id', 'exist', 'targetClass' => '\common\models\QaQuestion', 'targetAttribute' => 'id'],
            ['user_id', 'exist', 'targetClass' => '\common\models\User', 'targetAttribute' => 'id'],
            [['body'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'question_id' => 'Question ID',
            'user_id' => 'User ID',
            'body' => 'Body',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'status' => 'Status',
        ];
    }

    public function getQuestion()
    {
        return $this->hasOne(QaQuestion::className(), ['question_id' => 'id']);
    }
}
