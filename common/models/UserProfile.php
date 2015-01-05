<?php

namespace common\models;

use common\components\Gravatar;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\timeago\TimeAgo;
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
            'email' => Yii::t('app', 'Public Email'),
            'name' => Yii::t('app', 'Name'),
            'gender' => Yii::t('app', 'Gender'),
            'birthday' => Yii::t('app', 'Birthday'),
            'website' => Yii::t('app', 'Url'),
            'locale' => Yii::t('app', 'Locale'),
            'location' => Yii::t('app', 'Location'),
            'company' => Yii::t('app', 'Company'),
            'bio' => Yii::t('app', 'About'),
            'picture' => Yii::t('app', 'Avatar'),
        ];
    }

    public function getInlineInfo()
    {
        if (!empty($this->company)) {
            return Html::tag('i', null, ['class' => 'psi-']) . '&nbsp; ' . $this->company;
        } elseif (!empty($this->location)) {
            return Html::tag('i', null, ['class' => 'psi-location']) . '&nbsp; ' . $this->location;
        } else {
            return Html::tag('i', null, ['class' => 'fa fa-clock-o']) . '&nbsp; Joined ' . TimeAgo::widget(['timestamp' => $this->created_at]);
        }
    }
}
