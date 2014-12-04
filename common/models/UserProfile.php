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
 * @property integer $id
 * @property integer $user_id
 * @property integer $priority
 * @property string $email
 * @property string $first_name
 * @property string $last_name
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
 * @property integer $verified
 * @property integer $status
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
    const STATUS_ACTIVE = 1;
    const STATUS_DELETE = 0;
    const PRIORITY_PRIMARY = 1;
    const PRIORITY_NONE = 0;


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
            NameableBehavior::className(),
            [
                'class' => SluggableBehavior::className(),
                'attribute' => ['first_name', 'last_name']
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
            [['first_name', 'last_name'], 'required'],
            [['user_id', 'created_at', 'updated_at', 'verified', 'status'], 'integer'],
            [['username', 'gender', 'bio'], 'string'],
            [['email', 'name', 'slug', 'website', 'locale', 'location', 'company', 'picture'], 'string', 'max' => 255],
            [['first_name', 'last_name'], 'string', 'max' => 100],
            ['email', 'email'],
            ['website', 'url'],
            ['picture', 'url'],
            ['pictureUpload', 'file', 'mimeTypes' => ['image/jpeg', 'image/gif', 'image/png', 'image/bmp']],
            ['birthday', 'date', 'format' => 'yyyy-MM-dd'],
            ['priority', 'default', 'value' => self::PRIORITY_NONE],
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
            'user_id' => Yii::t('app', 'User ID'),
            'email' => Yii::t('app', 'Email'),
            'first_name' => Yii::t('app', 'First Name'),
            'last_name' => Yii::t('app', 'Last Name'),
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
            'verified' => Yii::t('app', 'Verified'),
            'status' => Yii::t('app', 'Status'),
        ];
    }

    public function getInlineInfo()
    {
        if (!empty($this->company)) {
            return Html::tag('i', null, ['class' => 'fa fa-building-o']) . '&nbsp; ' . $this->company;
        } elseif (!empty($this->location)) {
            return Html::tag('i', null, ['class' => 'fa fa-map-marker']) . '&nbsp; ' . $this->location;
        } else {
            return Html::tag('i', null, ['class' => 'fa fa-clock-o']) . '&nbsp; joined '.TimeAgo::widget(['timestamp' => $this->created_at]);
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

    public function primary()
    {
        $this->updateAttributes(['priority' => self::PRIORITY_PRIMARY]);
        $this->updateAll(['priority' => self::PRIORITY_NONE], ['AND', ['user_id' => Yii::$app->user->id], ['!=', 'id', $this->id]]);
        Flash::alert(Flash::ALERT_SUCCESS, 'Your primary profile was established');
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
    public function getAvatarUrl()
    {
        if (!empty($this->picture)) {
            return $this->picture;
        } else {
            return $this->getGravatar();
        }
    }

    /**
     * @return string
     */
    public function getAvatarImage($options = ['class' => 'img-thumbnail'])
    {
        return Html::img($this->getAvatarUrl(), $options);
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

    /**
     * @inheritdoc
     */
    public function afterValidate()
    {
        if ($this->pictureUpload instanceof UploadedFile) {
            $fileName = substr(uniqid(md5(rand()), true), 0, 10) . '_' . Inflector::slug($this->pictureUpload->baseName) . '.' . $this->pictureUpload->extension;
            $thumbnailName = substr(uniqid(md5(rand()), true), 0, 10) . '.' . $this->pictureUpload->extension;
            $ownerFolder = Yii::$app->user->isGuest ? 'guest' : Yii::$app->user->id;
            $path = Yii::getAlias('@webroot/uploads/') . DIRECTORY_SEPARATOR . $ownerFolder;
            FileHelper::createDirectory($path);
            if ($this->pictureUpload->saveAs($path . DIRECTORY_SEPARATOR . $fileName)) {
                (new Image())->thumbnail($path . DIRECTORY_SEPARATOR . $fileName, 200, 200)->save($path . DIRECTORY_SEPARATOR . $thumbnailName);
                $this->picture = Yii::$app->request->hostInfo . '/uploads/' . $ownerFolder . '/' . $thumbnailName;
                $this->pictureUpload->reset();
            }
        }
    }


}
