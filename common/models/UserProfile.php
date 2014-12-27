<?php

namespace common\models;

use common\behaviors\NameableBehavior;
use common\helpers\Gravatar;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\flash\Flash;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\imagine\Image;
use yii\timeago\TimeAgo;
use yii\web\UploadedFile;
use Yii;

/**
 * This is the model class for table "user_profile".
 *
 * @property integer $user_id
 * @property string $email
 * @property string $name
 * @property string $slug
 * @property string $gender
 * @property string $birthday
 * @property string $website
 * @property string $location
 * @property string $company
 * @property string $locale
 * @property string $bio
 * @property string $picture
 * @property integer $created_at
 * @property integer $updated_at
 *
 * Relations properties
 * @property User $user
 * @property UserOAuth $oauth
 * @property ArticleItem[] $articleItems
 * @property ArticleComment[] $comments
 */
class UserProfile extends \yii\db\ActiveRecord
{
    use UserRelationTrait;
    public $pictureUpload;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_profile';
    }

    public function behaviors()
    {
        return [
            [
                'class' => SluggableBehavior::className(),
                'attribute' => ['name']
            ],
            TimestampBehavior::className()
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'name'], 'required'],
            [['user_id', 'created_at', 'updated_at'], 'integer'],
            [['gender', 'bio'], 'string'],
            [['email', 'name', 'slug', 'website', 'locale', 'location', 'company', 'picture'], 'string', 'max' => 255],
            ['email', 'email'],
            ['website', 'url'],
            ['picture', 'url'],
            ['birthday', 'date', 'format' => 'yyyy-MM-dd'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'email' => Yii::t('app', 'Email'),
            'name' => Yii::t('app', 'Name'),
            'slug' => Yii::t('app', 'Slug'),
            'gender' => Yii::t('app', 'Gender'),
            'birthday' => Yii::t('app', 'Birthday'),
            'website' => Yii::t('app', 'Url'),
            'locale' => Yii::t('app', 'Locale'),
            'location' => Yii::t('app', 'Location'),
            'company' => Yii::t('app', 'Company'),
            'bio' => Yii::t('app', 'Bio'),
            'picture' => Yii::t('app', 'Picture'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    public function getInlineInfo()
    {
        if (!empty($this->company)) {
            return Html::tag('i', null, ['class' => 'psi-']) . '&nbsp; ' . $this->company;
        } elseif (!empty($this->location)) {
            return Html::tag('i', null, ['class' => 'psi-location']) . '&nbsp; ' . $this->location;
        } else {
            return Html::tag('i', null, ['class' => 'fa fa-clock-o']) . '&nbsp; joined ' . TimeAgo::widget(['timestamp' => $this->created_at]);
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOauth()
    {
        return $this->hasOne(UserOAuth::className(), ['profile_id' => 'id']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getComments()
    {
        return $this->hasMany(ArticleComment::className(), ['profile_id' => 'id']);
    }

    /**
     * @return array
     */
    public function getUrl()
    {
        return ['/profile/view', 'id' => $this->id, 'slug' => $this->slug];
    }

    /**
     * @return string
     */
    public function getLink()
    {
        return Html::a($this->name, $this->getUrl(), ['title' => $this->name]);
    }

    /**
     * @param array $config
     * @return object
     * @throws \yii\base\InvalidConfigException
     */
    public function getGravatar($config = [])
    {
        $config = ArrayHelper::merge([
            'class' => Gravatar::className(),
            'email' => $this->email
        ], $config);
        return Yii::createObject($config);
    }

    /**
     * @return object|string
     */
    public function getAvatarUrl($config = [])
    {
        if (!empty($this->picture)) {
            return $this->picture;
        } else {
            return $this->gravatar($config);
        }
    }

    /**
     * @return string
     */
    public function getAvatarImage($htmlOptions = [], $config = [])
    {
        return Html::img($this->getAvatarUrl($config), $htmlOptions);
    }


    /**
     * @inheritdoc
     */
    public function beforeValidate()
    {
        if (parent::beforeValidate()) {
            $this->pictureUpload = UploadedFile::getInstance($this, 'pictureUpload');
            return true;
        }
        return false;
    }
    
}
