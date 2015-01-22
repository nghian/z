<?php

namespace frontend\models;

use yii\base\Model;
use yii\helpers\Url;
use yii\imagine\Image;
use Imagine\Image\Box;
use Imagine\Image\Point;
use yii\web\UploadedFile;

use Yii;

class ChangeAvatar extends Model
{
    public $avatarDir = '@webroot/images/avatars/pictures';
    public $avatarUrl = '@web/images/avatars/pictures';
    public $file;
    public $x;
    public $y;
    public $width;
    public $height;
    private $_fileName;

    public function rules()
    {
        return [
            [['x', 'y', 'width', 'height'], 'trim'],
            [['x', 'y', 'width', 'height'], 'double'],
            ['file', 'image', 'minWidth' => 100, 'minHeight' => 100, 'extensions' => ['jpg', 'jpeg', 'png', 'gif']],
        ];
    }

    public function attributeLabels()
    {
        return ['file' => 'Local Upload'];
    }

    public function beforeValidate()
    {
        if (parent::beforeValidate()) {
            $this->file = UploadedFile::getInstance($this, 'file');
            return true;
        }
        return false;
    }


    public function change()
    {
        if ($this->validate()) {
            $imagine = new Image();
            $imagine = $imagine->getImagine()->open($this->file->tempName)->crop(new Point($this->x, $this->y), new Box($this->width, $this->height));
            if ($this->width > 400) {
                $imagine = $imagine->resize(new Box(400, 400));
            }
            if ($imagine->save(Yii::getAlias($this->avatarDir . DIRECTORY_SEPARATOR . $this->getFileName()))) {
                if (Yii::$app->user->identity->userProfile->updateAttributes(['picture' => $this->getResourceUrl()])) {
                    return true;
                }
            }
        }
        return false;
    }

    public function getFileName()
    {
        if (!$this->_fileName) {
            $this->_fileName = Yii::$app->user->id . '_' . substr(md5(uniqid()), 0, 5) . '.' . end(explode('/', $this->file->type));
        }
        return $this->_fileName;
    }

    public function getResourceUrl()
    {
        return Url::to(Yii::getAlias($this->avatarUrl) . '/' . $this->getFileName(), true);
    }

    public function getUrl()
    {
        return Url::to(['user/picture', 'username' => Yii::$app->user->identity->userLogin->username, 's' => 214], true);
    }

}