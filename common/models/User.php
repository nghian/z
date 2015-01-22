<?php

namespace common\models;

use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\IdentityInterface;
use yii\db\ActiveRecord;
use Yii;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $role
 * @property string $auth_key
 * @property integer $last_login
 * @property integer $created_at
 * @property integer $updated_at
 *
 * Properties for get relations
 * @property UserEmail[] $userEmails
 * @property UserEmail $userEmail
 * @property UserLogin $userLogin
 * @property UserProfile $userProfile
 * @property UserProfile[] $userProfiles
 * @property User[] $userFollowers
 * @property User[] $userFollowing
 * @property User[] $userFriends
 * @property ArticleItem[] $articleItems
 *
 * Shortcut
 * @property array $url
 * @property string $link
 * @property null|string $name
 * @property null|string $email
 * @property string $avatarImage
 * @property string|object $avatarUrl
 * @property object $gravatar
 * @property bool isFollowed
 * @property bool isFriend
 * @property bool isRequestFriend
 * @property bool isConfirmFriend
 * @property string followButton
 * @property string friendButton
 */
class User extends ActiveRecord implements IdentityInterface
{
    const ROLE_REGISTER = 1;
    const ROLE_EDITOR = 2;
    const ROLE_MANAGER = 3;
    const ROLE_ADMIN = 4;
    const ROLE_BANNED = 5;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className()
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['role', 'default', 'value' => self::ROLE_REGISTER],
            ['role', 'in', 'range' => [
                self::ROLE_REGISTER,
                self::ROLE_EDITOR,
                self::ROLE_MANAGER,
                self::ROLE_ADMIN,
                self::ROLE_BANNED
            ]],
            [['auth_key'], 'string', 'max' => 60]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'role' => 'Role',
            'auth_key' => 'Auth Key',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @param int $role
     * @return bool|mixed
     */
    public static function getRoleName($role)
    {
        $roles = [
            self::ROLE_ADMIN => 'admin',
            self::ROLE_MANAGER => 'manager',
            self::ROLE_EDITOR => 'editor',
            self::ROLE_REGISTER => 'register',
            self::ROLE_BANNED => 'banned'
        ];
        if (ArrayHelper::keyExists($role, $roles)) {
            return ArrayHelper::getValue($roles, $role);
        }
        return false;
    }

    public function getUserEmail()
    {
        return $this->hasOne(UserEmail::className(), ['user_id' => 'id'])
            ->andWhere([
                'priority' => UserEmail::PRIORITY_PRIMARY,
            ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserEmails()
    {
        return $this->hasMany(UserEmail::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserLogin()
    {
        return $this->hasOne(UserLogin::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserProfile()
    {
        return $this->hasOne(UserProfile::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserProfiles()
    {
        return $this->hasMany(UserProfile::className(), ['user_id' => 'id']);
    }

    public function getUserFollowers()
    {
        return $this->hasMany(self::className(), ['id' => 'user_id'])
            ->viaTable(UserFollow::tableName(), ['follow_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserFollowing()
    {
        return $this->hasMany(self::className(), ['id' => 'follow_id'])
            ->viaTable(UserFollow::tableName(), ['user_id' => 'id']);
    }

    /**
     * @return bool
     */
    public function getIsFollowed()
    {
        if (!Yii::$app->user->isGuest) {
            return UserFollow::findOne(['follow_id' => $this->id, 'user_id' => Yii::$app->user->id]) !== null;
        }
        return false;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserFriends()
    {
        return $this->hasMany(self::className(), ['id' => 'user_id'])->viaTable(UserFriend::tableName(), ['friend_id' => 'id'], function ($query) {
            /* @var $query ActiveQuery */
            return $query->andWhere(['status' => UserFriend::STATUS_ACTIVE]);
        });
    }

    /**
     * @return bool
     */
    public function getIsFriend()
    {
        if (!Yii::$app->user->isGuest) {
            return UserFriend::findOne(['friend_id' => $this->id, 'user_id' => Yii::$app->user->id, 'status' => UserFriend::STATUS_ACTIVE]) !== null;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function getIsRequestFriend()
    {
        if (!Yii::$app->user->isGuest) {
            return UserFriend::findOne(['friend_id' => $this->id, 'user_id' => Yii::$app->user->id, 'status' => UserFriend::STATUS_CONFIRM]) !== null;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function getIsConfirmFriend()
    {
        if (!Yii::$app->user->isGuest) {
            return UserFriend::findOne(['user_id' => $this->id, 'friend_id' => Yii::$app->user->id, 'status' => UserFriend::STATUS_CONFIRM]) !== null;
        }
        return false;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArticleItems()
    {
        return $this->hasMany(ArticleItem::className(), ['user_id' => 'id'])
            ->andWhere(['status' => ArticleItem::STATUS_PUBLISHED])
            ->andWhere(['<=', 'published_at', new Expression('UNIX_TIMESTAMP()')]);
    }

    /**
     * @param array $options
     * @return string
     */
    public function getFollowButton($options = [])
    {
        $options = array_merge($options, ['class' => 'btn btn-sm btn-default']);
        if (Yii::$app->user->isGuest) {
            return Html::a(Html::tag('span', null, ['class' => '']) . ' Follow', [Yii::$app->user->loginUrl, 'ref' => Yii::$app->request->absoluteUrl], $options);
        } else {
            $options = array_merge($options, [
                'data-toggle' => 'ajax',
                'data-type' => 'POST',
                'data-data-type' => 'json',
                'data-data' => Json::encode(['id' => $this->id]),
                'data-cache' => 'false',
            ]);
            if ($this->isFollowed) {
                return Html::button(Html::tag('span', null, ['class' => 'psi-eye-minus']) . ' Unfollow', array_merge($options, [
                    'data-alert' => 'Are you sure unfollow this person',
                    'data-url' => Url::to(['follow/un'])
                ]));
            } else {
                return Html::button(Html::tag('span', null, ['class' => 'psi-eye-plus']) . ' Follow', array_merge($options, [
                    'data-url' => Url::to(['follow/add'])
                ]));
            }
        }
    }

    /**
     * @param array $options
     * @return string
     */
    public function getFriendButton($options = ['class' => 'btn btn-sm btn-default'])
    {
        $options = array_merge($options, ['class' => 'btn btn-sm btn-default']);

        if (Yii::$app->user->isGuest) {
            return Html::a(Html::tag('span', null, ['class' => 'psi-add']) . ' Add friend', [Yii::$app->user->loginUrl, 'ref' => Yii::$app->request->absoluteUrl], $options);
        } else {
            $options = array_merge($options, [
                'data-toggle' => 'ajax',
                'data-type' => 'POST',
                'data-cache' => 'false',
                'data-data-type' => 'json',
                'data-data' => Json::encode(['id' => $this->id]),
            ]);
            if ($this->isFriend) {
                return Html::button(Html::tag('span', null, ['class' => 'psi-cancel-c']) . ' Unfriend', array_merge($options, [
                    'data-alert' => 'Are you sure unfriend this person',
                    'data-url' => Url::to(['friend/un'])
                ]));
            } elseif ($this->isRequestFriend) {
                $options = array_merge($options, [
                    'data-url' => Url::to(['friend/cancel'])
                ]);
                return Html::button(Html::tag('span', null, ['class' => 'psi-cancel-o']) . ' Cancel friend', array_merge($options, [
                    'data-url' => Url::to(['friend/cancel'])
                ]));
            } elseif ($this->isConfirmFriend) {
                return Html::button(Html::tag('span', null, ['class' => 'psi-check']) . ' Confirm friend', array_merge($options, [
                    'data-url' => Url::to(['friend/confirm'])
                ]));
            } else {
                return Html::button(Html::tag('span', null, ['class' => 'psi-add']) . ' Add friend', array_merge($options, [
                    'data-url' => Url::to(['friend/add'])
                ]));
            }
        }
    }

    /**
     * @return array
     */
    public function getUrl()
    {
        return ['/user/article', 'username' => $this->userLogin->username];
    }

    /**
     * @param array $options
     * @return string
     */
    public function getLink($options = [])
    {
        $options['title'] = ArrayHelper::getValue($options, 'title', $this->getName());
        return Html::a($this->getName(), $this->getUrl(), $options);
    }

    /**
     * @param array $options
     * @return string
     */
    public function getAvatarUrl($options = [])
    {
        $params = ['user/picture', 'username' => $this->userLogin->username];
        if (!empty($options)) {
            $params = array_merge($params, $options);
        }
        return Url::to($params);
    }

    /**
     * @param array $options
     * @param array $avatarOptions
     * @return string
     */
    public function getAvatarImage($options = [], $avatarOptions = [])
    {
        $options['alt'] = ArrayHelper::getValue($options, 'alt', $this->name);
        $options['title'] = ArrayHelper::getValue($options, 'title', $this->name);
        $options['class'] = ArrayHelper::getValue($options, 'class', 'img-circle');
        return Html::img($this->getAvatarUrl($avatarOptions), $options);
    }

    /**
     * @return null|string
     */
    public function getName()
    {
        if (!is_null($this->userProfile)) {
            return $this->userProfile->name;
        }
        return null;
    }

    /**
     * @return null|string
     */
    public function getEmail()
    {
        if (!is_null($this->userEmail)) {
            return $this->userEmail->email;
        }
        return null;
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->auth_key == $authKey;
    }

    /**
     * Generates new auth token
     */
    public function setAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function validateResetToken($token)
    {
        if (empty($token)) {
            return false;
        }
        $expire = Yii::$app->params['user.resetTokenExpire'];
        $parts = explode('_', $token);
        $timestamp = (int)end($parts);
        return $timestamp + $expire >= time();
    }

    /**
     * @param bool $remember
     * @return bool
     */
    public function login($remember = true)
    {
        if (!$this->isNewRecord) {
            $this->setAuthKey();
            $this->save();
            $authManager = Yii::$app->authManager;
            $role = $authManager->getRole(self::getRoleName($this->role));
            if (is_null($authManager->getAssignment($role->name, $this->id))) {
                $authManager->assign($role, $this->id);
            }
            return Yii::$app->user->login($this, $remember ? 3600 * 24 * 30 : 0);
        }
        return false;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        $authManager = Yii::$app->authManager;
        $role = $authManager->getRole(self::getRoleName($this->role));
        if (!$insert) {
            if (ArrayHelper::keyExists('role', $changedAttributes)) {
                $oldRole = $authManager->getRole(self::getRoleName($changedAttributes['role']));
                $authManager->revoke($oldRole, $this->id);
            }
        }
        if (is_null($authManager->getAssignment($role->name, $this->id))) {
            $authManager->assign($role, $this->id);
        }
    }
}
