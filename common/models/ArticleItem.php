<?php

namespace common\models;

use common\behaviors\WordCountBehavior;
use Yii;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\Html;

/**
 * This is the model class for table "article_item".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $cid
 * @property string $title
 * @property string $slug
 * @property string $summary
 * @property string $body
 * @property string $bio
 * @property string $tags
 * @property integer $word_count
 * @property integer $view_count
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $status
 *
 * Relations
 * @property User $user
 * @property ArticleComment $comments
 * @property ArticleCategory $category
 */
class ArticleItem extends \yii\db\ActiveRecord
{
    use UserRelationTrait;
    const STATUS_ACTIVE = 1;
    const STATUS_DELETE = 0;

    public $category_id;
    public $subcategory_id;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'article_item';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => SluggableBehavior::className(),
                'slugAttribute' => 'slug',
                'attribute' => ['title']
            ],
            TimestampBehavior::className(),
            WordCountBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'category_id', 'cid', 'title', 'summary', 'body'], 'required'],
            [['user_id', 'cid', 'word_count', 'view_count', 'created_at', 'updated_at', 'status'], 'integer'],
            [['summary', 'body', 'bio'], 'string'],
            [['title', 'slug', 'tags'], 'string', 'max' => 255],
            [['title'], 'unique'],
            ['user_id', 'exist', 'targetClass' => '\common\models\User', 'targetAttribute' => 'id'],
            ['status', 'default', 'value' => self::STATUS_ACTIVE]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', 'User'),
            'category_id' => Yii::t('app', 'Select Category'),
            'subcategory_id' => Yii::t('app', 'Select Subcategory'),
            'cid' => Yii::t('app', 'Category'),
            'title' => Yii::t('app', 'Title'),
            'slug' => Yii::t('app', 'slug'),
            'summary' => Yii::t('app', 'Summary'),
            'body' => Yii::t('app', 'Body'),
            'bio' => Yii::t('app', 'Bio'),
            'tags' => Yii::t('app', 'Tags'),
            'word_count' => Yii::t('app', 'Word Count'),
            'view_count' => Yii::t('app', 'View Count'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'status' => Yii::t('app', 'Status'),
        ];
    }

    public function getComments()
    {
        return $this->hasMany(ArticleComment::className(), ['article_id' => 'id'])->andWhere(['status' => ArticleComment::STATUS_ACTIVE]);
    }

    public function getCategory()
    {
        return $this->hasOne(ArticleCategory::className(), ['id' => 'cid']);
    }

    public function synCategory()
    {
        if (!$this->isNewRecord) {
            if (is_null($this->category->parent)) {
                $this->category_id = $this->category->id;
            } else {
                $this->category_id = $this->category->parent->id;
                $this->subcategory_id = $this->category->id;
            }
        }
    }

    public function getUrl()
    {
        return ['article/view', 'id' => $this->id, 'slug' => $this->slug];
    }

    public function getLink()
    {
        return Html::a($this->title, $this->getUrl(), ['title' => $this->title]);
    }

    public function beforeValidate()
    {
        if (parent::beforeValidate()) {
            if (!$this->user_id && !Yii::$app->user->isGuest) {
                $this->user_id = Yii::$app->user->id;
            }
            return true;
        }
        return false;
    }


}
