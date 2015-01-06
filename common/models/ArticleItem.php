<?php

namespace common\models;

use common\behaviors\WordCountBehavior;
use Yii;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
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
 * @property string $list_tags
 * @property integer $word_count
 * @property integer $view_count
 * @property integer $created_at
 * @property integer $published_at
 * @property integer $updated_at
 * @property integer $status
 *
 * Relations
 * @property User $user
 * @property ArticleComment[] $comments
 * @property ArticleTag[] $tags
 * @property ArticleCategory $category
 */
class ArticleItem extends ActiveRecord
{
    use UserRelationTrait;
    const STATUS_DRAFT = 0;
    const STATUS_PUBLISHED = 1;
    const STATUS_ARCHIVED = 10;

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
            [['user_id', 'cid', 'word_count', 'view_count', 'created_at', 'updated_at', 'published_at', 'status'], 'integer'],
            [['summary', 'body', 'bio'], 'string'],
            [['title', 'slug', 'list_tags'], 'string', 'max' => 255],
            [['title'], 'unique'],
            ['user_id', 'exist', 'targetClass' => '\common\models\User', 'targetAttribute' => 'id'],
            ['published_at', 'default', 'value' => new Expression('UNIX_TIMESTAMP()')],
            ['status', 'default', 'value' => self::STATUS_PUBLISHED]
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
            'list_tags' => Yii::t('app', 'Tags'),
            'word_count' => Yii::t('app', 'Word Count'),
            'view_count' => Yii::t('app', 'View Count'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'status' => Yii::t('app', 'Status'),
        ];
    }

    public function getComments()
    {
        return $this->hasMany(ArticleComment::className(), ['article_id' => 'id'])->andWhere(['status' => ArticleComment::STATUS_APPROVED]);
    }

    public function getTags()
    {
        return $this->hasMany(ArticleTag::className(), ['id' => 'tag_id'])->viaTable(Article2tag::tableName(), ['article_id' => 'id']);
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

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if (!$insert) {
                if ($this->isAttributeChanged('list_tags')) {
                    (new ArticleTag())->synctags($this->getOldAttribute('list_tags'), $this->list_tags, $this->id);
                }
                return true;
            }
        }
        return false;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ($insert) {
            (new ArticleTag())->addtags(ArticleTag::str2tags($this->list_tags), $this->id);
        }
    }

    public function afterDelete()
    {
        parent::afterDelete();
        ArticleComment::deleteAll(['article_id' => $this->id]);
        (new ArticleTag())->synctags($this->list_tags, '', $this->id);
    }
}
