<?php

namespace common\models;

use Yii;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "qa_question".
 *
 * @property integer $id
 * @property integer $category_id
 * @property integer $user_id
 * @property string $title
 * @property string $slug
 * @property string $body
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $status
 */
class QaQuestion extends \yii\db\ActiveRecord
{
    use UserRelationTrait;

    const STATUS_PUBLISHED = 1;
    const STATUS_CLOSED = 2;
    const STATUS_PENDING = 0;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
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
        return 'qa_question';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['category_id', 'user_id', 'title', 'body'], 'required'],
            [['category_id', 'user_id', 'created_at', 'updated_at', 'status'], 'integer'],
            ['category_id', 'exist', 'targetClass' => '\common\models\QaCategory', 'targetAttribute' => 'id'],
            ['user_id', 'exist', 'targetClass' => '\common\models\User', 'targetAttribute' => 'id'],
            [['body'], 'string'],
            [['title', 'slug'], 'string', 'max' => 255],
            ['status', 'default', 'value' => self::STATUS_PUBLISHED]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'category_id' => 'Category ID',
            'user_id' => 'User ID',
            'title' => 'Title',
            'slug' => 'Slug',
            'body' => 'Body',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'status' => 'Status',
        ];
    }

    /**
     * @return $this
     */
    public function close()
    {
        $this->status = self::STATUS_CLOSED;
        return $this;
    }

    public function reopen()
    {
        $this->status = self::STATUS_CLOSED;
        return $this;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(QaCategory::className(), ['id' => 'category_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAnswers()
    {
        return $this->hasMany(QaAnswer::className(), ['question_id' => 'id']);
    }
}
