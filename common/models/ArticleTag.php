<?php

namespace common\models;

use Yii;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "article_tag".
 *
 * @property integer $id
 * @property string $name
 * @property string $slug
 * @property integer $frequency
 * @property integer $created_at
 * @property integer $updated_at
 */
class ArticleTag extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
            [
                'class' => SluggableBehavior::className(),
                'attribute' => 'name'
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'article_tag';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['name', 'trim'],
            ['name', 'required'],
            [['frequency', 'created_at', 'updated_at'], 'integer'],
            [['name', 'slug'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'slug' => 'Slug',
            'frequency' => 'Frequency',
            'created_at' => 'Created At',
            'updated_at' => 'Update At',
        ];
    }

    /**
     * @param string $keywords
     * @param int $limit
     * @return array|\yii\db\ActiveRecord[]
     */
    public function suggestTags($keywords, $limit = 20)
    {
        return self::find()->select(['name', 'frequency'])
            ->where(['like', 'name', ':keywords'])
            ->orderBy(['frequency' => SORT_DESC, 'updated_at' => SORT_DESC])
            ->limit($limit)
            ->params([':keywords' => '%' . strtr($keywords, array('%' => '\%', '_' => '\_', '\\' => '\\\\')) . '%'])
            ->all();
    }

    /**
     * @param string $str
     * @return array
     */
    public static function str2tags($str)
    {
        return preg_split('/\s*,\s*/', trim($str), -1, PREG_SPLIT_NO_EMPTY);
    }

    /**
     * @param array $tags
     * @return string
     */
    public static function tags2str($tags)
    {
        return implode(',', $tags);
    }

    /**
     * @param string $oldTags
     * @param string $newTags
     * @param $articleId
     */
    public function syncTags($oldTags, $newTags, $articleId)
    {
        $addTags = array_values(array_diff(self::str2tags($newTags), self::str2tags($oldTags)));
        $removeTags = array_values(array_diff(self::str2tags($oldTags), self::str2tags($newTags)));
        foreach ($addTags as $addTag) {
            self::addTag($addTag, $articleId);
        }
        foreach ($removeTags as $removeTag) {
            self::removeTag($removeTag, $articleId);
        }
    }

    /**
     * @param array $tags
     * @param int $articleId
     */
    public function addTags($tags, $articleId)
    {
        foreach ($tags as $name) {
            self::addTag($name, $articleId);
        }
    }

    /**
     * @param string $name
     * @param int $articleId
     * @return bool
     */
    public function addTag($name, $articleId)
    {
        if (is_integer($id = self::add($name))) {
            return (new Article2tag(['article_id' => $articleId, 'tag_id' => $id]))->save();
        }

        return false;
    }

    /**
     * @param string $name
     * @return bool|int The id of tag
     */
    public function add($name)
    {
        if ($model = self::findOne(['name' => $name])) {
            $model->updateCounters(['frequency' => 1]);
        } else {
            $model = new self(['name' => $name]);
            $model->save();
        }
        if (($model instanceof self) && !$model->isNewRecord) {
            return $model->id;
        }
        return false;
    }

    /**
     * @param string $name
     * @param int $articleId
     * @return bool|int The id of tag
     */
    public function removeTag($name, $articleId)
    {
        if (is_integer($id = self::remove($name))) {
            return Article2tag::deleteAll(['article_id' => $articleId, 'tag_id' => $id]);
        }
        return false;
    }

    /**
     * @param string $name
     * @return bool
     * @throws \Exception
     */
    public function remove($name)
    {
        if ($model = self::findOne(['name' => $name])) {
            if ($model->frequency > 1) {
                if ($model->updateCounters(['frequency' => -1])) {
                    return $model->id;
                }
            } else {
                $id = $model->id;
                if ($model->delete()) {
                    return $id;
                }
            }
        }
        return false;
    }

}