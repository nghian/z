<?php

namespace common\models;

use common\behaviors\WordCountBehavior;
use Yii;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;

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
 * @property string $str_tags
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
 *
 *
 * Shortcut
 * @property string $likeButton
 *
 */
class ArticleItem extends ActiveRecord
{
    use UserRelationTrait;
    const STATUS_DRAFT = 0;
    const STATUS_PUBLISHED = 1;
    const STATUS_ARCHIVED = 2;

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
            [['user_id', 'cid', 'category_id', 'subcategory_id', 'word_count', 'view_count', 'created_at', 'updated_at', 'published_at', 'status'], 'integer'],
            [['summary', 'body', 'bio'], 'string'],
            [['title', 'slug', 'str_tags'], 'string', 'max' => 255],
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
            'category_id' => Yii::t('app', 'Select an category'),
            'subcategory_id' => Yii::t('app', 'Select an subcategory'),
            'cid' => Yii::t('app', 'Category'),
            'title' => Yii::t('app', 'Title'),
            'slug' => Yii::t('app', 'slug'),
            'summary' => Yii::t('app', 'Summary'),
            'body' => Yii::t('app', 'Body'),
            'bio' => Yii::t('app', 'Bio'),
            'str_tags' => Yii::t('app', 'Tags'),
            'word_count' => Yii::t('app', 'Word Count'),
            'view_count' => Yii::t('app', 'View Count'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'status' => Yii::t('app', 'Status'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLikes()
    {
        return $this->hasMany(ArticleLike::className(), ['article_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getComments()
    {
        return $this->hasMany(ArticleComment::className(), ['article_id' => 'id'])->andWhere(['status' => ArticleComment::STATUS_APPROVED]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTags()
    {
        return $this->hasMany(ArticleTag::className(), ['id' => 'tag_id'])->viaTable(ArticleTagAssignment::tableName(), ['article_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(ArticleCategory::className(), ['id' => 'cid']);
    }

    public function syncCategory()
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

    public function getLikeButton($options = [])
    {
        $options = array_merge($options, [
            'class' => 'btn btn-xs btn-default',
        ]);
        if (Yii::$app->user->isGuest) {
            return Html::button(Html::tag('span', null, ['class' => 'psi-thumb-up']) . ' Like', array_merge($options, [
                'class' => 'btn btn-xs btn-default disabled',
            ]));
        } else {
            $options = array_merge($options, [
                'data-toggle' => 'ajax',
                'data-type' => 'POST',
                'data-data' => Json::encode(['id' => $this->id]),
                'data-data-type' => 'json',
                'data-url' => Url::to(['article/like']),
                'data-cache' => 'false',
            ]);
            if (ArticleLike::hasLike($this->id)) {
                return Html::button(Html::tag('span', null, ['class' => 'psi-thumb-down']) . ' Unlike', array_merge($options, ['data-alert' => 'Are you sure to unlike this article?']));
            } else {
                return Html::button(Html::tag('span', null, ['class' => 'psi-thumb-up']) . ' Like', $options);
            }
        }
    }

    /**
     * @param array $params
     * @return array
     */
    public function getUrl($params = [])
    {
        return array_merge(['article/view', 'id' => $this->id, 'slug' => $this->slug], $params);
    }

    /**
     * @param array $options
     * @param array $params
     * @return string
     */
    public function getLink($options = [], $params = [])
    {
        $options['title'] = ArrayHelper::getValue($options, 'title', $this->title);
        return Html::a($this->title, $this->getUrl($params), $options);
    }

    /**
     * @inheritdoc
     */
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

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ($insert) {
            (new ArticleTag())->addTags(ArticleTag::str2tags($this->str_tags), $this->id);
        } else {
            if (ArrayHelper::keyExists('str_tags', $changedAttributes)) {
                (new ArticleTag())->syncTags($changedAttributes['str_tags'], $this->str_tags, $this->id);
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function afterDelete()
    {
        parent::afterDelete();
        ArticleComment::deleteAll(['article_id' => $this->id]);
        (new ArticleTag())->syncTags($this->str_tags, '', $this->id);
    }
}
